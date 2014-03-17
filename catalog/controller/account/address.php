<?php
class Catalog_Controller_Account_Address extends Controller
{
	public function index()
	{
		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', $this->url->link('account/address'));

			$this->url->redirect('account/login');
		}

		$this->getList();
	}

	public function update()
	{
		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', $this->url->link('account/address'));

			$this->url->redirect('account/login');
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
			} elseif (!$this->message->hasError()) {
				$this->url->redirect('account/address');
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', $this->url->link('account/address'));

			$this->url->redirect('account/login');
		}

		$this->document->setTitle(_l("Address Book"));

		if (!empty($_GET['address_id'])) {
			if (!$this->customer->removeAddress($_POST['address_id'])) {
				$this->error += $this->address->getError();
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

			$this->url->redirect('account/address');
		}

		$this->getList();
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Address Book"));

		//The Template
		$this->view->load('account/address_list');

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account/account'));
		$this->breadcrumb->add(_l("Address Book"), $this->url->link('account/address'));

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
			'area/left',
			'area/right',
			'area/top',
			'area/bottom',
			'common/footer',
			'common/header'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		//Page Head
		$this->document->setTitle(_l("Address Book"));

		//The Template
		$this->view->load('account/address_form');

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Address Book"), $this->url->link('account/account'));
		$this->breadcrumb->add(_l("Home"), $this->url->link('account/address'));

		$crumb_url = isset($_GET['address_id']) ? $this->url->link('account/address/update') : $this->url->link('account/address/update');
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
			'country_id' => $this->config->get('config_country_id'),
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

		$this->data = $address_info + $defaults;

		$this->data['address_id'] = $address_id;

		//Template Data
		$this->data['data_countries'] = $this->Model_Localisation_Country->getCountries();

		$this->data['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		//Action Buttons
		$this->data['save'] = $this->url->link('account/address/update', 'address_id=' . $address_id);

		if (!$this->request->isAjax()) {
			$this->data['back'] = $this->url->link('account/address');
		}

		//Dependencies
		$this->children = array(
			'area/left',
			'area/right',
			'area/top',
			'area/bottom',
			'common/footer',
			'common/header'
		);

		//Render
		$this->response->setOutput($this->render());
	}
}
