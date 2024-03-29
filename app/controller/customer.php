<?php

/**
 * @author  Daniel Newman
 * @date    3/20/2013
 * @package Amplo MVC
 * @link    http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */
class App_Controller_Customer extends Controller
{
	public function __construct()
	{
		parent::__construct();

		switch ($this->router->getAction()->getMethod()) {
			//allow access only to these pages if logged in
			case 'logout':
			case 'success':
			case 'agree_to_terms':
				break;

			default:
				if (is_logged()) {
					if ($this->is_ajax) {
						output_json(array('success' => _l("You have been logged into your account.")));
						output_flush();
						exit;
					} else {
						redirect('account');
					}
				}
				break;
		}
	}

	public function index()
	{
		$this->login();
	}

	public function login($settings = array())
	{
		if (empty($settings)) {
			$settings = array();
		}

		//Page Head
		set_page_info('title', _l("Customer Sign In"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb(_l("Sign In"), site_url('customer/login'));

		if (isset($settings['redirect'])) {
			if (!empty($settings['redirect'])) {
				$this->request->setRedirect($settings['redirect']);
			} else {
				$this->request->clearRedirect();
			}
		} elseif (!empty($_REQUEST['redirect'])) {
			$this->request->setRedirect($_REQUEST['redirect']);
		} elseif (!$this->is_ajax && !$this->request->hasRedirect()) {
			$this->request->setRedirect();
		}

		//Block Settings
		$defaults = array(
			'username' => '',
			'size'     => 'large',
			'template' => 'customer/login',
			'redirect' => $this->request->getRedirect(),
		);

		$settings += $_POST + $defaults;

		//Template Data
		$login_settings = $this->config->loadGroup('login_settings');

		$medias = array();

		if (!empty($login_settings['status'])) {
			if (!empty($login_settings['facebook']['active'])) {
				$medias['facebook'] = array(
					'name' => 'facebook',
					'url'  => $this->Model_Block_Login_Facebook->getConnectUrl(),
				);
			}

			if (!empty($login_settings['google_plus']['active'])) {
				$medias['google-plus'] = array(
					'name' => 'google-plus',
					'url'  => $this->Model_Block_Login_Google->getConnectUrl(),
				);
			}
		}

		$settings['medias'] = $medias;

		//Render
		output($this->render($settings['template'], $settings));
	}

	public function authenticate()
	{
		if ($this->customer->login(_post('username'), _post('password'))) {
			if ($this->is_ajax) {
				message('success', _l("You have been logged into your account"));
			}
		} else {
			message('error', $this->customer->fetchError());
		}

		if ($this->is_ajax) {
			message('data', array('redirect' => $this->request->getRedirect()));
			output_message();
		} else {
			if ($this->message->has('error')) {
				post_redirect('customer/login');
			}

			//Resolve Redirect
			if ($this->request->hasRedirect()) {
				$this->request->doRedirect();
			} else {
				redirect('account');
			}
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
		if (is_logged()) {
			redirect('account');
		}

		//Page Head
		set_page_info('title', _l("Register Account"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb(_l("Login"), site_url('customer/login'));
		breadcrumb(_l("Register"), site_url('customer/registration'));

		$registration_data = $_POST;

		$defaults = array(
			'first_name' => '',
			'last_name'  => '',
			'email'      => '',
			'company'    => '',
			'address'    => '',
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
		if (option('terms_agreement_page_id')) {
			$information_info = $this->Model_Page->getRecord(option('terms_agreement_page_id'));

			if ($information_info) {
				$data['agree_to']    = site_url('page', 'page_id=' . option('terms_agreement_page_id'));
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
		if ($this->customer->register($_POST, true)) {
			message('success', _l("Your account has been created!"));
		} else {
			message('error', $this->customer->fetchError());
		}

		if ($this->is_ajax && !$this->request->hasRedirect()) {
			output_message();
		} else {
			if ($this->message->has('error')) {
				post_redirect('customer/login', 'register=1');
			}

			redirect('customer/success');
		}
	}

	public function success()
	{
		//Page Title
		set_page_info('title', _l("Your Account Has Been Created!"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb(_l("Account"), site_url('account'));
		breadcrumb(_l("Your Account Has Been Created!"), site_url('customer/success'));


		if (option('show_customer_success')) {
			//Render
			output($this->render('customer/success'));
		} else {
			//Redirect to requested page
			if ($this->request->hasRedirect()) {
				$this->request->doRedirect();
			} else {
				redirect('');
			}
		}
	}

	public function forgotten()
	{
		//Page Head
		set_page_info('title', _l("Forgot Your Password?"));

		//Breadcrumbs
		breadcrumb(_l('Home'), site_url());
		breadcrumb(_l('Login'), site_url('customer/login'));
		breadcrumb(_l('Forgotten Password'), site_url('customer/forgotten'));

		//Render
		output($this->render('customer/forgotten'));
	}

	public function generate_reset_code()
	{
		$code = $this->customer->generateCode();

		if (!$this->customer->setResetCode(_post('email'), $code)) {
			message('error', $this->customer->fetchError());
		} else {

			$email_data = array(
				'email' => _post('email'),
				'reset' => site_url('customer/reset_form', 'code=' . $code),
			);

			call('mail/forgotten', array($email_data));

			message('notify', _l("Please follow the link that was sent to your email to reset your password."));
		}

		redirect('customer/login');
	}

	public function reset_form()
	{
		if (is_logged() || empty($_GET['code'])) {
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
		breadcrumb(_l('Password Reset'), site_url('customer/reset-form', $_GET));

		$data['code'] = $code;

		//Render
		output($this->render('customer/reset_form', $data));
	}

	public function reset_password()
	{
		$customer_id = $this->customer->lookupResetCode(_get('code'));

		//User not found
		if (!$customer_id) {
			message('warning', _l("Invalid password reset code. Password was not reset. Try starting over from the <a href=\"%s\">forgotten password page</a>", site_url('customer/forgotten')));
			redirect('customer/login');
		}

		//Disable password verification check
		set_option('verify_password_on_change', false);

		$reset = array(
			'password' => _post('password'),
			'confirm'  => _post('confirm'),
		);

		if ($this->Model_Customer->save($customer_id, $reset)) {
			$this->customer->clearResetCode($customer_id);
			message('success', _l('You have successfully updated your password!'));
			redirect('customer/login');
		} else {
			message('error', $this->Model_Customer->fetchError());
			redirect('customer/reset_form', $_GET);
		}
	}

	public function agree_to_terms()
	{
		$this->customer->agreedToTerms();

		message('success', _l("You have agreed to the terms and conditions."));

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect(_get('redirect'));
		}
	}
}
