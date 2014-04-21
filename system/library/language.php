<?php
class Language extends Library
{
	private $language_id;
	private $code;
	private $info;
	private $loaded = array();

	public $defaults = array(
		'id'                     => 0,
		'code'                   => 'en',
		'name'                   => 'English',
		'locale'                 => "en,en-US,en_US.UTF-8,en_US,en-gb,english",
		'image'                  => 'gb.png',
		'direction'              => 'ltr',
		'date_format_short'      => 'm/d/Y',
		'date_format_medium'     => 'M d, Y',
		'date_format_long'       => 'l dS F Y',
		'time_format'            => 'h:i:s A',
		'datetime_format'        => 'Y-m-d H:i:s',
		'datetime_format_medium' => 'M d, Y H:i A',
		'datetime_format_long'   => 'M d, Y H:i:s A A',
		'datetime_format_full'   => 'D, d M, Y H:i:s',
		'decimal_point'          => '.',
		'thousand_point'         => ',',
		'status'                 => 1,
	);

	public function __construct($registry, $language_id = null, $set_session = true)
	{
		parent::__construct($registry);

		if (empty($language_id)) {
			$this->resolve();
		} else {
			$this->setLanguage($language_id);
		}

		if ($set_session) {
			$this->session->set('language_code', $this->code);

			//Set as default language for this user for 30 days
			$this->session->setCookie('language_code', $this->code, 60 * 60 * 24 * 30);

			$this->config->set('config_language_id', $this->language_id);
		}
	}

	public function id()
	{
		return $this->language_id;
	}

	public function getLanguage($language_id)
	{
		if (!isset($this->loaded[$language_id])) {
			$language = $this->queryRow("SELECT * FROM " . DB_PREFIX . "language WHERE language_id = " . (int)$language_id);

			$this->loaded[$language_id] = $language + $this->defaults;
		}

		return $this->loaded[$language_id];
	}

	public function getLanguages()
	{
		static $all_loaded = false;

		if (!$all_loaded) {
			$this->loaded = $this->queryRows("SELECT * FROM " . DB_PREFIX . "language", 'language_id');

			foreach ($this->loaded as &$loaded) {
				$loaded += $this->defaults;
			}

			$all_loaded = true;
		}

		return $this->loaded;
	}

	public function setLanguage($language)
	{
		if (!is_array($language)) {
			$language = $this->getLanguage((int)$language);
		}

		$this->language_id = $language['language_id'];
		$this->code        = $language['code'];
		$this->info        = $language;
	}

	public function info($key = null, $language_id = null)
	{
		$language = !empty($language_id) ? $this->getLanguage($language_id) : $this->info;

		if (is_null($key)) {
			return $language;
		} else {
			return isset($language[$key]) ? $language[$key] : null;
		}
	}

	private function resolve()
	{
		//Resolve Language if it was requested
		if (!empty($_GET['language_code'])) {
			$code = $_GET['language_code'];
		} elseif ($this->session->has('language_code')) {
			$code = $this->session->get('language_code');
		} elseif (!empty($_COOKIE['language_code'])) {
			$code = $_COOKIE['language_code'];
		} else {
			$code = false;
		}

		if ($code) {
			$language = $this->queryRow("SELECT * FROM " . DB_PREFIX . "language WHERE status = '1' AND `code` = '" . $this->escape($code) . "' LIMIT 1");
		}

		//Language requested was invalid, attempt to detect language or revert to default
		if (empty($language)) {
			$language = $this->detect();

			//Last Resort Load English or any language
			if (!$language) {
				$query = "SELECT * FROM " . DB_PREFIX . "language WHERE status = '1'" .
					" ORDER BY CASE" .
					" WHEN `code` = '" . $this->escape($this->config->get('config_language')) . "' THEN 2" .
					" WHEN `code` = 'en' THEN 1" .
					" ELSE 0" .
					" END DESC LIMIT 1";

				$language = $this->queryRow($query);
			}
		}

		if (!$language) {
			$this->setLanguage($this->defaults);
		} else {
			$this->setLanguage($language);

			$this->loaded[$language['language_id']] = $language + $this->defaults;
		}
	}

	private function detect()
	{
		//Detect Language From Browser
		if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$use_macro = $this->config->get('config_use_macro_languages');

			$languages = $this->cache->get('language.locales');

			if (!$languages) {
				$language_list = $this->getLanguages();

				$languages = array();

				foreach ($language_list as $language) {
					if (!$language['status']) {
						continue;
					}

					if ($use_macro) {
						$language['locales'] = explode(',', $language['locale']);
					}

					$languages[$language['code']] = $language;
				}

				$this->cache->set('language.locales', $languages);
			}

			foreach ($languages as $language) {
				$this->loaded[$language['language_id']] = $language;
			}

			$browser_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

			$alpha2 = array();
			$alpha3 = array();
			$macro  = array();

			foreach ($browser_languages as $browser_language) {
				$lq = explode(';', $browser_language);

				$l_code = $lq[0];
				$q      = isset($lq[1]) ? (float)(str_replace('q=', '', $lq[1])) : 1;

				if (strlen($l_code) === 2) {
					$alpha2[$l_code] = $q;
				} elseif (strlen($l_code) === 3) {
					$alpha3[$l_code] = $q;
				} else {
					$macro[$l_code] = $q;
				}
			}

			if ($use_macro) {
				//Resolve Macro Language codes
				foreach ($macro as $code => $q) {
					if (isset($languages[$code])) {
						return $languages[$code];
					}
				}
			} else {
				//Resolve 2 letter language code
				uasort($alpha2, function ($a, $b) { return $a > $b; });

				foreach ($alpha2 as $code => $q) {
					if (isset($languages[$code])) {
						return $languages[$code];
					}
				}

				//Resolve 3 letter language code
				uasort($alpha3, function ($a, $b) { return $a > $b; });

				foreach ($alpha3 as $code => $q) {
					if (isset($languages[$code])) {
						return $languages[$code];
					}
				}
			}
		}

		return false;
	}
}
