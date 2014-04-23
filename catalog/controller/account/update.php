<?php
class Catalog_Controller_Account_Update extends Controller
{
	public function index()
	{
		//Login Verification
		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', $this->url->link('account/update'));

			$this->url->redirect('account/login');
		}

		//Handle POST
		if ($this->request->isPost() && $this->validate()) {
			$this->customer->edit($_POST);

			if (!empty($_POST['payment_code']) && !empty($_POST['payment_key'])) {
				$this->System_Extension_Payment->get($_POST['payment_code'])->updateCard($_POST['payment_key'], array('default' => true));
			}

			$this->message->add('success', _l("Your account information has been updated successfully!"));

			$this->url->redirect('account/account');
		}

		//Page Head
		$this->document->setTitle(_l("My Account Information"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account/account'));
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
			$address['remove']  = $this->url->link('account/update/remove_address', 'address_id=' . $address['address_id']);
		}
		unset($address);

		$data['data_addresses'] = $addresses;


		//TODO: This is a temporary hack to integrate with braintree
		$data['card_select'] = $this->call('extension/payment/braintree/select_card', array(null, true));

		//Action Buttons
		$data['save']        = $this->url->link('account/update');
		$data['back']        = $this->url->link('account/account');
		$data['add_address'] = $this->url->link('account/address/update');

		//Render
		$this->response->setOutput($this->render('account/update', $data));
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

	private function validate()
	{
		if (!$this->validation->text($_POST['firstname'], 1, 32)) {
			$this->error['firstname'] = _l("First Name must be between 1 and 32 characters!");
		}

		if (!$this->validation->text($_POST['lastname'], 1, 32)) {
			$this->error['lastname'] = _l("Last Name must be between 1 and 32 characters!");
		}

		if (!$this->validation->email($_POST['email'])) {
			$this->error['email'] = _l("The email address you provided is invalid.");
		}

		if (($this->customer->info('email') !== $_POST['email']) && $this->customer->emailRegistered($_POST['email'])) {
			$this->error['warning'] = _l("This email address is already registered under a different account.");
		}

		if (isset($_POST['telephone']) && !$this->validation->phone($_POST['telephone'])) {
			$this->error['telephone'] = _l("The phone number you provided is invalid.");
		}

		if (!empty($_POST['password'])) {
			if (!$this->validation->password($_POST['password'])) {
				$this->error['password'] = $this->validation->getError();
			} elseif ($_POST['password'] !== $_POST['confirm']) {
				$this->error['confirm'] = _l("Your password and confirmation do not match!");
			}
		}

		$_POST['newsletter'] = !empty($_POST['newsletter']) ? 1 : 0;

		return $this->error ? false : true;
	}
}
