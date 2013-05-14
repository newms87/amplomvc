<?php 
class ControllerTotalReward extends Controller { 
	 
	 
	public function index() { 
		$this->template->load('total/reward');

		$this->load->language('total/reward');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('reward', $_POST);
		
			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('extension/total'));
		}
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_total'), $this->url->link('extension/total'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('total/reward'));

		$this->data['action'] = $this->url->link('total/reward');
		
		$this->data['cancel'] = $this->url->link('extension/total');

		if (isset($_POST['reward_status'])) {
			$this->data['reward_status'] = $_POST['reward_status'];
		} else {
			$this->data['reward_status'] = $this->config->get('reward_status');
		}

		if (isset($_POST['reward_sort_order'])) {
			$this->data['reward_sort_order'] = $_POST['reward_sort_order'];
		} else {
			$this->data['reward_sort_order'] = $this->config->get('reward_sort_order');
		}
																		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'total/reward')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}