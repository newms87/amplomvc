<?php
class Catalog_Controller_Payment_Cheque extends Controller
{
	protected function index()
	{
		$this->template->load('payment/cheque');

		$this->language->load('payment/cheque');
		
		$this->data['payable'] = $this->config->get('cheque_payable');
		$this->data['address'] = nl2br($this->config->get('config_address'));

		$this->data['continue'] = $this->url->link('checkout/success');

		$this->render();
	}
	
	public function confirm()
	{
		$this->language->load('payment/cheque');
		
		$comment  = $this->_('text_payable') . "\n";
		$comment .= $this->config->get('cheque_payable') . "\n\n";
		$comment .= $this->_('text_address') . "\n";
		$comment .= $this->config->get('config_address') . "\n\n";
		$comment .= $this->_('text_payment') . "\n";
		
		$this->order->update($this->session->data['order_id'], $this->config->get('cheque_order_status_id'), $comment, true);
	}
}