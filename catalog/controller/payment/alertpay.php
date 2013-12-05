<?php
class Catalog_Controller_Payment_Alertpay extends Controller
{
	protected function index()
	{
		$this->template->load('payment/alertpay');

		$order_info = $this->order->get($this->session->data['order_id']);

		$this->data['action'] = 'https://www.alertpay.com/PayProcess.aspx';

		$this->data['ap_merchant']     = $this->config->get('alertpay_merchant');
		$this->data['ap_amount']       = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$this->data['ap_currency']     = $order_info['currency_code'];
		$this->data['ap_purchasetype'] = 'Item';
		$this->data['ap_itemname']     = $this->config->get('config_name') . ' - #' . $this->session->data['order_id'];
		$this->data['ap_itemcode']     = $this->session->data['order_id'];
		$this->data['ap_returnurl']    = $this->url->link('checkout/success');
		$this->data['ap_cancelurl']    = $this->url->link('checkout/checkout');

		$this->render();
	}

	public function callback()
	{
		if (isset($_POST['ap_securitycode']) && ($_POST['ap_securitycode'] == $this->config->get('alertpay_security'))) {
			$this->order->updateOrder($_POST['ap_itemcode'], $this->config->get('alertpay_order_status_id'));
		}
	}
}
