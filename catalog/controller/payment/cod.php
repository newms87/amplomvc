<?php
class ControllerPaymentCod extends Controller {
	protected function index() {
		$this->template->load('payment/cod');

		$this->data['continue'] = $this->url->link('checkout/success');

		$this->render();
	}
	
	public function confirm() {
		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('cod_order_status_id'));
	}
}
