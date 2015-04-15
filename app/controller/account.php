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

		$data['path']    = $this->route->getPath();
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

		$data['path'] = $this->route->getPath();

		//Customer Information
		$customer['customer'] = customer_info();
		$customer['meta']     = $this->customer->meta();

		$options = array(
			'index' => 'address_id',
			'cache' => true
		);

		$customer['addresses'] = $this->Model_Customer->getAddresses(customer_info('customer_id'), null, null, $options);

		$filter = array(
			'country_id' => option('config_country_id', 223),
		);

		$customer['data_zones'] = $this->Model_Localisation_Zone->getRecords(null, $filter, array('cache' => true));

		//Render
		$content = $this->render('account/details', $customer);

		if ($this->is_ajax) {
			output($content);
		} else {
			$data['content'] = $content;

			output($this->render('account/account', $data));
		}
	}

	public function save()
	{
		if ($this->Model_Customer->save(customer_info('customer_id'), $_POST)) {
			message('success', _l("Your account information has been updated successfully!"));
		} else {
			message('error', $this->Model_Customer->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('account');
		}
	}
}
