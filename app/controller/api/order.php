<?php

class App_Controller_Api_Order extends App_Controller_Api
{
	public function __construct()
	{
		$this->api->authorize();

		parent::__construct();
	}

	public function get()
	{
		message('status', 'success');
		message('message', "Hey There");
		
		output_message();
	}
}
