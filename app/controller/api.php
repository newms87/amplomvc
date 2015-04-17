<?php

class App_Controller_Api extends Controller
{
	public function __construct()
	{
		if (defined('AMPLO_API_AUTH') && AMPLO_API_AUTH) {
			$this->api->authorize();
		}

		parent::__construct();
	}

	public function authenticate()
	{
		$token = $this->api->getToken();

		if (!$token) {
			header('HTTP/1.1 401 Unauthorized');

			$response = array(
				'status'  => 'error',
				'message' => _l("Unable to authenticate the request."),
			);
		} else {
			$response = array(
				'status' => 'success',
				'token'  => $token,
			);
		}

		output_json($response);
	}
}
