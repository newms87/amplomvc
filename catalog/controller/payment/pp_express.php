<?php
class Catalog_Controller_Payment_PpExpress extends Controller 
{
	protected function index()
	{
		$this->template->load('payment/pp_express');

		if (!$this->config->get('pp_express_test')) {
			$this->data['action'] = 'https://www.pp_express.com/cgi-bin/webscr';
  		} else {
			$this->data['action'] = 'https://www.sandbox.pp_express.com/cgi-bin/webscr';
		}
		
		$order_info = $this->Model_Checkout_Order->getOrder($this->session->data['order_id']);

		if (!$this->config->get('pp_direct_test')) {
			$api_endpoint = 'https://api-3t.pp.com/nvp';
		} else {
			$api_endpoint = 'https://api-3t.sandbox.pp.com/nvp';
		}

		$this->render();
	}
}