<?php

class App_Controller_Account extends Controller
{
	static $allow = array(
		'access' => '.*',
	);

	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Account Manager"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb(_l("Account Manager"), site_url('account'));

		//Page Information
		$data['shipping_address'] = $this->customer->getDefaultShippingAddress();

		//Customer Information
		$data['customer'] = customer_info() + $this->customer->getMeta();

		//Actions
		$data['edit_account'] = site_url('account/update');

		//Render
		output($this->render('account/account', $data));
	}

	public function update()
	{
		//Page Head
		$this->document->setTitle(_l("My Account Information"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb(_l("Account"), site_url('account'));
		breadcrumb(_l("Edit Information"), site_url('account/update'));

		//Handle POST
		if (!IS_POST) {
			$customer_info             = customer_info();
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
		if (!isset($data['metadata']['default_shipping_address_id'])) {
			$data['metadata']['default_shipping_address_id'] = '';
		}

		$data['data_addresses'] = $this->customer->getShippingAddresses();

		//Actions
		$data['save'] = site_url('account/submit-update');

		//Render
		output($this->render('account/update', $data));
	}

	public function submit_update()
	{
		$this->customer->edit($_POST);

		if (!empty($_POST['payment_code']) && !empty($_POST['payment_key'])) {
			$this->System_Extension_Payment->get($_POST['payment_code'])->updateCard($_POST['payment_key'], array('default' => true));
		}

		message('success', _l("Your account information has been updated successfully!"));

		redirect('account');
	}

	public function remove_address()
	{
		if (!empty($_GET['address_id'])) {
			if (!$this->customer->removeAddress($_GET['address_id'])) {
				message('error', $this->customer->getError());
			}
		}

		if (IS_AJAX) {
			output_json($this->message->fetch());
		} else {
			redirect('account/update');
		}
	}
}
