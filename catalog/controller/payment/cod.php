<?php
class Catalog_Controller_Payment_Cod extends Controller
{
	public function index()
	{
		$this->template->load('payment/cod');
		
		$this->data['confirm'] = $this->url->link('payment/cod/confirm', 'order_id=' . $this->order->getId());
		
		$this->render();
	}
	
	public function confirm()
	{
		if (!empty($_GET['order_id'])) {
			$this->order->update($_GET['order_id'], $this->config->get('cod_order_status_id'));
			
			$this->url->redirect($this->url->link('checkout/success'));
		}
		else {
			$this->language->load('payment/cod');
			
			$this->message->add('warning', $this->_('error_cod_confirm', $this->config->get('config_email'), $this->config->get('config_email')));
			
			$this->url->redirect($this->url->link('checkout/checkout'));
		}
		
	}
}
