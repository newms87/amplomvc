<?php
class ControllerPluginCms extends Controller {
	public function index() {
		echo 'index';
	}
	
	public function cms_header(){
		if($this->config->get('config_store_id') != 2)return;
		
		$this->data['giveaway'] = $this->image->get('data/dogear_giveaway.gif');
	}
	
	public function footer(){
		echo 'footer';
	}
}