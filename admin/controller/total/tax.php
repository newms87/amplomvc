<?php 
class ControllerTotalTax extends Controller { 
	
	 
	public function index() { 
		$this->template->load('total/tax');

		$this->load->language('total/tax');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('tax', $_POST);
		
			$this->message->add('success', $this->_('text_success'));
			
			$this->redirect($this->url->link('extension/total'));
		}
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
 
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_total'), $this->url->link('extension/total'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('total/tax'));

		$this->data['action'] = $this->url->link('total/tax');
		
		$this->data['cancel'] = $this->url->link('extension/total');

		if (isset($_POST['tax_status'])) {
			$this->data['tax_status'] = $_POST['tax_status'];
		} else {
			$this->data['tax_status'] = $this->config->get('tax_status');
		}

		if (isset($_POST['tax_sort_order'])) {
			$this->data['tax_sort_order'] = $_POST['tax_sort_order'];
		} else {
			$this->data['tax_sort_order'] = $this->config->get('tax_sort_order');
		}
																				
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'total/tax')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}