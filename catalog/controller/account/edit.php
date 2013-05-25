<?php
class ControllerAccountEdit extends Controller {
	

	public function index() {
		$this->template->load('account/edit');

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/edit');

			$this->url->redirect($this->url->link('account/login'));
		}

		$this->language->load('account/edit');
		
		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_account_customer->editCustomer($_POST);
			
			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('account/account'));
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
			$this->breadcrumb->add($this->_('text_edit'), $this->url->link('account/edit'));

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['firstname'])) {
			$this->data['error_firstname'] = $this->error['firstname'];
		} else {
			$this->data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$this->data['error_lastname'] = $this->error['lastname'];
		} else {
			$this->data['error_lastname'] = '';
		}
		
		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}	
		
		if (isset($this->error['telephone'])) {
			$this->data['error_telephone'] = $this->error['telephone'];
		} else {
			$this->data['error_telephone'] = '';
		}	

		$this->data['action'] = $this->url->link('account/edit');

		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
		}

		if (isset($_POST['firstname'])) {
			$this->data['firstname'] = $_POST['firstname'];
		} elseif (isset($customer_info)) {
			$this->data['firstname'] = $customer_info['firstname'];
		} else {
			$this->data['firstname'] = '';
		}

		if (isset($_POST['lastname'])) {
			$this->data['lastname'] = $_POST['lastname'];
		} elseif (isset($customer_info)) {
			$this->data['lastname'] = $customer_info['lastname'];
		} else {
			$this->data['lastname'] = '';
		}

		if (isset($_POST['email'])) {
			$this->data['email'] = $_POST['email'];
		} elseif (isset($customer_info)) {
			$this->data['email'] = $customer_info['email'];
		} else {
			$this->data['email'] = '';
		}

		if (isset($_POST['telephone'])) {
			$this->data['telephone'] = $_POST['telephone'];
		} elseif (isset($customer_info)) {
			$this->data['telephone'] = $customer_info['telephone'];
		} else {
			$this->data['telephone'] = '';
		}

		if (isset($_POST['fax'])) {
			$this->data['fax'] = $_POST['fax'];
		} elseif (isset($customer_info)) {
			$this->data['fax'] = $customer_info['fax'];
		} else {
			$this->data['fax'] = '';
		}

		$this->data['back'] = $this->url->link('account/account');







		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'	
		);
						
		$this->response->setOutput($this->render());	
	}

	private function validate() {
		if ((strlen($_POST['firstname']) < 1) || (strlen($_POST['firstname']) > 32)) {
			$this->error['firstname'] = $this->_('error_firstname');
		}

		if ((strlen($_POST['lastname']) < 1) || (strlen($_POST['lastname']) > 32)) {
			$this->error['lastname'] = $this->_('error_lastname');
		}

		if ((strlen($_POST['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['email'])) {
			$this->error['email'] = $this->_('error_email');
		}
		
		if (($this->customer->getEmail() != $_POST['email']) && $this->model_account_customer->getTotalCustomersByEmail($_POST['email'])) {
			$this->error['warning'] = $this->_('error_exists');
		}

		if (isset($_POST['telephone']) && (strlen($_POST['telephone']) < 3) || (strlen($_POST['telephone']) > 32)) {
			$this->error['telephone'] = $this->_('error_telephone');
		}

		return $this->error ? false : true;
	}
}