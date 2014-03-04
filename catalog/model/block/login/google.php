<?php
class Catalog_Model_Block_Login_Google extends Model
{
	private $settings;

	public function __construct($registry)
	{
		parent::__construct($registry);

		$this->settings = $this->config->load('login_settings', 'google_plus');
	}

	public function getStateToken()
	{
		if (empty($this->session->data['gp_state'])) {
			$this->session->set('gp_state', md5(rand()));
		}

		return $this->session->data['gp_state'];
	}

	public function getConnectUrl()
	{
		//Redirect after login
		if (strpos($this->url->getPath(), 'account/logout') !== 0) {
			$this->request->setRedirect($this->url->here(), null, 'gp_redirect');
		} else {
			$this->request->setRedirect($this->url->link('account/account'), null, 'gp_redirect');
		}

		$query = array(
			'scope'         => "https://www.googleapis.com/auth/plus.profile.emails.read",
			'state'         => $this->getStateToken(),
			'redirect_uri'  => $this->url->link("block/login/google/connect"),
			'response_type' => 'code',
			'client_id'     => $this->settings['client_id'],
			'access_type'   => 'offline',
		);

		return $this->url->link('https://accounts.google.com/o/oauth2/auth', $query);
	}

	public function authenticate()
	{
		if (empty($_GET['state']) || empty($this->session->data['gp_state']) || $_GET['state'] !== $this->session->data['gp_state']) {
			$this->error['state'] = _l("Unable to verify the User");
			return false;
		}

		if (!empty($_GET['error_code'])) {
			$this->error['error_code'] = $_GET['error_message'];
			return false;
		}

		if (empty($_GET['code'])) {
			$this->error['code'] = _l("Your access code was unable to be verified");
			return false;
		}

		//Authentication
		$auth_data = array(
			'code' => $_GET['code'],
		   'client_id' => $this->settings['client_id'],
		   'client_secret' => $this->settings['client_secret'],
		   'redirect_uri' => $this->url->link("block/login/google/connect"),
		   'grant_type' => 'authorization_code',
		);

		$response = $this->curl->post("https://accounts.google.com/o/oauth2/token", $auth_data, Curl::RESPONSE_JSON);

		if (empty($response->access_token)) {
			$msg = _l("There was a problem authenticating your credentials.");
			$this->error['exception'] = $msg;
			$this->error_log->write($msg);
			return false;
		}

		$_SESSION['token'] = $response->access_token;

		$query = array(
			'access_token' => $response->access_token,
		);

		$data = $this->curl->get("https://www.googleapis.com/plus/v1/people/me", $query, Curl::RESPONSE_JSON);

		if (empty($data)) {
			$this->error['data'] = _l("There was an error in the response from Google+");
			return false;
		}

		return $this->registerCustomer($data);
	}

	private function registerCustomer($data)
	{
		$customer_id = $this->queryVar("SELECT customer_id FROM " . DB_PREFIX . "customer_meta WHERE `key` = 'google+_id' AND `value` = '" . $this->escape($data->id) . "' LIMIT 1");

		//Lookup Customer or Register new customer
		if (!$customer_id) {
			$no_meta = true;
			$email   = !empty($data->emails[0]) ? $data->emails[0]->value : '';

			if ($email) {
				$customer = $this->queryRow("SELECT * FROM " . DB_PREFIX . "customer WHERE email = '" . $this->escape($email) . "'");
			}

			if (empty($customer)) {
				if (!$data->name->givenName && !$data->name->familyName && $data->displayName) {
					$names                 = explode(' ', $data->displayName, 2);
					$data->name->givenName = $names[0];

					if (!empty($names[1])) {
						$data->name->familyName = $names[1];
					}
				}

				$customer = array(
					'firstname' => $data->name->givenName,
					'lastname'  => $data->name->familyName,
					'email'     => $email,
				);

				$this->customer->add($customer);
			}
		} else {
			$customer = $this->customer->getCustomer($customer_id);
			$no_meta  = false;
		}

		//Login Customer
		if (!$this->customer->login($customer['email'], AC_CUSTOMER_OVERRIDE)) {
			$this->error['login'] = _l("Customer login failed. Please try again");
			return false;
		}

		//Set Meta for future login
		if ($no_meta) {
			$this->customer->setMeta('google+_id', $data->id);
		}

		return true;
	}
}
