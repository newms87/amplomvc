<?php
class Catalog_Controller_Account_Address extends Controller 
{
	
  	public function index()
  	{
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/address');

			$this->url->redirect($this->url->link('account/login'));
		}
	
		$this->language->load('account/address');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
  	}

  	public function insert()
  	{
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/address');

			$this->url->redirect($this->url->link('account/login'));
		}

		$this->language->load('account/address');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validateForm()) {
			$address_id = $this->Model_Account_Address->addAddress($_POST);
			
			if (!empty($_POST['default'])) {
				$this->customer->set_setting('default_shipping_address_id', $address_id);
			}
			
			$this->message->add('success', $this->_('text_insert'));

			$this->url->redirect($this->url->link('account/address'));
		}
		
		$this->getForm();
  	}

  	public function update()
  	{
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/address');

			$this->url->redirect($this->url->link('account/login'));
		}
		
		$this->language->load('account/address');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Account_Address->editAddress($_GET['address_id'], $_POST);
			
			if (!empty($_POST['default'])) {
				$this->customer->set_setting('default_shipping_address_id', $_GET['address_id']);
			}
			
			if ((int)$_GET['address_id'] === $this->cart->getShippingAddressId()) {
				$this->cart->setShippingMethod();
			}

			if ((int)$_GET['address_id'] === $this->cart->getPaymentAddressId()) {
				$this->cart->setPaymentMethod();
			}
			
			$this->message->add('success', $this->_('text_update'));
			
			$this->url->redirect($this->url->link('account/address'));
		}
		
		$this->getForm();
  	}

  	public function delete()
  	{
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/address');

			$this->url->redirect($this->url->link('account/login'));
		}
			
		$this->language->load('account/address');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_GET['address_id']) && $this->validateDelete()) {
			$this->Model_Account_Address->deleteAddress($_GET['address_id']);

			if ((int)$_GET['address_id'] === $this->cart->getShippingAddressId()) {
				$this->cart->setShippingAddress();
				$this->cart->setShippingMethod();
			}

			if ((int)$_GET['address_id'] === $this->cart->getPaymentAddressId()) {
				$this->cart->setPaymentAddress();
				$this->cart->setPaymentMethod();
			}
			
			$this->message->add('success', $this->_('text_delete'));
	
			$this->url->redirect($this->url->link('account/address'));
		}
	
		$this->getList();
  	}

  	private function getList()
  	{
  		//The Template
		$this->template->load('account/address_list');
		
		//Breadcrumbs
  		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/address'));
		
		//Load Addresses
		$addresses = $this->customer->getAddresses();
		
		foreach ($addresses as &$address) {
			$address['address'] = $this->address->format($address);
			$address['update'] = $this->url->link('account/address/update', 'address_id=' . $address['address_id']);
			$address['delete'] = $this->url->link('account/address/delete', 'address_id=' . $address['address_id']);
		} unset($address);
		
		$this->data['addresses'] = $addresses;
		
		//Action Buttons
		$this->data['insert'] = $this->url->link('account/address/insert');
		$this->data['back'] = $this->url->link('account/account');
		
		//Dependencies
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);
		
		//Render
		$this->response->setOutput($this->render());
  	}

  	private function getForm()
  	{
		$this->template->load('account/address_form');

  		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/account'));
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('account/address'));
		
		$crumb_url = isset($_GET['address_id']) ? $this->url->link('account/address/update') : $this->url->link('account/address/insert');
		$this->breadcrumb->add($this->_('heading_title'), $crumb_url);
						
						
		if (!isset($_GET['address_id'])) {
			$this->data['action'] = $this->url->link('account/address/insert');
		} else {
			$this->data['action'] = $this->url->link('account/address/update', 'address_id=' . $_GET['address_id']);
		}
		
		if (isset($_GET['address_id']) && !$this->request->isPost()) {
			$address_info = $this->customer->getAddress($_GET['address_id']);
		}
	
		if (isset($_POST['firstname'])) {
				$this->data['firstname'] = $_POST['firstname'];
		} elseif (isset($address_info)) {
				$this->data['firstname'] = $address_info['firstname'];
		} else {
			$this->data['firstname'] = '';
		}

		if (isset($_POST['lastname'])) {
				$this->data['lastname'] = $_POST['lastname'];
		} elseif (isset($address_info)) {
				$this->data['lastname'] = $address_info['lastname'];
		} else {
			$this->data['lastname'] = '';
		}

		if (isset($_POST['company'])) {
				$this->data['company'] = $_POST['company'];
		} elseif (isset($address_info)) {
			$this->data['company'] = $address_info['company'];
		} else {
				$this->data['company'] = '';
		}

		if (isset($_POST['address_1'])) {
				$this->data['address_1'] = $_POST['address_1'];
		} elseif (isset($address_info)) {
			$this->data['address_1'] = $address_info['address_1'];
		} else {
				$this->data['address_1'] = '';
		}

		if (isset($_POST['address_2'])) {
				$this->data['address_2'] = $_POST['address_2'];
		} elseif (isset($address_info)) {
			$this->data['address_2'] = $address_info['address_2'];
		} else {
				$this->data['address_2'] = '';
		}

		if (isset($_POST['postcode'])) {
				$this->data['postcode'] = $_POST['postcode'];
		} elseif (isset($address_info)) {
			$this->data['postcode'] = $address_info['postcode'];
		} else {
				$this->data['postcode'] = '';
		}

		if (isset($_POST['city'])) {
				$this->data['city'] = $_POST['city'];
		} elseif (isset($address_info)) {
			$this->data['city'] = $address_info['city'];
		} else {
				$this->data['city'] = '';
		}

		if (isset($_POST['country_id'])) {
				$this->data['country_id'] = $_POST['country_id'];
		} elseif (isset($address_info)) {
				$this->data['country_id'] = $address_info['country_id'];
		} else {
				$this->data['country_id'] = $this->config->get('config_country_id');
		}

		if (isset($_POST['zone_id'])) {
				$this->data['zone_id'] = $_POST['zone_id'];
		} elseif (isset($address_info)) {
				$this->data['zone_id'] = $address_info['zone_id'];
		} else {
				$this->data['zone_id'] = '';
		}
		
		$this->data['countries'] = $this->Model_Localisation_Country->getCountries();

		if (isset($_POST['default'])) {
				$this->data['default'] = $_POST['default'];
		} elseif (isset($_GET['address_id'])) {
				$this->data['default'] = (int)$this->customer->get_setting('default_shipping_address_id') === (int)$_GET['address_id'];
		} else {
			$this->data['default'] = false;
		}

		$this->data['back'] = $this->url->link('account/address');
		
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
	
  	private function validateForm()
  	{
		if ((strlen($_POST['firstname']) < 1) || (strlen($_POST['firstname']) > 32)) {
				$this->error['firstname'] = $this->_('error_firstname');
		}

		if ((strlen($_POST['lastname']) < 1) || (strlen($_POST['lastname']) > 32)) {
				$this->error['lastname'] = $this->_('error_lastname');
		}

		if ((strlen($_POST['address_1']) < 3) || (strlen($_POST['address_1']) > 128)) {
				$this->error['address_1'] = $this->_('error_address_1');
		}

		if ((strlen($_POST['city']) < 2) || (strlen($_POST['city']) > 128)) {
				$this->error['city'] = $this->_('error_city');
		}
		
		$country_info = $this->Model_Localisation_Country->getCountry($_POST['country_id']);
		
		if ($country_info && $country_info['postcode_required'] && (strlen($_POST['postcode']) < 2) || (strlen($_POST['postcode']) > 10)) {
			$this->error['postcode'] = $this->_('error_postcode');
		}
		
		if ($_POST['country_id'] == '') {
				$this->error['country'] = $this->_('error_country');
		}
		
		if ($_POST['zone_id'] == '') {
				$this->error['zone'] = $this->_('error_zone');
		}
		
		return $this->error ? false : true;
  	}

  	private function validateDelete()
  	{
		if ($this->Model_Account_Address->getTotalAddresses() == 1) {
				$this->error['warning'] = $this->_('error_delete');
		}

		if ($this->customer->get_setting('default_shipping_address_id') === (int)$_GET['address_id']) {
				$this->error['warning'] = $this->_('error_default');
		}

		return $this->error ? false : true;
  	}
}
