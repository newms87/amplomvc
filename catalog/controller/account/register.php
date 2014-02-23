<?php
class Catalog_Controller_Account_Register extends Controller
{
	public function index()
	{
		if ($this->customer->isLogged()) {
			$this->url->redirect('account/account');
		}

		$this->document->setTitle(_l("Register Account"));

		if ($this->request->isPost() && $this->validate()) {
			$this->customer->add($_POST);

			$this->customer->login($_POST['email'], $_POST['password']);

			//Redirect to requested page
			if ($this->request->hasRedirect()) {
				$this->request->doRedirect();
			}

			$this->url->redirect('account/success');
		}

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account/account'));
		$this->breadcrumb->add(_l("Register"), $this->url->link('account/register'));

		$this->data['action'] = $this->url->link('account/register');

		$registration_data = array();

		if ($this->request->isPost()) {
			$registration_data = $_POST;
		}

		$defaults = array(
			'firstname'  => '',
			'lastname'   => '',
			'email'      => '',
			'company'    => '',
			'address_1'  => '',
			'address_2'  => '',
			'postcode'   => '',
			'city'       => '',
			'country_id' => $this->config->get('config_country_id'),
			'zone_id'    => '',
			'password'   => '',
			'confirm'    => '',
			'newsletter' => 1,
			'agree'      => false
		);

		$this->data += $registration_data + $defaults;

		//Template Data
		$this->data['data_countries'] = $this->Model_Localisation_Country->getCountries();

		//TODO: update this to a page!
		if ($this->config->get('config_account_terms_info_id')) {
			$information_info = $this->Model_Catalog_Information->getInformation($this->config->get('config_account_terms_info_id'));

			if ($information_info) {
				$this->data['agree_to']    = $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_account_terms_info_id'));
				$this->data['agree_title'] = $information_info['title'];
			}
		}

		$this->data['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		//Action Buttons
		$this->data['login'] = $this->url->link('account/login');

		//The Template
		$this->template->load('account/register');

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

	public function validate()
	{
		if (!$this->validation->text($_POST['firstname'], 1, 32)) {
			$this->error['firstname'] = _l("First Name must be between 1 and 32 characters!");
		}

		if (!$this->validation->text($_POST['lastname'], 1, 32)) {
			$this->error['lastname'] = _l("Last Name must be between 1 and 32 characters!");
		}

		if (!$this->validation->email($_POST['email'])) {
			$this->error['email'] = $this->validation->getError();
		}

		if ($this->customer->emailRegistered($_POST['email'])) {
			$this->error['email'] = _l("Warning: E-Mail Address is already registered!");
		}

		if (!$this->address->validate($_POST)) {
			$this->error = $this->address->getError();
		}

		if (!$this->validation->password($_POST['password'])) {
			$this->error['password'] = $this->validation->getError();
		}

		if ($_POST['confirm'] !== $_POST['password']) {
			$this->error['confirm'] = _l("Password confirmation does not match password!");
		}

		if ($this->config->get('config_account_terms_info_id')) {
			$information_info = $this->Model_Catalog_Information->getInformation($this->config->get('config_account_terms_info_id'));

			if ($information_info && !isset($_POST['agree'])) {
				$this->error['warning'] = sprintf(_l("Warning: You must agree to the %s!"), $information_info['title']);
			}
		}

		return $this->error ? false : true;
	}
}
