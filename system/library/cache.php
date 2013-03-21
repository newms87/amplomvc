<?php
class Cache { 
	private $expire;
   private $ignore_list = array();
   private $loaded = array();

  	public function __construct($registry) {
  	   $this->expire = CACHE_FILE_EXPIRATION;
      
		if(!is_dir(DIR_CACHE)){
         $mode = octdec($registry->get('config')->get('config_default_dir_mode'));
         mkdir(DIR_CACHE, $mode,true);
         chmod(DIR_CACHE, $mode);
      }
		
		$files = glob(DIR_CACHE . '*.cache');
		
		if ($files) {
			foreach ($files as $file) {
				$time = strstr(basename($file), '.',true);
            
   			if ($time < time()) {
					if (file_exists($file)) {
						@unlink($file);
					}
   			}
    		}
		}
  	}
   
   public function get_cache_time($key){
      if(!isset($this->loaded[$key])){
         $this->get($key);
      }
      
      return (int) preg_replace(array('/\..*$/','/.*\//'),'',$this->loaded[$key]['file']) - $this->expire;
   }
   
	public function get($key) {
	   if(isset($this->loaded[$key])){
	      return $this->loaded[$key]['data'];
      }
      
	   foreach($this->ignore_list as $ignore){
         if(preg_match("/^$ignore/", $key) > 0) return;
      }
      
		$files = glob(DIR_CACHE . '*.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.cache');
      
      if ($files) {
			$this->loaded[$key]['data'] = unserialize(file_get_contents($files[0]));
         $this->loaded[$key]['file'] = $files[0];
         
         return $this->loaded[$key]['data'];
		}
	}

  	public function set($key, $value) {
  	   foreach($this->ignore_list as $ignore){
  	      if(preg_match("/^$ignore/", $key) > 0) return;
      }
      
    	$this->delete($key);
      
		$file = DIR_CACHE . (time() + $this->expire) .'.'  . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.cache';
    	
		$handle = fopen($file, 'w');

    	fwrite($handle, serialize($value));
		
    	fclose($handle);
  	}
   
  	public function delete($key) {
		$files = glob(DIR_CACHE . '*.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '*.cache');
		if ($files) {
    		foreach ($files as $file) {
      			if (file_exists($file)) {
					unlink($file);
				}
    		}
		}
  	}
   
   public function ignore($key){
      $key = trim($key);
      if($key)
         $this->ignore_list[$key] = $key;
   }
}
