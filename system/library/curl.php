<?php

class Curl extends Library
{
	private $response;

	const RESPONSE_TEXT = 'Text';
	const RESPONSE_JSON = 'JSON';
	const RESPONSE_DATA = 'Data';

	public function getResponse()
	{
		return $this->response;
	}

	public function get($url, $data = '', $response_type = self::RESPONSE_TEXT, $options = array())
	{
		$url = site_url($url, $data);

		$opts = $options + array(
				// return web page
				CURLOPT_RETURNTRANSFER => true,
				// return headers
				CURLOPT_HEADER         => false,
				// follow redirects
				CURLOPT_FOLLOWLOCATION => true,
				// handle all encodings
				CURLOPT_ENCODING       => "",
				// The Amplo MVC Browser
				CURLOPT_USERAGENT      => "Amplo MVC " . AMPLO_VERSION . " - Curl post request",
				// set referrer on redirect
				CURLOPT_AUTOREFERER    => true,
				// timeout on connect
				CURLOPT_CONNECTTIMEOUT => 120,
				// timeout on response
				CURLOPT_TIMEOUT        => 120,
				// stop after 10 redirects
				CURLOPT_MAXREDIRS      => 10,
				//SSL Verified
				CURLOPT_SSL_VERIFYPEER => false,
				//Explain everything
				CURLOPT_VERBOSE        => 1,
				//1 time use
				CURLOPT_FORBID_REUSE   => 1,
			);

		return $this->call($url, $response_type, $opts);
	}

	public function post($url, $data, $response_type = self::RESPONSE_TEXT, $options = array())
	{
		$opts = $options + array(
				CURLOPT_RETURNTRANSFER => true,
				// return web page
				CURLOPT_HEADER         => false,
				// don't return headers
				CURLOPT_FOLLOWLOCATION => true,
				// follow redirects
				CURLOPT_ENCODING       => "",
				// handle all encodings
				CURLOPT_USERAGENT      => "Amplo MVC " . AMPLO_VERSION . " - Curl post request",
				CURLOPT_AUTOREFERER    => true,
				// set referer on redirect
				CURLOPT_CONNECTTIMEOUT => 120,
				// timeout on connect
				CURLOPT_TIMEOUT        => 120,
				// timeout on response
				CURLOPT_MAXREDIRS      => 10,
				// stop after 10 redirects
				CURLOPT_POST           => 1,
				CURLOPT_POSTFIELDS     => http_build_query($data),
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_VERBOSE        => 1,
				CURLOPT_FORBID_REUSE   => 1,
			);

		return $this->call($url, $response_type, $opts);
	}

	public function call($url, $response_type, $options)
	{
		//Init Curl
		$ch = curl_init($url);

		if (defined('AMPLO_CURLOPT_PROXY')) {
			$options[CURLOPT_PROXY] = AMPLO_CURLOPT_PROXY;
		}

		if (!$ch) {
			$this->error = _l("There was an error initializing cURL!");
		} //Set Options
		else if (!curl_setopt_array($ch, $options)) {
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

		write_log('curl', _l("%s - upload size: %s, download size: %s, time: %s", $url, $this->response['header']['size_upload'], $this->response['header']['size_download'], $this->response['header']['total_time']));

		//Error failed
		if ($this->error) {
			if (empty($this->response['content'])) {
				if ($errno === 56 && (!isset($options[CURLOPT_HTTP_VERSION]) || $options[CURLOPT_HTTP_VERSION] !== CURL_HTTP_VERSION_1_0)) {
					$options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_0;
					return $this->call($url, $response_type, $options);
				}

				write_log('curl_error', _l("%s(): %s - Error in response: %s", __METHOD__, $url, $this->error));
				return false;
			}

			write_log('curl_error', _l("%s(): %s - Error in response (w/ content returned): %s", __METHOD__, $url, $this->error));
		}

		//Response
		switch ($response_type) {
			case self::RESPONSE_JSON:
				if (isset($options[CURLOPT_HTTP_VERSION]) && $options[CURLOPT_HTTP_VERSION] === CURL_HTTP_VERSION_1_0) {
					$this->response['content'] = utf8_decode($this->response['content']);
				}

				$json = @json_decode($this->response['content'], true);

				if ($json === null) {
					write_log('curl_error', _l("%s(): %s - JSON decode Failed with ERROR (%s): %s", __METHOD__, $url, json_last_error(), json_last_error_msg()));
				}

				return $json;

			case self::RESPONSE_TEXT:
				return $this->response['content'];

			case self::RESPONSE_DATA:
			default:
				return $this->response;
		}
	}
}
