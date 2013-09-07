<?php
class Catalog_Controller_Account_Update extends Controller
{
	public function index()
	{
		//Login Verification
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/update');

			$this->url->redirect($this->url->link('account/login'));
		}

		//Handle POST
		if ($this->request->isPost() && $this->validate()) {
			$this->customer->edit($_POST);

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('account/account'));
		}

		//The Template and Language
		$this->template->load('account/update');
		$this->language->load('account/update');

		//Page Head
		$this->document->setTitle($this->_('head_title'));

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
		$this->breadcrumb->add($this->_('text_edit'), $this->url->link('account/update'));


		if (!$this->request->isPost()) {
			$customer_info             = $this->customer->info();
			$customer_info['metadata'] = $this->customer->getMetaData();
		}

		$addresses = $this->customer->getAddresses();

		//Load Data or Defaults
		$defaults = array(
			'firstname'  => '',
			'lastname'   => '',
			'email'      => '',
			'telephone'  => '',
			'fax'        => '',
			'address_id' => null,
			'metadata'   => array(),
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($customer_info[$key])) {
				$this->data[$key] = $customer_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		//Additional Data
		if (!$this->data['address_id'] && !empty($addresses)) {
			$first_address            = current($addresses);
			$this->data['address_id'] = $first_address['address_id'];
		}

		foreach ($addresses as &$address) {
			$address['display'] = $this->address->format($address);
		}
		unset($address);

		$this->data['data_addresses'] = $addresses;

		//Action Buttons
		$this->data['save'] = $this->url->link('account/update');
		$this->data['back'] = $this->url->link('account/account');
		$this->data['add_address'] = $this->url->link('account/address/update');

		//Ajax
		$this->data['ajax_add_address'] = $this->url->ajax('account/address/update');

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

		return $this->error ? false : true;
	}
}
