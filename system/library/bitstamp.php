<?php
class Bitstamp extends Library
{
	private $api_key = "zAeVsqQOETToatMjjYIvwVyW4OH7CPNt";
	private $secret = "cJ3YY9VfY6HBju18agdqhTBIZDEpklbW";
	private $client_id = "373282";

	public function getDepositAddress()
	{
		$response = $this->curl->post("https://www.bitstamp.net/api/bitcoin_deposit_address/", $this->authData());


		if ($response['errno']) {
			$this->error = $response['errmsg'];
			return false;
		}

		if (preg_match("/[^a-z0-9\"]/i", $response['content'])) {
			$response_data = unserialize($response['content']);

			html_dump($response, 'response');
			html_dump($response_data, 'data');

			return false;
		}

		return str_replace('"', '', $response['content']);
	}

	private function authData()
	{
		$nonce  = time();
		$string = $nonce . $this->client_id . $this->api_key;

		$signature = strtoupper(hash_hmac('sha256', $string, $this->secret));

		$post_data = array(
			'key'       => $this->api_key,
			'signature' => $signature,
			'nonce'     => $nonce,
		);

		return $post_data;
	}
}
