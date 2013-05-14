<?php 
class ControllerTotalLowOrderFee extends Controller { 
	 
	 
	public function index() { 
		$this->template->load('total/low_order_fee');

		$this->load->language('total/low_order_fee');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('low_order_fee', $_POST);
		
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('total/low_order_fee'));

		$this->data['action'] = $this->url->link('total/low_order_fee');
		
		$this->data['cancel'] = $this->url->link('extension/total');

		if (isset($_POST['low_order_fee_total'])) {
			$this->data['low_order_fee_total'] = $_POST['low_order_fee_total'];
		} else {
			$this->data['low_order_fee_total'] = $this->config->get('low_order_fee_total');
		}
		
		if (isset($_POST['low_order_fee_fee'])) {
			$this->data['low_order_fee_fee'] = $_POST['low_order_fee_fee'];
		} else {
			$this->data['low_order_fee_fee'] = $this->config->get('low_order_fee_fee');
		}

		if (isset($_POST['low_order_fee_tax_class_id'])) {
			$this->data['low_order_fee_tax_class_id'] = $_POST['low_order_fee_tax_class_id'];
		} else {
			$this->data['low_order_fee_tax_class_id'] = $this->config->get('low_order_fee_tax_class_id');
		}
		
		if (isset($_POST['low_order_fee_status'])) {
			$this->data['low_order_fee_status'] = $_POST['low_order_fee_status'];
		} else {
			$this->data['low_order_fee_status'] = $this->config->get('low_order_fee_status');
		}

		if (isset($_POST['low_order_fee_sort_order'])) {
			$this->data['low_order_fee_sort_order'] = $_POST['low_order_fee_sort_order'];
		} else {
			$this->data['low_order_fee_sort_order'] = $this->config->get('low_order_fee_sort_order');
		}
		
		$this->data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'total/low_order_fee')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}