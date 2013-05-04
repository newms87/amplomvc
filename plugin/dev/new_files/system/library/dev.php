<?php
class Dev{
	private $registry;
	
	function __construct($registry){
		$this->registry = $registry;
	}
	
	public function __get($key){
		return $this->registry->get($key);
	}
	
	public function sync_request_table($table, $url, $username, $password){
	}
}