<?php
class Catalog_Controller_Account_Address extends Controller
{
	public function index()
	{
		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', site_url('account/address'));

			redirect('customer/login');
		}

		$this->getList();
	}

	public function update()
	{
		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', site_url('account/address'));

			redirect('customer/login');
		}

		if ($this->request->isPost()) {
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
				return;
			} elseif (!$this->message->has('error', 'warning')) {
				redirect('account/address');
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', site_url('account/address'));

			redirect('customer/login');
		}

		$this->document->setTitle(_l("Address Book"));

		if (!empty($_GET['address_id'])) {
			if (!$this->customer->removeAddress($_POST['address_id'])) {
				$this->message->add('error', $this->address->getError());
			}
			$this->address->remove($_GET['address_id']);

			if ((int)$_GET['address_id'] === $this->cart->getShippingAddressId()) {
				$this->cart->clearShippingAddress();
				$this->cart->clearShippingMethod();
			}

			if ((int)$_GET['address_id'] === $this->cart->getPaymentAddressId()) {
				$this->cart->clearPaymentAddress();
				$this->cart->clearPaymentMethod();
			}

			$this->message->add('success', _l("Your address has been successfully deleted"));

			redirect('account/address');
		}

		$this->getList();
	}

	private function getList()
	{
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
		$data['insert'] = site_url('account/address/update');
		$data['back']   = site_url('account');

		//Render
		$this->response->setOutput($this->render('account/address_list', $data));
	}

	private function getForm()
	{
		//Page Head
		$this->document->setTitle(_l("Address Book"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Address Book"), site_url('account'));
		$this->breadcrumb->add(_l("Home"), site_url('account/address'));

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
		$data['data_countries'] = $this->Model_Localisation_Country->getCountries();

		$data['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		//Action Buttons
		$data['save'] = site_url('account/address/update', 'address_id=' . $address_id);

		if (!$this->request->isAjax()) {
			$data['back'] = site_url('account/address');
		}

		//Render
		$this->response->setOutput($this->render('account/address_form', $data));
	}
}
