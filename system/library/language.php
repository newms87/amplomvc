<?php
class Language {
	private $default = 'english';
	private $directory;
   private $plugin_handler;
   private $orig_data = array();
   private $info;
   private $latest_modified_file = 0;
   
   public  $data = array();
 
	public function __construct($language, $plugin_handler) {
      $this->plugin_handler = $plugin_handler;
      
	   if(is_string($language)){
		   $this->directory = $language;
      }
      else{
         $this->info = $language;
         $this->directory = $language['directory'];
         $this->load($language['filename']);
      }
	}
	
  	public function get($key) {
		return (isset($this->data[$key]) ? $this->data[$key] : $key);
  	}
   
   public function set($key, $value){
      $this->data[$key] = $value;
   }
   
   public function get_latest_modified_file(){
      return $this->latest_modified_file;
   }
   
   public function set_latest_modified_file($time){
      if($time > $this->latest_modified_file){
         $this->latest_modified_file = $time;
      }
   }
   
   public function getInfo($key = null){
      if($key === null){
         return $this->info;
      }
      else{
         return isset($this->info[$key]) ? $this->info[$key] : null;
      }
   }
	
	public function load($filename) {
	   
		$file = DIR_LANGUAGE . $this->directory . '/' . $filename . '.php';
    	
      if(!file_exists($file)){
         $file = DIR_LANGUAGE . $this->default . '/' . $filename . '.php';
         
         if(!file_exists($file)){
            trigger_error('Error: Could not load language ' . $filename . '!');
            exit();
         }
      }
      
      $this->set_latest_modified_file(filemtime($file));
      
      $_ = array();
      
      require($file);
      
      $this->data = array_merge($this->data, $_, $this->plugin_handler->loadLanguageExtensions($filename));
      
      return $this->data;
  	}
   
   public function set_orig($key,$value){
      $this->orig_data[$key] = $value;
   }
   public function get_orig($key){
      return $this->orig_data[$key];
   }
   
   public function format($key){
      if(!isset($this->data[$key])){
         return $key;
      }
      
      if(!isset($this->orig_data[$key])){
         $this->orig_data[$key] = $this->data[$key];
      }
      
      $values = func_get_args();
      
      array_shift($values);
      
      if(!$values){
         list(,$caller) = debug_backtrace(false);
         trigger_error("Language::format requires at least 2 arguments! Called from $caller[class]::$caller[function]");
         return;
      }
      
      return $this->data[$key] = vsprintf($this->orig_data[$key],$values);
   }
   
   public function plugin($name, $filename){
      
      $file = DIR_PLUGIN . $name . '/language/' . $filename . '.php';
      
      if(!file_exists($file)){
         if(!file_exists($file)){
            trigger_error('Error: Could not load plugin language for plugin ' . $name . ': ' . $filename . '!');
         }
      }
      
      $_ = array();
      
      require($file);
      
      $this->data = array_merge($this->data, $_, $this->plugin_handler->loadLanguageExtensions($filename, $name));
      
      return $this->data;
   }
}