<?php

class App_Controller_Api_Order extends App_Controller_Api
{
	public function __construct()
	{
		$this->api->authorize();
	}

	public function get()
	{

	}
}
