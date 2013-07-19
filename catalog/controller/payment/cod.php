<?php
class Catalog_Controller_Payment_Cod extends Controller 
{
	public function index()
	{
		$this->template->load('payment/cod');

		$this->data['continue'] = $this->url->link('payment/cod/confirm');
		
		$this->render();
	}
	
	public function confirm()
	{
		$this->order->update($this->order->getId(), $this->config->get('cod_order_status_id'));
		
		$this->url->redirect($this->url->link('checkout/success'));
	}
}
