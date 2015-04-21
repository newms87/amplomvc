<?php

class amploAPI
{
	public $config;

	private $response, $error, $token;

	const
		RESPONSE_TEXT = 'Text',
		RESPONSE_API = 'API',
		RESPONSE_JSON = 'JSON';


	public function __construct($config = array())
	{
		$this->config = (object)$config;

		if (empty($this->config->response_type)) {
			$this->config->response_type = self::RESPONSE_API;
		}

		if (!empty($_SESSION['amplo_api_token'])) {
			$this->token = $_SESSION['amplo_api_token'];
		}
	}

	public function hasError($type = null)
	{
		if ($type) {
			return !empty($this->error[$type]);
		}

		return !empty($this->error);
	}

	public function getError($type = null)
	{
		if ($type) {
			return isset($this->error[$type]) ? $this->error[$type] : null;
		}

		return $this->error;
	}

	public function fetchError($type = null)
	{
		$error = $this->getError($type);

		$this->clearErrors($type);

		return $error;
	}

	public function clearErrors($type = null)
	{
		if ($type) {
			unset($this->error[$type]);
		} else {
			$this->error = array();
		}
	}

	public function getResponse()
	{
		return $this->response;
	}

	public function getToken()
	{
		return $this->token;
	}

	public function setToken($token)
	{
		$response = $this->get('refresh-token', array('token' => $token));

		if ($response['status'] === 'error') {
			return false;
		}

		$this->token = $token;

		return true;
	}

	public function clearToken()
	{
		$this->token = null;
	}

	public function requestCustomerToken($customer_id)
	{
		return $this->requestToken($customer_id);
	}

	public function requestToken($customer_id = null)
	{
		if (!$this->config->api_key) {
			$this->error['api_key'] = "API Key must be set.";
		}

		if (!$this->config->api_user) {
			$this->error['api_user'] = "API User must be set.";
		}

		if (!$this->config->api_url) {
			$this->error['api_url'] = "API URL must be set.";
		}

		if ($this->error) {
			trigger_error(implode('<br>', $this->error));

			return false;
		}

		$this->token = null;

		$credentials = array(
			'api_key'  => $this->config->api_key,
			'api_user' => $this->config->api_user,
		);

		if ($customer_id) {
			$credentials['customer_id'] = $customer_id;
		}

		$response = $this->post('request-token', $credentials);

		if (empty($response['status'])) {
			$this->error['response'] = _l("Invalid response from server while requesting token.");

			return false;
		} elseif ($response['status'] === 'error') {
			$this->error['message'] = $response['message'];

			return false;
		}

		$this->token = $response['data']['token'];

		$_SESSION['amplo_api_token'] = $this->token;

		return $this->token;
	}

	public function get($uri, $data = '', $response_type = null, $options = array())
	{
		if ($data) {
			$uri .= (strpos($uri, '?') === false ? '?' : '&') . (is_string($data) ? $data : http_build_query($data));
		}

		return $this->call($uri, $response_type, $options);
	}

	public function post($uri, $data, $response_type = null, $options = array())
	{
		$options += array(
			CURLOPT_POST       => 1,
			CURLOPT_POSTFIELDS => http_build_query($data),
		);

		return $this->call($uri, $response_type, $options);
	}

	public function call($uri, $response_type = null, $options = array())
	{
		if (!$response_type) {
			$response_type = $this->config->response_type;
		}

		$url = $this->config->api_url . $uri;

		if ($this->token) {
			$url .= (strpos($uri, '?') === false ? '?' : '&') . 'token=' . $this->token;
		}

		$options += array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER         => false,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_USERAGENT      => "Amplo API - Curl",
			CURLOPT_AUTOREFERER    => true,
			CURLOPT_CONNECTTIMEOUT => 120,
			CURLOPT_TIMEOUT        => 120,
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_VERBOSE        => 0,
			CURLOPT_FORBID_REUSE   => 1,
		);

		//Init Curl
		$ch = curl_init($url);

		if (defined('AMPLO_CURLOPT_PROXY')) {
			$options[CURLOPT_PROXY] = AMPLO_CURLOPT_PROXY;
		}

		if (!$ch) {
			$this->error = _l("There was an error initializing cURL!");
		} else if (!curl_setopt_array($ch, $options)) {
			$this->error = _l("There was an error setting the cURL options!");
		} else {
			$content = curl_exec($ch);

			$errno  = curl_errno($ch);
			$errmsg = curl_error($ch);

			if ($errno) {
				$this->error = "Curl Error ($errno): $errmsg";
			}

			$this->response = array(
				'content' => $content,
				'header'  => curl_getinfo($ch),
				'errno'   => $errno,
				'errmsg'  => $errmsg,
			);

			curl_close($ch);
		}

		//Error failed
		if ($this->error) {
			if (empty($this->response['content'])) {
				if ($errno === 56 && (!isset($options[CURLOPT_HTTP_VERSION]) || $options[CURLOPT_HTTP_VERSION] !== CURL_HTTP_VERSION_1_0)) {
					$options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_0;

					$this->clearErrors();

					return $this->call($url, $response_type, $options);
				}
			}
		}

		//Response
		if ($response_type === self::RESPONSE_TEXT) {
			if ($this->error) {
				return $this->fetchError();
			} else {
				return $this->response['content'];
			}
		} else {
			if ($this->error) {
				return array(
					'status'  => 'error',
					'code'    => '2',
					'message' => $this->fetchError(),
				);
			}

			if (isset($options[CURLOPT_HTTP_VERSION]) && $options[CURLOPT_HTTP_VERSION] === CURL_HTTP_VERSION_1_0) {
				$this->response['content'] = utf8_decode($this->response['content']);
			}

			$json = @json_decode($this->response['content'], true);

			if ($response_type === self::RESPONSE_JSON) {
				return $json;
			}

			//$response_type === self::RESPONSE_API
			if (!isset($json)) {
				return array(
					'status'  => 'error',
					'code'    => 4,
					'message' => sprintf("%s(): %s - JSON decode Failed with ERROR (%s): %s", __METHOD__, $url, json_last_error(), json_last_error_msg()),
					'data'    => $this->fetchError(),
				);
			} elseif (empty($json['status'])) {
				return array(
					'status'  => 'error',
					'code'    => 5,
					'message' => "Invalid response from server at " . $this->api_url,
					'data'    => $json,
				);
			} elseif ($json['status'] === 'error' && (int)$json['code'] === 401) {
				$this->token = null;
			}

			return $json;
		}
	}
}

if (!function_exists('json_last_error_msg')) {
	function json_last_error_msg()
	{
		static $errors = array(
			JSON_ERROR_NONE           => null,
			JSON_ERROR_DEPTH          => 'Maximum stack depth exceeded',
			JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch',
			JSON_ERROR_CTRL_CHAR      => 'Unexpected control character found',
			JSON_ERROR_SYNTAX         => 'Syntax error, malformed JSON',
			JSON_ERROR_UTF8           => 'Malformed UTF-8 characters, possibly incorrectly encoded'
		);
		$error = json_last_error();

		return array_key_exists($error, $errors) ? $errors[$error] : "Unknown error ({$error})";
	}
}

if (!session_id()) {
	session_start();
}
