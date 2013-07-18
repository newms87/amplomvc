<?php
class Catalog_Controller_Payment_Cod extends Controller 
{
	public function index()
	{
		$this->template->load('payment/cod');

		$this->data['continue'] = $this->url->link('checkout/success');

		$this->render();
	}
	
	public function confirm()
	{
		$this->order->update($this->session->data['order_id'], $this->config->get('cod_order_status_id'));
	}
}
