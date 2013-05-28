<?php
class Theme {
	private $registry;
	private $theme;
	private $default_theme = 'default';
	
	private $settings;
	
	public function __construct(&$registry){
		$this->registry = &$registry;
		
		$this->theme = $this->config->get('config_theme');
		
		if(!$this->theme){
			$this->theme = $this->default_theme;
		}
		
		define('HTTP_THEME_STYLE', HTTP_CONTENT . 'view/theme/' . $this->theme . '/css/');
		define('HTTP_THEME_FONT', HTTP_CONTENT . 'view/theme/' . $this->theme . '/fonts/');
		define('HTTP_THEME_IMAGE', HTTP_CONTENT . 'view/theme/' . $this->theme . '/image/');
		
		if(defined("IS_ADMIN")){
			$this->settings = $this->load_admin_theme_settings();
			$this->load_theme_language();
		}
		else{
			$theme_settings_file = $this->find_file('settings.php', $this->theme);
		
			$this->settings = $this->get_theme_settings($theme_settings_file);
			
			$this->load_theme_language();
		}
	}
	
	public function __get($key){
		return $this->registry->get($key);
	}
	
	public function get_setting($key){
		if(isset($this->settings[$key])){
			return $this->settings[$key];
		}
		
		return null;
	}
	
	private function get_theme_settings($theme_settings_file, $theme = false){
		if(!$theme){
			$theme = $this->theme;
		}
		
		if(is_file($theme_settings_file)){
			$theme_settings = $this->cache->get('theme_settings.' . $theme);
		
			if(!$theme_settings || $theme_settings['mod_time'] != filemtime($theme_settings_file)){
				
				$_ = array();
				
				require_once($theme_settings_file);
				
				$theme_settings = $_;
				
				$theme_settings['mod_time'] = filemtime($theme_settings_file);
				
				$this->cache->set('theme_settings.' . $theme, $theme_settings);
			}
			
			return $theme_settings;
		}
		
		return null;
	}
	
	private function load_admin_theme_settings(){
		//We get the Themes here to validate the file modified times for caching
		$themes = $this->get_themes();
		
		$theme_settings_admin = $this->cache->get('theme_settings_admin');
			
		if(!$theme_settings_admin){
			$_ = array();
				
			require_once(DIR_THEME . $this->theme . '/settings.php');
			
			$theme_settings_admin = $_;
			
			//TODO - move this somewhere to make more easily dynamic (if we want to add other settings from the Themes)
			//We must load all the Themes' data for the admin
			$theme_settings_admin['data_positions'] = array();
			
			foreach($themes as $theme){
				$theme_settings_admin['data_positions'] = $theme['settings']['data_positions'];
			}
			
			$theme_settings_admin['themes'] = $themes;
			
			$this->cache->set('theme_settings_admin', $theme_settings_admin);
		}
		
		return $theme_settings_admin;
	}
	
	public function get_themes(){
		$theme_dir = DIR_CATALOG . 'view/theme/';
		
		$themes = $this->cache->get('themes');
		
		//invalidate all themes if one of the themes' settings has been updated
		if($themes){
			foreach($themes as $theme){
				if(filemtime($theme_dir . $theme['name'] . '/settings.php') != $theme['settings']['mod_time']){
					$themes = false;
					$this->cache->delete('theme');
					break;
				}
			}
		}
		
		if(!$themes){
			$dir_themes = glob($theme_dir . '*', GLOB_ONLYDIR);
			
			$themes = array();
			
			foreach ($dir_themes as $dir) {
				$name = basename($dir);
				
				$theme_settings_file = $theme_dir . $name . '/settings.php';
				
				$themes[$name] = array(
					'name' => $name,
					'settings' => $this->get_theme_settings($theme_settings_file, $name),
				);
			}
			
			$this->cache->set('themes', $themes);
		}
		
		return $themes;
	}
	
	private function load_theme_language(){
		//Load Positions' Language
		if(!empty($this->settings['data_positions'])){
			foreach($this->settings['data_positions'] as $key => &$position){
				$text = $this->language->get('position_' . $key);
				
				if($text != 'position_' . $key){
					$position = $text;
				}
			}
		}
	}
	
	public function find_file($file, $theme = false){
		//Search By specified theme
		if($theme){
			if (file_exists(DIR_THEME . $theme . '/' . $file)) {
				return DIR_THEME . $theme . '/' . $file;
			}
		}
		//Search By current theme
		else{
			if (file_exists(DIR_THEME . $this->theme . '/' . $file)) {
				return DIR_THEME . $this->theme . '/' . $file;
			}
			elseif (file_exists(DIR_THEME . $this->theme . '/template/' . $file)) {
				return DIR_THEME . $this->theme . '/template/' . $file;
			}
			elseif(file_exists(DIR_THEME . $this->default_theme . '/template/' . $file)) {
				return DIR_THEME . $this->default_theme . '/template/' . $file;
			}
		}
		
		//File not found
		return false;
	}
}