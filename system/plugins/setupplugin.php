<?php
abstract class SetupPlugin {
	private $registry;
	
	function __construct($registry){
		$this->registry = $registry;
	}
	
	public function __get($key){
		return $this->registry->get($key);
	}
	
	public function install(&$controller_adapters, &$db_requests){
		//Installation Code goes here
	}
	
	public function uninstall($keep_data){
		//Uninstall code goes here
	}
	
	public function update($version){
		//Update code goes here
	}
}