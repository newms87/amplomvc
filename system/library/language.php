<?php
class Language extends Library
{
	private $language_id;
	private $code;
	private $default = 'english';
	private $directory;
	private $orig_data = array();
	private $info;
	private $latest_modified_file = 0;
	private $last_loaded = '';
	private $saved_data = null;
	private $root = DIR_LANGUAGE;
	
	private $loaded_languages = array();
	
	public  $data = array();

	public function __construct($registry, $language_id = null, $set_active_language = true)
	{
		parent::__construct($registry);
		
		if (empty($language_id)) {
			$language = $this->resolve();
		} else {
			$language = $this->getInfo($language_id);
		}
		
		$this->language_id = $language['language_id'];
		$this->code = $language['code'];
		$this->info = $language;
		$this->directory = $language['directory'];
		
		if ($set_active_language) {
			$session->data['language_code'] = $this->code;
			
			//Set as default language for this user for 30 days
			$this->session->set_cookie('language_code', $this->code, 60 * 60 * 24 * 30);
			
			$this->config->set('config_language_id', $this->language_id);
		}
		
		$this->load($language['filename']);
	}
	
	public function setRoot($root)
	{
		$this->root = $root;
	}
	
	public function id()
	{
		return $this->language_id;
	}
	
	public function code()
	{
		return $this->code;
	}
	
  	public function get($key, $return_value = null)
  	{
		return (isset($this->data[$key]) ? $this->data[$key] : ($return_value === null ? $key : $return_value));
  	}
	
	public function getLanguages()
	{
		static $all_loaded = false;
		
		if (!$all_loaded) {
			$languages = $this->System_Model_Language->getLanguages();
			
			foreach ($languages as $language) {
				$this->loaded_languages[$language['language_id']] = $language;
			}

			$all_loaded = true;
		}
		
		return $this->loaded_languages;
	}
	
	public function set($key, $value)
	{
		$this->data[$key] = $value;
	}
	
	public function get_latest_modified_file()
	{
		return $this->latest_modified_file;
	}
	
	public function set_latest_modified_file($time)
	{
		if ($time > $this->latest_modified_file) {
			$this->latest_modified_file = $time;
		}
	}
	
	public function getInfo($key = null, $language_id = null)
	{
		if (!empty($language_id)) {
			if (!isset($loaded_languages[$language_id])) {
				$loaded_languages[$language_id] = $this->db->queryRow("SELECT * FROM " . DB_PREFIX . "language WHERE language_id = '" . (int)$language_id . "'");
			}
			
			if (is_null($key)) {
				return $loaded_languages[$language_id];
			} else {
				return isset($loaded_languages[$language_id][$key]) ? $loaded_languages[$language_id][$key] : null;
			}
		}
		
		if (is_null($key)) {
			return $this->info;
		} else {
			return isset($this->info[$key]) ? $this->info[$key] : null;
		}
	}
	
	public function load($filename)
	{
		if($this->last_loaded == $filename) return;
		
		$file = $this->root . $this->directory . '/' . $filename . '.php';
		
		if (!file_exists($file)) {
			$file = $this->root . $this->default . '/' . $filename . '.php';
			
			if (!file_exists($file)) {
				trigger_error('Error: Could not load language ' . $filename . '!');
				exit();
			}
		}
		
		$this->set_latest_modified_file(filemtime($file));
		
		$_ = _require($file, false);
		
		$this->data = $_ + $this->data;
		
		$this->last_loaded = $filename;
		
		return $this->data;
  	}
	
	public function loadTemporary($filename, $language_id = null)
	{
		if ($language_id && $language_id !== $this->language_id) {
			$directory = $this->getInfo('directory', $language_id);
		} else {
			$directory = $this->directory;
		}
		
		$file = $this->root . $directory . '/' . $filename . '.php';
		
		if (!file_exists($file)) {
			$file = $this->root . $this->default . '/' . $filename . '.php';
			
			if (!file_exists($file)) {
				trigger_error('Error: Could not load language ' . $filename . '!');
				exit();
			}
		}
		
		$_ = _require($file, false);
		
		$this->saved_data = $this->data;
		
		$this->data = $_ + $this->data;
		
		return $this->data;
  	}
	
	public function unloadTemporary()
	{
		$this->data = $this->saved_data;
	}
	
	public function fetch($filename, $directory = '')
	{
		$directory = !empty($directory) ? $directory : $this->directory;
		
		$file = $this->root . $directory . '/' . $filename . '.php';
		
		if (!file_exists($file)) {
			$file = $this->root . $this->default . '/' . $filename . '.php';
			
			if (!file_exists($file)) {
				trigger_error("Could not fetch language $filename in $directory! " . get_caller());
				exit();
			}
		}
		
		$_ = _require($file, false);
		
		return $_;
	}
	
	
	public function set_orig($key,$value)
	{
		$this->orig_data[$key] = $value;
	}
	public function get_orig($key)
	{
		return $this->orig_data[$key];
	}
	
