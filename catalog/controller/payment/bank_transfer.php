<?php
class Catalog_Controller_Payment_BankTransfer extends Controller
{
	protected function index()
	{
		$this->template->load('payment/bank_transfer');

		$this->language->load('payment/bank_transfer');

		$this->data['bank'] = nl2br($this->config->get('bank_transfer_bank_' . $this->config->get('config_language_id')));

		$this->data['continue'] = $this->url->link('checkout/success');

		$this->render();
	}

	public function confirm()
	{
		$this->language->load('payment/bank_transfer');

		$comment = $this->_('text_instruction') . "\n\n";
		$comment .= $this->config->get('bank_transfer_bank_' . $this->config->get('config_language_id')) . "\n\n";
		$comment .= $this->_('text_payment');

		$this->order->update($this->session->data['order_id'], $this->config->get('bank_transfer_order_status_id'), $comment, true);
	}
}
