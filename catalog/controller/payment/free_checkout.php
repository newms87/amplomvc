<?php
class Catalog_Controller_Payment_FreeCheckout extends Controller
{
	protected function index()
	{
		$this->template->load('payment/free_checkout');

		$this->data['continue'] = $this->url->link('checkout/success');

		$this->render();
	}
	
	public function confirm()
	{
		$this->Model_Checkout_Order->confirm($this->session->data['order_id'], $this->config->get('free_checkout_order_status_id'));
	}
}