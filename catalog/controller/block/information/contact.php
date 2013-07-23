<?php
class Catalog_Controller_Block_Information_Contact extends Controller
{
	public function index($settings)
	{
		//Template and Language
		$this->template->load('block/information/contact');
		$this->language->load('block/information/contact');
		
		//The Data
		$settings['contact_info'] = html_entity_decode($settings['contact_info']);
		
		$this->data = $settings;
		
		//Render
		$this->render();
	}
}