	public function format($key)
	{
		if (!isset($this->data[$key])) {
			return $key;
		}
		
		if (!isset($this->orig_data[$key])) {
			$this->orig_data[$key] = $this->data[$key];
		}
		
		$values = func_get_args();
		
		array_shift($values);
		
		if (!$values) {
			trigger_error("Language::format requires at least 2 arguments! " . get_caller());
			return;
		}
		
		return $this->data[$key] = vsprintf($this->orig_data[$key],$values);
	}
	
	public function system($filename)
	{
		$file = DIR_SYSTEM . 'language/' . $this->directory . '/' . $filename . '.php';
		
		if (!is_file($file)) {
			$file = DIR_SYSTEM . 'language/' . $this->default . '/' . $filename . '.php';
			
			if (!is_file($file)) {
				trigger_error('Could not load system language file ' . $filename . '!');
				
				return null;
			}
		}
		
		$_ = _require($file, false);
		
		$this->data = $_ + $this->data;
		
		return $this->data;
	}
	
	public function system_fetch($filename, $directory = null)
	{
		$directory = !empty($directory) ? $directory : $this->directory;
		
		$file = DIR_SYSTEM . 'language/' . $directory . '/' . $filename . '.php';
		
		if (!file_exists($file)) {
			$file = DIR_SYSTEM . 'language/' . $this->default . '/' . $filename . '.php';
			
			if (!file_exists($file)) {
				trigger_error("The langauge file was not found for $filename in $directory! " . get_caller());
				exit();
			}
		}
		
		$_ = _require($file, false);
		
		return $_;
	}
	
	public function plugin($name, $filename)
	{
		$file = DIR_PLUGIN . $name . '/language/' . $this->directory . '/' . $filename . '.php';
		
		if (!file_exists($file)) {
			$file = DIR_PLUGIN . $name . '/language/' . $this->default . '/' . $filename . '.php';
			
			if (!file_exists($file)) {
				trigger_error("The plugin language file $file was not found for the plugin $name: $filename requested!" . get_caller());
			}
		}
		
		$_ = _require($file, false);
		
		$this->data = $_ + $this->data;
		
		return $this->data;
	}
	
	private function resolve()
	{
		//Resolve Language if it was requested
		if (!empty($_GET['language_code'])) {
			$code = $_GET['language_code'];
		} elseif (!empty($this->session->data['language_code'])) {
			$code = $this->session->data['language_code'];
		} elseif (!empty($_COOKIE['language_code'])) {
			$code = $_COOKIE['language_code'];
		} else {
			$code = false;
		}
		
		if ($code) {
			$language = $this->db->queryRow("SELECT * FROM " . DB_PREFIX . "language WHERE status = '1' AND `code` = '" . $this->db->escape($code) . "' LIMIT 1");
			
			if ($language) {
				return $language;
			}
		}
		
		//Language requested was invalid, attempt to detect language or revert to default
		if ($language = $this->detect()) {
			return $language;
		} else {
			$language = $this->db->queryRow("SELECT * FROM " . DB_PREFIX . "language WHERE status = '1' AND `code` = '" . $this->config->get('config_language') . "' LIMIT 1");
			
			if ($language) {
				return $language;
			}
		}
		
		trigger_error("Unable to resolve a language!");
		exit;
	}
		
	private function detect()
	{
		//Detect Language From Browser
		if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$use_macro = $this->config->get('config_use_macro_languages');
			
			$languages = $this->cache->get('language.locales');
			
			if (!$languages) {
				$language_list = $this->db->queryRows("SELECT * FROM " . DB_PREFIX . "language WHERE status = '1'");
				
				$languages = array();
				
				foreach ($language_list as $language) {
					if ($use_macro) {
						$language['locales'] = explode(',', $language['locale']);
					}
					
					$languages[$language['code']] = $language;
				}
				
				$this->cache->set('language.locales', $languages);
			}
			
			foreach ($languages as $language) {
				$this->loaded_languages[$language['language_id']] = $language;
			}
			
			$browser_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			
			$alpha2 = array();
			$alpha3 = array();
			$macro = array();
			
			foreach ($browser_languages as $browser_language) {
				$lq = explode(';', $browser_language);
				
				$l_code = $lq[0];
				$q = isset($lq[1]) ? (float)(str_replace('q=','',$lq[1])) : 1;
				
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
			}
			else {
				//Resolve 2 letter language code
				uasort($alpha2, function ($a,$b){ return $a > $b; } );
				
				foreach ($alpha2 as $code => $q) {
					if (isset($languages[$code])) {
						return $languages[$code];
					}
				}
				
				//Resolve 3 letter language code
				uasort($alpha3, function ($a,$b){ return $a > $b; } );
				
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