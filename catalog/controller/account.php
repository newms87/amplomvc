<?php
class Catalog_Controller_Account extends Controller
{
	public function index()
	{
		//Login Verification
		if (!$this->customer->isLogged()) {
			$this->request->setRedirect('account');

			$this->url->redirect('customer/login');
		}

		//Page Head
		$this->document->setTitle(_l("Account Manager"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account Manager"), $this->url->link('account'));

		//Page Information
		$shipping_address            = $this->customer->getDefaultShippingAddress();
		$shipping_address['display'] = $this->address->format($shipping_address);
		$data['shipping_address']    = $shipping_address;

		//Customer Information
		$customer = $this->customer->info() + $this->customer->getMeta();

		$customer['display_name'] = $customer['firstname'] . ' ' . $customer['lastname'];

		$data['newsletter_display'] = $customer['newsletter'] ? _l("Send me weekly updates from %s!", $this->config->get('config_name')) : _l("Do not send me any emails.");

		$data['customer'] = $customer;

		//Urls
		$data['url_order_history'] = $this->url->link('account/order');
		$data['url_returns']       = $this->url->link('account/return');

		//Action Buttons
		$data['edit_account'] = $this->url->link('account/update');
		$data['back']         = $this->url->link('common/home');

		//Render
		$this->response->setOutput($this->render('account/account', $data));
	}

	public function update()
	{
		//Login Verification
		if (!$this->customer->isLogged()) {
			$this->request->setRedirect($this->url->link('account/update'));

			$this->url->redirect('customer/login');
		}

		//Page Head
		$this->document->setTitle(_l("My Account Information"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account'));
		$this->breadcrumb->add(_l("Edit Information"), $this->url->link('account/update'));

		//Handle POST
		if (!$this->request->isPost()) {
			$customer_info             = $this->customer->info();
			$customer_info['metadata'] = $this->customer->getMeta();
		} else {
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

		$data = $customer_info + $defaults;

		//Template Data
		$default_shipping_address_id = isset($data['metadata']['default_shipping_address_id']) ? $data['metadata']['default_shipping_address_id'] : null;

		$addresses = $this->customer->getShippingAddresses();

		if (!empty($addresses) && (!$default_shipping_address_id || !array_search_key('address_id', $default_shipping_address_id, $addresses))) {
			$first_address                                         = current($addresses);
			$data['metadata']['default_shipping_address_id'] = $first_address['address_id'];
		}

		foreach ($addresses as &$address) {
			$address['display'] = $this->address->format($address);
			$address['remove']  = $this->url->link('account/remove_address', 'address_id=' . $address['address_id']);
		}
		unset($address);

		$data['data_addresses'] = $addresses;

		//Action Buttons
		$data['save']        = $this->url->link('account/submit_update');
		$data['back']        = $this->url->link('account');
		$data['add_address'] = $this->url->link('account/address/update');

		//Render
		$this->response->setOutput($this->render('account/update', $data));
	}

	public function submit_update()
	{
		$this->customer->edit($_POST);

		if (!empty($_POST['payment_code']) && !empty($_POST['payment_key'])) {
			$this->System_Extension_Payment->get($_POST['payment_code'])->updateCard($_POST['payment_key'], array('default' => true));
		}

		$this->message->add('success', _l("Your account information has been updated successfully!"));

		$this->url->redirect('account');
	}

	public function remove_address()
	{
		if (!empty($_GET['address_id'])) {
			if (!$this->customer->removeAddress($_GET['address_id'])) {
				$this->message->add('error', $this->customer->getError());
			}
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} else {
			$this->url->redirect('account/update');
		}
	}
}
