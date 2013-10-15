<?php
class Catalog_Controller_Payment_Confirm extends Controller
{
	public function index()
	{
		$this->language->system('payment/payment');

		if (!empty($_GET['order_id'])) {
			if (!empty($_GET['code'])) {
				$extension = $this->System_Extension_Payment->get($_GET['code']);

				if (method_exists($extension, 'confirm')) {
					$extension->confirm();
				} else {
					$this->message->add('error', $this->_('error_method', $this->config->get('config_email'), $this->config->get('config_email')));
					$this->url->redirect($this->url->link('checkout/checkout'));
				}
			} else {
				$this->order->update($_GET['order_id'], $this->config->get('config_order_complete_status_id'));
			}
		} else {
			$this->language->load('payment/payment');

			$this->message->add('error', $this->_('error_confirm', $this->config->get('config_email'), $this->config->get('config_email')));
			$this->url->redirect($this->url->link('checkout/checkout'));
		}

		$this->url->redirect($this->url->link('checkout/success'));
	}
}
