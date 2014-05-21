<?php

class App_Controller_Account_Address extends Controller
{
	public function index()
	{
		if (!$this->customer->isLogged()) {
			$this->request->setRedirect('account/address/form');

			redirect('customer/login');
		}

		//Page Head
		$this->document->setTitle(_l("Address Book"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Account"), site_url('account'));
		$this->breadcrumb->add(_l("Address Book"), site_url('account/address'));

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
		$this->response->setOutput($this->render('account/address_list', $data));
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

				$this->message->add('success', _l("You have successfully added an address to your account!"));
			} else {
				$this->message->add('error', $this->address->getError());
			}
		} //Update
		else {
			if ($this->customer->editAddress($_GET['address_id'], $_POST)) {

				if (!empty($_POST['default'])) {
					$this->customer->setDefaultShippingAddress($_GET['address_id']);
				}

				//If the shipping address in the cart has been updated, invalidate the shipping method
				if ((int)$_GET['address_id'] === $this->cart->getShippingAddressId()) {
					$this->cart->setShippingMethod();
				}

				//If the payment address in the cart has been updated, invalidate the payment method
				if ((int)$_GET['address_id'] === $this->cart->getPaymentAddressId()) {
					$this->cart->clearPaymentMethod();
				}

				$this->message->add('success', _l("You have successfully updated your address."));
			} else {
				$this->message->add('error', $this->address->getError());
			}
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
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
				$this->message->add('error', $this->customer->getError());
			} else {
				if (!$this->address->remove($_GET['address_id'])) {
					$this->message->add('error', $this->address->getError());
				}

				if ((int)$_GET['address_id'] === $this->cart->getShippingAddressId()) {
					$this->cart->clearShippingAddress();
				}

				if ((int)$_GET['address_id'] === $this->cart->getPaymentAddressId()) {
					$this->cart->clearPaymentAddress();
				}
			}

			if (!$this->message->has('error')) {
				$this->message->add('success', _l("Your address has been successfully deleted"));
			}
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} else {
			redirect('account/address');
		}
	}

	public function form()
	{
		//Page Head
		$this->document->setTitle(_l("Address Form"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Account"), site_url('account'));
		$this->breadcrumb->add(_l("Address Book"), site_url('account/address'));

		$crumb_url = isset($_GET['address_id']) ? site_url('account/address/update') : site_url('account/address/update');
		$this->breadcrumb->add(_l("Address Book"), $crumb_url);

		//Insert or Update
		$address_id = !empty($_GET['address_id']) ? (int)$_GET['address_id'] : 0;

		//Load Information
		$defaults = array(
			'firstname'  => $this->customer->info('firstname'),
			'lastname'   => $this->customer->info('lastname'),
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

		if ($this->request->isPost()) {
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

		$data['is_ajax'] = $this->request->isAjax();

		//Action Buttons
		$data['save'] = site_url('account/address/update', 'address_id=' . $address_id);

		//Render
		$template = $this->request->isAjax() ? 'account/address_form_ajax' : 'account/address_form';
		$this->response->setOutput($this->render($template, $data));
	}
}
