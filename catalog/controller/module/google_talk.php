<?php
class ControllerModuleGoogleTalk extends Controller 
{
	protected function index()
	{
		$this->template->load('module/google_talk');

		$this->language->load('module/google_talk');

		if ($this->url->is_ssl()) {
			$this->data['base'] = str_replace('http', 'https', html_entity_decode($this->config->get('google_talk_code')));
		} else {
			$this->data['base'] = html_entity_decode($this->config->get('google_talk_code'));
		}

		$this->render();
	}
}