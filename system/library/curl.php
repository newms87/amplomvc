<?php
class Curl extends Library
{
	private $response;

	public function getResponse()
	{
		return $this->response;
	}

	public function get($url, $data = '', $options = array())
	{
		$url = $this->url->link($url, $data);

		$opts = $options + array(
				CURLOPT_RETURNTRANSFER => true,
				// return web page
				CURLOPT_HEADER         => false,
				// don't return headers
				CURLOPT_FOLLOWLOCATION => true,
				// follow redirects
				CURLOPT_ENCODING       => "",
				// handle all encodings
				CURLOPT_USERAGENT      => "AmploCart " . AC_VERSION . " - Curl post request",
				CURLOPT_AUTOREFERER    => true,
				// set referer on redirect
				CURLOPT_CONNECTTIMEOUT => 120,
				// timeout on connect
				CURLOPT_TIMEOUT        => 120,
				// timeout on response
				CURLOPT_MAXREDIRS      => 10,
				// stop after 10 redirects
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_VERBOSE        => 1,
				CURLOPT_FORBID_REUSE   => 1,
			);

		return $this->call($url, $opts);
	}

	public function post($url, $data, $options = array())
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
				CURLOPT_USERAGENT      => "AmploCart " . AC_VERSION . " - Curl post request",
				CURLOPT_AUTOREFERER    => true,
				// set referer on redirect
				CURLOPT_CONNECTTIMEOUT => 120,
				// timeout on connect
				CURLOPT_TIMEOUT        => 120,
				// timeout on response
				CURLOPT_MAXREDIRS      => 10,
				// stop after 10 redirects
				CURLOPT_POST           => 1,
				CURLOPT_POSTFIELDS     => $data,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_VERBOSE        => 1,
				CURLOPT_FORBID_REUSE   => 1,
			);

		return $this->call($url, $opts);
	}

	public function call($url, $options)
	{
		//Init Curl
		$ch = curl_init($url);

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

		//Error failed
		if ($this->error) {
			$this->error_log->write("Curl Failed: $this->error");
			return false;
		}

		//Response
		return $this->response;
	}
}
