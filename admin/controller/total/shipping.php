<?php 
class ControllerTotalShipping extends Controller { 
	 
	 
	public function index() { 
		$this->template->load('total/shipping');

		$this->load->language('total/shipping');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('shipping', $_POST);
		
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('total/shipping'));

		$this->data['action'] = $this->url->link('total/shipping');
		
		$this->data['cancel'] = $this->url->link('extension/total');

		if (isset($_POST['shipping_estimator'])) {
			$this->data['shipping_estimator'] = $_POST['shipping_estimator'];
		} else {
			$this->data['shipping_estimator'] = $this->config->get('shipping_estimator');
		}
		
		if (isset($_POST['shipping_status'])) {
			$this->data['shipping_status'] = $_POST['shipping_status'];
		} else {
			$this->data['shipping_status'] = $this->config->get('shipping_status');
		}

		if (isset($_POST['shipping_sort_order'])) {
			$this->data['shipping_sort_order'] = $_POST['shipping_sort_order'];
		} else {
			$this->data['shipping_sort_order'] = $this->config->get('shipping_sort_order');
		}
																		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'total/shipping')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}