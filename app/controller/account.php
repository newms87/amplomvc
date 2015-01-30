<?php

class App_Controller_Account extends Controller
{
	static $allow = array(
		'access' => '.*',
	);

	public function index($content = '')
	{
		if (!$content) {
			return $this->details();
		}

		$data['path'] = $this->route->getPath();
		$data['content'] = $content;

		//Render
		output($this->render('account/account', $data));
	}

	public function details()
	{
		//Page Head
		set_page_info('title', _l("My Details"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb(_l("My Account"), site_url('account'));
		breadcrumb(_l("My Details"), site_url('account/details'));

		//Customer Information
		$data['customer'] = customer_info();
		$data['meta']     = $this->customer->meta();

		//Render
		$content = $this->render('account/details', $data);

		if ($this->is_ajax) {
			output($content);
		} else {
			$this->index($content);
		}
	}

	public function form()
	{
		//Page Head
		set_page_info('title', _l("My Account Information"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb(_l("Account"), site_url('account'));
		breadcrumb(_l("Edit Information"), site_url('account/update'));

		//Handle POST
		if (!IS_POST) {
			$customer_info             = customer_info();
			$customer_info['metadata'] = $this->customer->meta();
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

		$data['data_addresses'] = $this->Model_Customer->getAddresses($this->customer->getId());

		//Actions
		$data['save'] = site_url('account/submit-update');

		//Render
		output($this->render('account/update', $data));
	}

	public function update()
	{
		if ($this->Model_Customer->save($this->customer->getId(), $_POST)) {
			message('success', _l("Your account information has been updated successfully!"));
		} else {
			message('error', $this->Model_Customer->getError());
		}

		if (!empty($_POST['payment_code']) && !empty($_POST['payment_key'])) {
			$this->System_Extension_Payment->get($_POST['payment_code'])->updateCard($_POST['payment_key'], array('default' => true));
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('account');
		}
	}

	public function remove_address()
	{
		if (!empty($_GET['address_id'])) {
			if (!$this->customer->removeAddress($_GET['address_id'])) {
				message('error', $this->customer->getError());
			}
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('account/update');
		}
	}
}
