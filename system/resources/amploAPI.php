<?php

class amploAPI
{
	public $config;

	private $response, $error;

	const
		RESPONSE_TEXT = 'Text',
		RESPONSE_JSON = 'JSON';


	public function __construct($config = array())
	{
		$this->config = (object)$config;
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

	public function clearErrors($type)
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

	public function get($uri, $data = '', $response_type = self::RESPONSE_JSON, $options = array())
	{
		if ($data) {
			$uri .= (strpos($uri, '?') === false ? '&' : '?') . (is_string($data) ? $data : http_build_query($data));
		}

		return $this->call($uri, $response_type, $options);
	}

	public function post($uri, $data, $response_type = self::RESPONSE_JSON, $options = array())
	{
		$options += array(
			CURLOPT_POST       => 1,
			CURLOPT_POSTFIELDS => http_build_query($data),
		);

		return $this->call($uri, $response_type, $options);
	}

	public function call($uri, $response_type, $options)
	{
		if (!$this->config->api_key) {
			trigger_error("API Key must be set.");
			return false;
		}

		if (!$this->config->api_user) {
			trigger_error("API User must be set.");
			return false;
		}

		if (!$this->config->api_url) {
			trigger_error("API URL must be set.");
			return false;
		}

		$url = $this->config->api_url . $uri;

		$options += array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER         => false,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_USERAGENT      => "Amplo API - Curl request",
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

					return $this->call($url, $response_type, $options);
				}

				return false;
			}
		}

		//Response
		switch ($response_type) {
			case self::RESPONSE_TEXT:
				return $this->response['content'];

			case self::RESPONSE_JSON:
			default:
				if (isset($options[CURLOPT_HTTP_VERSION]) && $options[CURLOPT_HTTP_VERSION] === CURL_HTTP_VERSION_1_0) {
					$this->response['content'] = utf8_decode($this->response['content']);
				}

				$json = @json_decode($this->response['content'], true);

				if ($json === null) {
					$this->error['json_decode'] = sprintf("%s(): %s - JSON decode Failed with ERROR (%s): %s", __METHOD__, $url, json_last_error(), json_last_error_msg());
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
