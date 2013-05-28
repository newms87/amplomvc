<?php
class ControllerTotalCredit extends Controller {
	
	
	public function index() {
		$this->template->load('total/credit');

		$this->load->language('total/credit');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('credit', $_POST);
		
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('total/credit'));

		$this->data['action'] = $this->url->link('total/credit');
		
		$this->data['cancel'] = $this->url->link('extension/total');

		if (isset($_POST['credit_status'])) {
			$this->data['credit_status'] = $_POST['credit_status'];
		} else {
			$this->data['credit_status'] = $this->config->get('credit_status');
		}

		if (isset($_POST['credit_sort_order'])) {
			$this->data['credit_sort_order'] = $_POST['credit_sort_order'];
		} else {
			$this->data['credit_sort_order'] = $this->config->get('credit_sort_order');
		}
																		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'total/credit')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
	}
}