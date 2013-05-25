<?php
class ControllerPaymentFreeCheckout extends Controller {
	protected function index() {
		$this->template->load('payment/free_checkout');

		$this->data['continue'] = $this->url->link('checkout/success');







		$this->render();		
	}
	
	public function confirm() {
		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('free_checkout_order_status_id'));
	}
}