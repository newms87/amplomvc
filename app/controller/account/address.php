<?php

class App_Controller_Account_Address extends Controller
{
	public function index()
	{
		if (!is_logged()) {
			$this->request->setRedirect('account/address/form');

			redirect('customer/login');
		}

		//Page Head
		set_page_info('title', _l("Address Book"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb(_l("Account"), site_url('account'));
		breadcrumb(_l("Address Book"), site_url('account/address'));

		//Load Addresses
		$addresses = $this->customer->getAddresses();

		foreach ($addresses as &$address) {
			$address['address'] = $this->address->format($address);
			$address['update']  = site_url('account/address/update', 'address_id=' . $address['address_id']);
			$address['delete']  = site_url('account/address/delete', 'address_id=' . $address['address_id']);
		}
		unset($address);

		$data['addresses'] = $addresses;

		//Action Buttons
		$data['insert'] = site_url('account/address/form');
		$data['back']   = site_url('account');

		//Render
		output($this->render('account/address_list', $data));
	}

	public function update()
	{
		//Insert
		if (empty($_GET['address_id'])) {
			$address_id = $this->customer->addAddress($_POST);

			if ($address_id) {
				if (!empty($_POST['default'])) {
					$this->customer->setDefaultShippingAddress($address_id);
				}

				message('success', _l("You have successfully added an address to your account!"));
			} else {
				message('error', $this->address->getError());
			}
		} //Update
		else {
			if ($this->customer->editAddress($_GET['address_id'], $_POST)) {

				if (!empty($_POST['default'])) {
					$this->customer->setDefaultShippingAddress($_GET['address_id']);
				}

				message('success', _l("You have successfully updated your address."));
			} else {
				message('error', $this->address->getError());
			}
		}

		if ($this->is_ajax) {
			output_message();
		} elseif ($this->message->has('error')) {
			$this->form();
		} else {
			redirect('account/address');
		}
	}

	public function delete()
	{
		if (!empty($_GET['address_id'])) {
			$this->customer->removeAddress($_POST['address_id']);

			if ($this->customer->hasError()) {
				message('error', $this->customer->getError());
			} else {
				if (!$this->address->remove($_GET['address_id'])) {
					message('error', $this->address->getError());
				}
			}

			if (!$this->message->has('error')) {
				message('success', _l("Your address has been successfully deleted"));
			}
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('account/address');
		}
	}

	public function form()
	{
		//Page Head
		set_page_info('title', _l("Address Form"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb(_l("Account"), site_url('account'));
		breadcrumb(_l("Address Book"), site_url('account/address'));

		$crumb_url = isset($_GET['address_id']) ? site_url('account/address/update') : site_url('account/address/update');
		breadcrumb(_l("Address Book"), $crumb_url);

		//Insert or Update
		$address_id = !empty($_GET['address_id']) ? (int)$_GET['address_id'] : 0;

		//Load Information
		$defaults = array(
			'firstname'  => customer_info('firstname'),
			'lastname'   => customer_info('lastname'),
			'company'    => '',
			'address_1'  => '',
			'address_2'  => '',
			'postcode'   => '',
			'city'       => '',
			'country_id' => option('config_country_id'),
			'zone_id'    => '',
			'default'    => false,
		);

		$address_info = array();

		if (IS_POST) {
			$address_info = $_POST;
		} elseif ($address_id) {
			$address_info = $this->customer->getAddress($_GET['address_id']);

			$address_info['default'] = (int)$this->customer->getDefaultShippingAddressId() === $address_id;
		}

		$data = $address_info + $defaults;

		$data['address_id'] = $address_id;

		//Template Data
		$data['data_countries'] = $this->Model_Localisation_Country->getActiveCountries();

		$data['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		$data['$this->is_ajax'] = $this->is_ajax;

		//Action Buttons
		$data['save'] = site_url('account/address/update', 'address_id=' . $address_id);

		//Render
		$template = $this->is_ajax ? 'account/address_form_ajax' : 'account/address_form';
		output($this->render($template, $data));
	}
}
