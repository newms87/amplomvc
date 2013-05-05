<?php
class Log {
	private $filename;
	private $store_name;
   
	public function __construct($filename, $store_name='Default') {
		$this->filename = $filename;
      $this->store_name = $store_name;
		if(!is_dir(DIR_LOGS)){
			mkdir(DIR_LOGS, 0777, true);
		}
	}
	
	public function write($message) {
		$file = DIR_LOGS . $this->filename;
		
		$handle = fopen($file, 'a+'); 
		
      $log = array('m'=>str_replace(array("\r\n","\r","\n"),'<br />', $message),
                   'd'=>date('Y-m-d G:i:s'),
                   'u'=>preg_replace("/\?.*/","",$_SERVER['REQUEST_URI']),
                   'q'=>$_SERVER['QUERY_STRING'],
                   's'=>$this->store_name,
                   'ip'=>$_SERVER['REMOTE_ADDR'],
                   'a'=> isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''
                  ); 
		fwrite($handle, serialize($log) . "\n");
			
		fclose($handle); 
	}
}