<?php
class Catalog_Controller_Payment_Liqpay extends Controller
{
	protected function index()
	{
		$this->template->load('payment/liqpay');

		$order_info = $this->Model_Checkout_Order->getOrder($this->session->data['order_id']);
		
		$this->data['action'] = 'https://liqpay.com/?do=clickNbuy';
		
		$xml  = '<request>';
		$xml .= '	<version>1.2</version>';
		$xml .= '	<result_url>' . $this->url->link('checkout/success') . '</result_url>';
		$xml .= '	<server_url>' . $this->url->link('payment/liqpay/callback') . '</server_url>';
		$xml .= '	<merchant_id>' . $this->config->get('liqpay_merchant') . '</merchant_id>';
		$xml .= '	<order_id>' . $this->session->data['order_id'] . '</order_id>';
		$xml .= '	<amount>' . $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) . '</amount>';
		$xml .= '	<currency>' . $order_info['currency_code'] . '</currency>';
		$xml .= '	<description>' . $this->config->get('config_name') . ' ' . $order_info['payment_firstname'] . ' ' . $order_info['payment_address_1'] . ' ' . $order_info['payment_address_2'] . ' ' . $order_info['payment_city'] . ' ' . $order_info['email'] . '</description>';
		$xml .= '	<default_phone></default_phone>';
		$xml .= '	<pay_way>' . $this->config->get('liqpay_type') . '</pay_way>';
		$xml .= '</request>';
		
		$this->data['xml'] = base64_encode($xml);
		$this->data['signature'] = base64_encode(sha1($this->config->get('liqpay_signature') . $xml . $this->config->get('liqpay_signature'), true));

		$this->render();
	}

	public function callback()
	{
		$xml = base64_decode($_POST['operation_xml']);
		$signature = base64_encode(sha1($this->config->get('liqpay_signature') . $xml . $this->config->get('liqpay_signature'), true));
		
		$posleft = strpos($xml, 'order_id');
		$posright = strpos($xml, '/order_id');
		
		$order_id = substr($xml, $posleft + 9, $posright - $posleft - 10);
		
		if ($signature == $_POST['signature']) {
			$this->Model_Checkout_Order->confirm($order_id, $this->config->get('config_order_status_id'));
		}
	}
}