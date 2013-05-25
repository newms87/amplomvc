<?php 
class ControllerPaymentFreeCheckout extends Controller {
	
	
	public function index() { 
		$this->template->load('payment/free_checkout');

		$this->load->language('payment/free_checkout');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('free_checkout', $_POST);

			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('extension/payment'));
		}
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('payment/free_checkout'));

		$this->data['action'] = $this->url->link('payment/free_checkout');

		$this->data['cancel'] = $this->url->link('extension/payment');	
				
		if (isset($_POST['free_checkout_order_status_id'])) {
			$this->data['free_checkout_order_status_id'] = $_POST['free_checkout_order_status_id'];
		} else {
			$this->data['free_checkout_order_status_id'] = $this->config->get('free_checkout_order_status_id'); 
		} 
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
				
		if (isset($_POST['free_checkout_status'])) {
			$this->data['free_checkout_status'] = $_POST['free_checkout_status'];
		} else {
			$this->data['free_checkout_status'] = $this->config->get('free_checkout_status');
		}
		
		if (isset($_POST['free_checkout_sort_order'])) {
			$this->data['free_checkout_sort_order'] = $_POST['free_checkout_sort_order'];
		} else {
			$this->data['free_checkout_sort_order'] = $this->config->get('free_checkout_sort_order');
		}
						
		$this->children = array(
			'common/header',
			'common/footer'
		);
			
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/free_checkout')) {
			$this->error['warning'] = $this->_('error_permission');
		}
				
		return $this->error ? false : true;	
	}
}