<?php 
class ControllerTotalHandling extends Controller { 
	
	
	public function index() { 
		$this->template->load('total/handling');

		$this->load->language('total/handling');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('handling', $_POST);
		
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('total/handling'));

		$this->data['action'] = $this->url->link('total/handling');
		
		$this->data['cancel'] = $this->url->link('extension/total');

		if (isset($_POST['handling_total'])) {
			$this->data['handling_total'] = $_POST['handling_total'];
		} else {
			$this->data['handling_total'] = $this->config->get('handling_total');
		}
		
		if (isset($_POST['handling_fee'])) {
			$this->data['handling_fee'] = $_POST['handling_fee'];
		} else {
			$this->data['handling_fee'] = $this->config->get('handling_fee');
		}
		
		if (isset($_POST['handling_tax_class_id'])) {
			$this->data['handling_tax_class_id'] = $_POST['handling_tax_class_id'];
		} else {
			$this->data['handling_tax_class_id'] = $this->config->get('handling_tax_class_id');
		}

		if (isset($_POST['handling_status'])) {
			$this->data['handling_status'] = $_POST['handling_status'];
		} else {
			$this->data['handling_status'] = $this->config->get('handling_status');
		}

		if (isset($_POST['handling_sort_order'])) {
			$this->data['handling_sort_order'] = $_POST['handling_sort_order'];
		} else {
			$this->data['handling_sort_order'] = $this->config->get('handling_sort_order');
		}
		
		$this->data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'total/handling')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;	
	}
}