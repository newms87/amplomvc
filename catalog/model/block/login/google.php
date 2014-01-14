<?php
class Catalog_Model_Block_Login_Google extends Model
{
	private $application_name = "Google+ PHP Quickstart";
	private $client_id = '876588091863.apps.googleusercontent.com';
	private $client_secret = 'lTIREo7JyWzm_TRLL4PNeklN';
	private $api_key = 'AIzaSyBMltXCirsFleaevacaAe7KEpb27UZa6xA';

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
		$this->request->setRedirect($this->url->here(), null, 'gp_redirect');

		$query = array(
			'scope'         => "https://www.googleapis.com/auth/plus.profile.emails.read",
			'state'         => $this->getStateToken(),
			'redirect_uri'  => $this->url->link("block/login/google/connect"),
			'response_type' => 'code',
			'client_id'     => $this->client_id,
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
			$this->error['code'] = _l("sYour access code was unable to be verified");
			return false;
		}

		require_once DIR_RESOURCES . "googleAPI/php_client/src/Google_Client.php";
		require_once DIR_RESOURCES . "googleAPI/php_client/src/contrib/Google_PlusService.php";

		$client = new Google_Client();
		$client->setApplicationName($this->application_name);
		$client->setClientId($this->client_id);
		$client->setClientSecret($this->client_secret);
		$client->setRedirectUri($this->url->link("block/login/google/connect"));

		try {
			$client->authenticate();
		} catch (Exception $e) {
			$this->error_log->write($e);
			$this->error['exception'] = _l("There was a problem authenticating your credentials.");
			return false;
		}

		$jsonTokens        = $client->getAccessToken();
		$_SESSION['token'] = $jsonTokens;

		$tokens = json_decode($jsonTokens);

		$query = array(
			'access_token' => $tokens->access_token,
		);

		$response = $this->curl->get("https://www.googleapis.com/plus/v1/people/me", $query);

		$data = json_decode($response['content']);

		if (empty($data)) {
			$this->error['data'] = _l("There was an error in the response from Google+");
			return false;
		}

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
