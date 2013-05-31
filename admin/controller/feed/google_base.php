<?php
class Admin_Controller_Feed_GoogleBase extends Controller 
{
	
	
	public function index()
	{
		$this->template->load('feed/google_base');

		$this->load->language('feed/google_base');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->Model_Setting_Setting->editSetting('google_base', $_POST);
			
			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('extension/feed'));
		}

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_feed'), $this->url->link('extension/feed'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('feed/google_base'));

		$this->data['action'] = $this->url->link('feed/google_base');
		
		$this->data['cancel'] = $this->url->link('extension/feed');
		
		if (isset($_POST['google_base_status'])) {
			$this->data['google_base_status'] = $_POST['google_base_status'];
		} else {
			$this->data['google_base_status'] = $this->config->get('google_base_status');
		}
		
		$this->data['data_feed'] = SITE_URL . 'index.php?route=feed/google_base';

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'feed/google_base')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		return $this->error ? false : true;
	}
}