<?php  
class ControllerModuleGoogleTalk extends Controller {
	protected function index() {
		$this->template->load('module/google_talk');

		$this->language->load('module/google_talk');

		if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
			$this->data['code'] = str_replace('http', 'https', html_entity_decode($this->config->get('google_talk_code')));
		} else {
			$this->data['code'] = html_entity_decode($this->config->get('google_talk_code'));
		}
		






		$this->render();
	}
}