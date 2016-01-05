<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

class Curl extends Library
{
	private $response;

	const
		RESPONSE_TEXT = 'Text',
		RESPONSE_JSON = 'JSON',
		RESPONSE_DATA = 'Data';

	public function getResponse()
	{
		return $this->response;
	}

	public function get($url, $data = '', $response_type = self::RESPONSE_JSON, $options = array())
	{
		return $this->call(site_url($url, $data), $response_type, $options);
	}

	public function post($url, $data, $response_type = self::RESPONSE_JSON, $options = array())
	{
		$options += array(
			CURLOPT_POST       => 1,
			CURLOPT_POSTFIELDS => http_build_query($data),
		);

		return $this->call($url, $response_type, $options);
	}

	public function saveFile($url, $file)
	{
		$fp = fopen($file, 'wb');

		$options = array(
			CURLOPT_FILE => $fp,
		);

		$response = $this->call($url, self::RESPONSE_DATA, $options);

		fclose($fp);

		return !empty($response);
	}

	public function call($url, $response_type, $options)
	{
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
