<?php
class SetupJanrain extends SetupPlugin {
 
	public function install(&$controller_adapters, &$db_requests){

	}
	
	public function update($version){
		switch($version){
			case '1.53':
			case '1.52':
			case '1.51':
			default:
				break;
		}
	}
	
	public function uninstall($keep_data = false){
	}
}