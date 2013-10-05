<?php
class Curl extends Library
{
	private $error;
	private $response;

	public function getError()
	{
		return $this->error;
	}

	public function getResponse()
	{
		return $this->response;
	}

	public function post($url, $data, $options = array())
	{
		$opts = array(
			CURLOPT_RETURNTRANSFER => true,		// return web page
			CURLOPT_HEADER         => false,		// don't return headers
			CURLOPT_FOLLOWLOCATION => true,		// follow redirects
			CURLOPT_ENCODING       => "",		   // handle all encodings
			CURLOPT_USERAGENT      => "spider",	// who am i
			CURLOPT_AUTOREFERER    => true,		// set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 120,		// timeout on connect
			CURLOPT_TIMEOUT        => 120,		// timeout on response
			CURLOPT_MAXREDIRS      => 10,		   // stop after 10 redirects
			CURLOPT_POST           => 1,
			CURLOPT_POSTFIELDS     => $data,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_VERBOSE        => 1,
			CURLOPT_FORBID_REUSE   => 1,
		);

		if ($options) {
			$opts = $options + $opts;
		}

		//Init Curl
		$ch = curl_init($url);

		if (!$ch) {
			$this->error = $this->_('error_curl_init');
			return false;
		}

		//Set Options
		if (!curl_setopt_array($ch, $opts)) {
			$this->error = $this->_('error_curl_setopt');
			return false;
		}

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

		//Error failed
		if ($this->error) {
			$this->error_log->write("Curl Failed: $this->error");
			return false;
		}

		//Response
		return $this->response;
	}
}