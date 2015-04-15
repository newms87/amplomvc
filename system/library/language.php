<?php

class Language extends Library
{
	protected
		$language_id,
		$code,
		$info,
		$languages = array();

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
		'datetime_format_long'   => 'M d, Y H:i:s A',
		'datetime_format_full'   => 'D, d M, Y H:i:s',
		'decimal_point'          => '.',
		'thousand_point'         => ',',
		'status'                 => 1,
	);

	public function __construct($language_id = null, $set_session = true)
	{
		parent::__construct();

		$this->loadLanguages();

		if (empty($language_id)) {
			$this->resolve();
		} else {
			$this->setLanguage($language_id);
		}

		if ($set_session) {
			$this->session->set('language_code', $this->code);

			//Set as default language for this user for 30 days
			set_cookie('language_code', $this->code, 3600 * 24 * 30);

			set_option('config_language_id', $this->language_id);
		}
	}

	public function id()
	{
		return $this->language_id;
	}

	public function getLanguage($language_id)
	{
		return isset($this->languages[$language_id]) ? $this->languages[$language_id] : $this->defaults;
	}

	protected function loadLanguages()
	{
		$this->languages = cache('language.active');

		if (!$this->languages) {
			$this->languages = $this->Model_Localisation_Language->getRecords(null, array('status' => 1), array('index' => 'language_id'));

			foreach ($this->languages as &$language) {
				foreach ($this->defaults as $key => $value) {
					if (empty($language[$key])) {
						$language[$key] = $value;
					}
				}
			}
			unset($language);

			cache('language.active', $this->languages);
		}
	}

	public function setLanguage($language)
	{
		if (!is_array($language)) {
			$language = $this->getLanguage((int)$language);
		} else {
			foreach ($this->defaults as $key => $default) {
				if (empty($language[$key])) {
					$language[$key] = $default;
				}
			}
		}

		$this->language_id = $language['language_id'];
		$this->code        = $language['code'];
		$this->info        = $language;
	}

	public function info($key = null, $default = null, $language_id = null)
	{
		$language = !empty($language_id) ? $this->getLanguage($language_id) : $this->info;

		if ($key) {
			return isset($language[$key]) ? $language[$key] : $default;
		}

		return $language;
	}

	public function setInfo($key, $value)
	{
		$this->info[$key] = $value;
	}

	private function resolve()
	{
		$code = _get('language_code', _session('language_code', _cookie('language_code', false)));

		if ($code) {
			$language = null;

			foreach ($this->languages as $lang) {
				if ($lang['code'] === $code) {
					$language = $lang;
				}
			}
		}

		//Language requested was invalid, attempt to detect language or revert to default
		if (empty($language)) {
			$language = $this->detect();

			//Last Resort Load English or any language
			if (!$language) {
				$query = "SELECT * FROM {$this->t['language']} WHERE status = '1'" .
					" ORDER BY CASE" .
					" WHEN `code` = '" . $this->escape(option('config_language')) . "' THEN 2" .
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

			$this->languages[$language['language_id']] = $language + $this->defaults;
		}
	}

	private function detect()
	{
		//Detect Language From Browser
		if (empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			return false;
		}

		foreach ($this->languages as &$language) {
			$language[$language['code']] = & $language;
		}
		unset($language);

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

		if (option('config_use_macro_languages')) {
			//Resolve Macro Language codes
			foreach ($macro as $code => $q) {
				if (isset($languages[$code])) {
					return $languages[$code];
				}
			}
		} else {
			//Resolve 2 letter language code
			arsort($alpha2);

			foreach ($alpha2 as $code => $q) {
				if (isset($languages[$code])) {
					return $languages[$code];
				}
			}

			//Resolve 3 letter language code
			arsort($alpha3);

			foreach ($alpha3 as $code => $q) {
				if (isset($languages[$code])) {
					return $languages[$code];
				}
			}
		}

		return false;
	}
}
