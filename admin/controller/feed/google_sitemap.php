<?php 
class ControllerFeedGoogleSitemap extends Controller {
	 
	
	public function index() {
		$this->template->load('feed/google_sitemap');

		$this->load->language('feed/google_sitemap');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('google_sitemap', $_POST);				
			
			$this->message->add('success', $this->_('text_success'));

			$this->redirect($this->url->link('extension/feed'));
		}

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_feed'), $this->url->link('extension/feed'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('feed/google_sitemap'));

		$this->data['action'] = $this->url->link('feed/google_sitemap');
		
		$this->data['cancel'] = $this->url->link('extension/feed');
		
		if (isset($_POST['google_sitemap_status'])) {
			$this->data['google_sitemap_status'] = $_POST['google_sitemap_status'];
		} else {
			$this->data['google_sitemap_status'] = $this->config->get('google_sitemap_status');
		}
		
		$this->data['data_feed'] = HTTP_CATALOG . 'index.php?route=feed/google_sitemap';

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	} 
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'feed/google_sitemap')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}	
}