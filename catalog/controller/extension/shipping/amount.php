<?php

class Catalog_Controller_Extension_Shipping_Amount extends Controller
{
	private $settings;

	public function __construct()
	{
		parent::__construct();

		$this->settings = $this->System_Extension_Shipping_Amount->settings();
	}

	public function index()
	{
		$settings = $this->settings;

		$settings['quotes'] = $this->System_Extension_Shipping_Amount->getQuotes($this->cart->getShippingAddress());

		$settings['shipping_key'] = $this->cart->getShippingKey();

		//Render
		$this->render('extension/shipping/amount/amount', $settings);
	}
}
