<?php
class Catalog_Controller_Account_Update extends Controller
{
	public function index()
	{
		//Load Language
		$this->language->load('account/update');

		//Login Verification
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/update');

			$this->url->redirect('account/login');
		}

		//Handle POST
		if ($this->request->isPost() && $this->validate()) {
			$this->customer->edit($_POST);

			if (!empty($_POST['payment_method_id']) && !empty($_POST['payment_key'])) {
				$this->System_Extension_Payment->get($_POST['payment_method_id'])->update_card($_POST['payment_key'], array('default' => true));
			}

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect('account/account');
		}

		//The Template
		$this->template->load('account/update');

		//Page Head
		$this->document->setTitle($this->_('head_title'));

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
		$this->breadcrumb->add($this->_('text_edit'), $this->url->link('account/update'));

		//Handle POST
		if (!$this->request->isPost()) {
			$customer_info             = $this->customer->info();
			$customer_info['metadata'] = $this->customer->getMetaData();
		}
		else {
			$customer_info = $_POST;
		}

		//Load Data or Defaults
		$defaults = array(
			'firstname'  => '',
			'lastname'   => '',
			'email'      => '',
			'telephone'  => '',
			'fax'        => '',
			'metadata'   => array(),
			'newsletter' => 1,
		);

		$this->data += $customer_info + $defaults;

		//Additional Data
		$default_shipping_address_id = isset($this->data['metadata']['default_shipping_address_id']) ? $this->data['metadata']['default_shipping_address_id'] : null;

		$addresses = $this->customer->getShippingAddresses();

		if (!empty($addresses) && (!$default_shipping_address_id || !array_search_key('address_id', $default_shipping_address_id, $addresses))) {
			$first_address                                         = current($addresses);
			$this->data['metadata']['default_shipping_address_id'] = $first_address['address_id'];
		}

		foreach ($addresses as &$address) {
			$address['display'] = $this->address->format($address);
			$address['remove'] = $this->url->link('account/update/remove_address', 'address_id=' . $address['address_id']);
		}
		unset($address);

		$this->data['data_addresses'] = $addresses;

		$this->data['card_select'] = $this->System_Extension_Payment->get('braintree')->cardSelect(null, true);

		//Action Buttons
		$this->data['save']        = $this->url->link('account/update');
		$this->data['back']        = $this->url->link('account/account');
		$this->data['add_address'] = $this->url->link('account/address/update');

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

	public function remove_address()
	{
		if (!empty($_GET['address_id'])) {
			if ($this->address->canRemove($_GET['address_id'])) {
				$this->address->remove($_GET['address_id']);
			}

			$error = $this->address->getError();
		}

		if ($this->request->isAjax()) {
			if ($error) {
				$this->response->setOutput(json_encode(array('error' => $error)));
			}
		} else {
			if ($error) {
				$this->message->add('warning', $error);
			}

			$this->url->redirect('account/update');
		}
	}

	private function validate()
	{
		if (!$this->validation->text($_POST['firstname'], 1, 32)) {
			$this->error['firstname'] = $this->_('error_firstname');
		}

		if (!$this->validation->text($_POST['lastname'], 1, 32)) {
			$this->error['lastname'] = $this->_('error_lastname');
		}

		if (!$this->validation->email($_POST['email'])) {
			$this->error['email'] = $this->_('error_email');
		}

		if (($this->customer->info('email') !== $_POST['email']) && $this->customer->emailRegistered($_POST['email'])) {
			$this->error['warning'] = $this->_('error_exists');
		}

		if (isset($_POST['telephone']) && !$this->validation->phone($_POST['telephone'])) {
			$this->error['telephone'] = $this->_('error_telephone');
		}

		if (!empty($_POST['password']) && !$this->validation->password($_POST['password'])) {
			$this->error['password'] = $this->_('error_password');
		} elseif ($_POST['password'] !== $_POST['confirm']) {
			$this->error['confirm'] = $this->_('error_confirm');
		}

		$_POST['newsletter'] = !empty($_POST['newsletter']) ? 1 : 0;

		return $this->error ? false : true;
	}
}
