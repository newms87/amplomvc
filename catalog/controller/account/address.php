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

		$this->getList();
	}

	public function update()
	{
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/address');

			$this->url->redirect($this->url->link('account/login'));
		}

		$this->language->load('account/address');

		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['address_id'])) {
				$address_id = $this->address->add($_POST);

				if (!empty($_POST['default'])) {
					$this->customer->setMeta('default_shipping_address_id', $address_id);
				}
			} //Update
			else {
				$this->address->update($_GET['address_id'], $_POST);

				if (!empty($_POST['default'])) {
					$this->customer->setMeta('default_shipping_address_id', $_GET['address_id']);
				}

				if ((int)$_GET['address_id'] === $this->cart->getShippingAddressId()) {
					$this->cart->setShippingMethod();
				}

				if ((int)$_GET['address_id'] === $this->cart->getPaymentAddressId()) {
					$this->cart->setPaymentMethod();
				}
			}

			if (!$this->message->error_set()) {
				if ($this->request->isAjax()) {
					return; //output nothing for an ajax request
				} else {
					$this->message->add('success', !empty($_GET['address_id']) ? $this->_('text_update') : $this->_('text_insert'));
					$this->url->redirect($this->url->link('account/address'));
				}
			}
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

		$this->document->setTitle($this->_('head_title'));

		if (isset($_GET['address_id']) && $this->validateDelete()) {
			$this->address->delete($_GET['address_id']);

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
		//Page Head
		$this->document->setTitle($this->_('head_title'));

		//The Template
		$this->template->load('account/address_list');

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('account/address'));

		//Load Addresses
		$addresses = $this->customer->getAddresses();

		foreach ($addresses as &$address) {
			$address['address'] = $this->address->format($address);
			$address['update']  = $this->url->link('account/address/update', 'address_id=' . $address['address_id']);
			$address['delete']  = $this->url->link('account/address/delete', 'address_id=' . $address['address_id']);
		}
		unset($address);

		$this->data['addresses'] = $addresses;

		//Action Buttons
		$this->data['insert'] = $this->url->link('account/address/update');
		$this->data['back']   = $this->url->link('account/account');

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
		//Page Head
		$this->document->setTitle($this->_('head_title'));

		//The Template
		$this->template->load('account/address_form');

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('account/account'));
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('account/address'));

		$crumb_url = isset($_GET['address_id']) ? $this->url->link('account/address/update') : $this->url->link('account/address/update');
		$this->breadcrumb->add($this->_('head_title'), $crumb_url);

		//Insert or Update
		$address_id = !empty($_GET['address_id']) ? (int)$_GET['address_id'] : 0;

		//Load Information
		if ($address_id && !$this->request->isPost()) {
			$address_info = $this->customer->getAddress($_GET['address_id']);

			$address_info['default'] = (int)$this->customer->getMeta('default_shipping_address_id') === $address_id;
		}

		//Load Data or Defaults
		$defaults = array(
			'firstname'  => '',
			'lastname'   => '',
			'company'    => '',
			'address_1'  => '',
			'address_2'  => '',
			'postcode'   => '',
			'city'       => '',
			'country_id' => $this->config->get('config_country_id'),
			'zone_id'    => '',
			'default'    => false,
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($address_info[$key])) {
				$this->data[$key] = $address_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		//Additional Information
		$this->data['data_countries'] = $this->Model_Localisation_Country->getCountries();

		//Action Buttons
		if ($this->request->isAjax()) {
			$this->data['save'] = $this->url->ajax('account/address/update', 'address_id=' . $address_id);
		} else {
			$this->data['save'] = $this->url->link('account/address/update', 'address_id=' . $address_id);
			$this->data['back'] = $this->url->link('account/address');
		}

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

	private function validateForm()
	{
		if (!$this->validation->text($_POST['firstname'], 1, 32)) {
			$this->error['firstname'] = $this->_('error_firstname');
		}

		if (!$this->validation->text($_POST['lastname'], 1, 32)) {
			$this->error['lastname'] = $this->_('error_lastname');
		}

		if (!$this->validation->text($_POST['address_1'], 1, 128)) {
			$this->error['address_1'] = $this->_('error_address_1');
		}

		if (!$this->validation->text($_POST['city'], 2, 128)) {
			$this->error['city'] = $this->_('error_city');
		}

		$country_info = $this->Model_Localisation_Country->getCountry($_POST['country_id']);

		if ($country_info && $country_info['postcode_required'] && (strlen($_POST['postcode']) < 2) || (strlen($_POST['postcode']) > 10)) {
			$this->error['postcode'] = $this->_('error_postcode');
		}

		if ($_POST['country_id'] == '') {
			$this->error['country'] = $this->_('error_country');
		}

		if (!isset($_POST['zone_id']) || $_POST['zone_id'] === '') {
			$this->error['zone'] = $this->_('error_zone');
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->address->canDelete($_POST['address_id'])) {
			$this->error += $this->address->getError();
		}

		return $this->error ? false : true;
	}
}
