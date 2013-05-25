<?php
class Request {
  	public function __construct() {
		$_GET = $this->clean($_GET);
		$_POST = $this->clean($_POST);
		$_REQUEST = $this->clean($_REQUEST);
		$_COOKIE = $this->clean($_COOKIE);
		$_SERVER = $this->clean($_SERVER);
	}
	
  	public function clean($data) {
		if(is_array($data)){
			foreach ($data as $key => $value) {
				$clean_key = htmlspecialchars(stripslashes($key), ENT_COMPAT);
				
				if($clean_key !== $key){
					unset($data[$key]);
				}
				
				$data[$clean_key] = $this->clean($value);
			}
		} else { 
			$data = htmlspecialchars(stripslashes($data), ENT_COMPAT);
		}
		
		return $data;
	}
}