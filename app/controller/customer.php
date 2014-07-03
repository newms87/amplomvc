<?php

class App_Controller_Customer extends Controller
{
	public function __construct()
	{
		parent::__construct();

		//Only allow access to certain pages if already logged in
		$allowed = array(
			'customer/logout',
			'customer/success',
		);

		if ($this->customer->isLogged() && !in_array($this->route->getPath(), $allowed)) {
			redirect('account');
		}
	}

	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Account Login"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb(_l("Login"), site_url('customer/login'));

		//Input Data
		$user_info = array();

		if (IS_POST) {
			$user_info = $_POST;
		}

		$defaults = array(
			'username' => '',
		);

		$data = $user_info + $defaults;

		//Template Data
		$data['gp_login'] = $this->Model_Block_Login_Google->getConnectUrl();
		$data['fb_login'] = $this->Model_Block_Login_Facebook->getConnectUrl();

		//Action Buttons
		$data['login']     = site_url('customer/login');
		$data['register']  = site_url('customer/registration');
		$data['forgotten'] = site_url('customer/forgotten');

		//Resolve Redirect
		if (!empty($_REQUEST['redirect'])) {
			$this->request->setRedirect($_REQUEST['redirect']);
		} elseif (!$this->request->hasRedirect()) {
			$this->request->setRedirect();
		}

		//Render
		output($this->render('customer/login', $data));
	}

	public function login()
	{
		if (IS_POST) {
			if (!$this->customer->login($_POST['username'], $_POST['password'])) {
				message('warning', _l("Login failed. Invalid username and / or password."));
			}
		}

		if (IS_AJAX) {
			output($this->message->toJSON());
		} else {
			if (!IS_POST || $this->message->has('error')) {
				return $this->index();

			}
			//Resolve Redirect
			if ($this->request->hasRedirect()) {
				$this->request->doRedirect();
			}

			redirect('account');
		}
	}

	public function logout()
	{
		$this->customer->logout();

		message('notify', _l("You have been logged out of your account"));

		redirect();
	}

	public function registration()
	{
		if ($this->customer->isLogged()) {
			redirect('account');
		}

		//Page Head
		$this->document->setTitle(_l("Register Account"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb(_l("Login"), site_url('customer/login'));
		breadcrumb(_l("Register"), site_url('customer/registration'));

		$registration_data = array();

		if (IS_POST) {
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
			'country_id' => option('config_country_id'),
			'zone_id'    => '',
			'password'   => '',
			'confirm'    => '',
			'newsletter' => 1,
			'agree'      => false
		);

		$data = $registration_data + $defaults;

		//Template Data
		$data['data_countries'] = $this->Model_Localisation_Country->getActiveCountries();

		//TODO: update this to a page!
		if (option('config_account_terms_page_id')) {
			$information_info = $this->Model_Page->getPage(option('config_account_terms_page_id'));

			if ($information_info) {
				$data['agree_to']    = site_url('page', 'page_id=' . option('config_account_terms_page_id'));
				$data['agree_title'] = $information_info['title'];
			}
		}

		$data['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		$data['errors'] = $this->message->get('error');

		//Action Buttons
		$data['login']    = site_url('customer/login');
		$data['register'] = site_url('customer/register');

		//Render
		output($this->render('customer/registration', $data));
	}

	public function register()
	{
		if (!$this->customer->register($_POST)) {
			message('error', $this->customer->getError());
			return $this->registration();
		}

		$this->customer->login($_POST['email'], $_POST['password']);

		//Redirect to requested page
		if ($this->request->hasRedirect()) {
			$this->request->doRedirect();
		}

		redirect('customer/success');
	}

	public function success()
	{
		//Page Title
		$this->document->setTitle(_l("Your Account Has Been Created!"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb(_l("Account"), site_url('account'));
		breadcrumb(_l("Your Account Has Been Created!"), site_url('customer/success'));

		//Render
		output($this->render('customer/success'));
	}

	public function forgotten()
	{
		//Page Head
		$this->document->setTitle(_l("Forgot Your Password?"));

		//Breadcrumbs
		breadcrumb(_l('Home'), site_url());
		breadcrumb(_l('Login'), site_url('customer/login'));
		breadcrumb(_l('Forgotten Password'), site_url('customer/forgotten'));

		//Action Buttons
		$data['save'] = site_url('customer/generate_reset_code');
		$data['back'] = site_url('customer/login');

		//Render
		output($this->render('customer/forgotten', $data));
	}

	public function generate_reset_code()
	{
		$code = $this->customer->generateCode();

		if (!$this->customer->setResetCode($_POST['email'], $code)) {
			message('error', $this->customer->getError());
		} else {

			$email_data = array(
				'email' => $_POST['email'],
				'reset' => site_url('customer/reset_form', 'code=' . $code),
			);

			call('mail/forgotten', $email_data);

			message('notify', _l("Please follow the link that was sent to your email to reset your password."));
		}

		redirect('customer/login');
	}

	public function reset_form()
	{
		if ($this->customer->isLogged() || empty($_GET['code'])) {
			redirect();
		}

		$code = $_GET['code'];

		$customer_id = $this->customer->lookupResetCode($code);

		//User not found
		if (!$customer_id) {
			message('warning', _l("Unable to locate password reset code. Please try again."));
			redirect('customer/login');
		}

		//Breadcrumbs
		breadcrumb(_l('Home'), site_url());
		breadcrumb(_l('Password Reset'), site_url('customer/reset', 'code=' . $code));

		//Action Buttons
		$data['save']   = site_url('customer/reset_password', 'code=' . $code);
		$data['cancel'] = site_url('customer/login');

		//Render
		output($this->render('customer/reset_form', $data));
	}

	public function reset_password()
	{
		$customer_id = $this->customer->lookupResetCode($_GET['code']);

		//User not found
		if (!$customer_id) {
			message('warning', _l("Unable to locate password reset code. Please try again."));
			redirect('customer/login');
		}

		//Validate Password
		if (!validate('password', $_POST['password'])) {
			message('error', $this->validation->getError());
			redirect('customer/reset_form');
		}

		$this->customer->setId($customer_id);
		$this->customer->updatePassword($_POST['password']);
		$this->customer->clearResetCode();

		message('success', _l('You have successfully updated your password!'));

		redirect('customer/login');
	}
}
