<?php

class App_Controller_Api extends Controller
{
	public function __construct()
	{
		$this->api->authorize(defined('AMPLO_API_AUTH') && AMPLO_API_AUTH);

		parent::__construct();
	}

	public function authenticate()
	{
		$token = $this->api->getToken();

		if (!$token) {
			output_api_error(401, _l("Unable to authenticate the request."));
		} else {
			output_api(array('token' => $token));
		}
	}
}
