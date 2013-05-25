<?php 
class ControllerAccountAddress extends Controller {
	
  	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/address');

			$this->url->redirect($this->url->link('account/login')); 
		}
	
		$this->language->load('account/address');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
  	}

  	public function insert() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/address');

			$this->url->redirect($this->url->link('account/login')); 
		} 

		$this->language->load('account/address');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_account_address->addAddress($_POST);
			
				$this->message->add('success', $this->_('text_insert'));

			$this->url->redirect($this->url->link('account/address'));
		} 
		
		$this->getForm();
  	}

  	public function update() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/address');

			$this->url->redirect($this->url->link('account/login')); 
		} 
		
		$this->language->load('account/address');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
				$this->model_account_address->editAddress($_GET['address_id'], $_POST);
			
			if (isset($this->session->data['shipping_address_id']) && ($_GET['address_id'] == $this->session->data['shipping_address_id'])) {
				unset($this->session->data['shipping_method']);	
			}

			if (isset($this->session->data['payment_address_id']) && ($_GET['address_id'] == $this->session->data['payment_address_id'])) {
				unset($this->session->data['payment_method']);
			}
			
			$this->message->add('success', $this->_('text_update'));
	
			$this->url->redirect($this->url->link('account/address'));
		} 
		
		$this->getForm();
  	}

  	public function delete() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/address');

			$this->url->redirect($this->url->link('account/login')); 
		} 
			
		$this->language->load('account/address');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_GET['address_id']) && $this->validateDelete()) {
			$this->model_account_address->deleteAddress($_GET['address_id']);	

			if (isset($this->session->data['shipping_address_id']) && ($_GET['address_id'] == $this->session->data['shipping_address_id'])) {
				unset($this->session->data['shipping_address_id']);
				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
			}

			if (isset($this->session->data['payment_address_id']) && ($_GET['address_id'] == $this->session->data['payment_address_id'])) {
				unset($this->session->data['payment_address_id']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);
			}
			
			$this->message->add('success', $this->_('text_delete'));
	
			$this->url->redirect($this->url->link('account/address'));
		}
	
		$this->getList();	
  	}

  	private function getList() {
		$this->template->load('account/address_list');

  		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/address'));
		
		$this->data['addresses'] = array();
		
		$results = $this->model_account_address->getAddresses();

		foreach ($results as $result) {
			if ($result['address_format']) {
					$format = $result['address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}
		
			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
					'{address_1}',
					'{address_2}',
				'{city}',
					'{postcode}',
					'{zone}',
				'{zone_code}',
					'{country}'
			);
	
			$replace = array(
				'firstname' => $result['firstname'],
				'lastname'  => $result['lastname'],
				'company'	=> $result['company'],
					'address_1' => $result['address_1'],
					'address_2' => $result['address_2'],
					'city'		=> $result['city'],
					'postcode'  => $result['postcode'],
					'zone'		=> $result['zone'],
				'zone_code' => $result['zone_code'],
					'country'	=> $result['country']  
			);

				$this->data['addresses'][] = array(
				'address_id' => $result['address_id'],
				'address'	=> str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format)))),
				'update'	=> $this->url->link('account/address/update', 'address_id=' . $result['address_id']),
				'delete'	=> $this->url->link('account/address/delete', 'address_id=' . $result['address_id'])
				);
		}

		$this->data['insert'] = $this->url->link('account/address/insert');
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

  	private function getForm() {
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
		
		if (isset($_GET['address_id']) && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
			$address_info = $this->model_account_address->getAddress($_GET['address_id']);
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
		}  elseif (isset($address_info)) {
				$this->data['country_id'] = $address_info['country_id'];			
		} else {
				$this->data['country_id'] = $this->config->get('config_country_id');
		}

		if (isset($_POST['zone_id'])) {
				$this->data['zone_id'] = $_POST['zone_id'];
		}  elseif (isset($address_info)) {
				$this->data['zone_id'] = $address_info['zone_id'];			
		} else {
				$this->data['zone_id'] = '';
		}
		
		$this->data['countries'] = $this->model_localisation_country->getCountries();

		if (isset($_POST['default'])) {
				$this->data['default'] = $_POST['default'];
		} elseif (isset($_GET['address_id'])) {
				$this->data['default'] = $this->customer->getAddressId() == $_GET['address_id'];
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
	
  	private function validateForm() {
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
		
		$country_info = $this->model_localisation_country->getCountry($_POST['country_id']);
		
		if ($country_info && $country_info['postcode_required'] && (strlen($_POST['postcode']) < 2) || (strlen($_POST['postcode']) > 10)) {
			$this->error['postcode'] = $this->_('error_postcode');
		}
		
		if ($_POST['country_id'] == '') {
				$this->error['country'] = $this->_('error_country');
		}
		
		if ($_POST['zone_id'] == '') {
				$this->error['zone'] = $this->_('error_zone');
		}
		
		if (!$this->error) {
				return true;
		} else {
				return false;
		}
  	}

  	private function validateDelete() {
		if ($this->model_account_address->getTotalAddresses() == 1) {
				$this->error['warning'] = $this->_('error_delete');
		}

		if ($this->customer->getAddressId() == $_GET['address_id']) {
				$this->error['warning'] = $this->_('error_default');
		}

		if (!$this->error) {
				return true;
		} else {
				return false;
		}
  	}
}
