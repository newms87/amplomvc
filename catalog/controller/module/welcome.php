<?php  
class ControllerModuleWelcome extends Controller {
	protected function index($setting) {
		$this->template->load('module/welcome');

		$this->language->load('module/welcome');
		
		$this->language->format('heading_title', $this->config->get('config_name'));
		
		$this->data['message'] = html_entity_decode($setting['description'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');







		$this->render();
	}
}