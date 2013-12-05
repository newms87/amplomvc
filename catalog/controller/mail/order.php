<?php
class Catalog_Controller_Mail_Order extends Controller
{
	public function index($order)
	{
		$order_id = $order['order_id'];

		//Order Information
		$this->data['logo'] = $this->image->get($this->config->get('config_logo'));
		$this->data['link'] = $this->url->store($order['store_id'], 'account/order/info', 'order_id=' . $order_id);

		$order['date_added'] = $this->date->format($order['date_added'], 'short');

		//Shipping / Payment addresses
		$order['payment_address']  = $this->address->format($this->order->extractPaymentAddress($order));
		$order['shipping_address'] = $this->address->format($this->order->extractShippingAddress($order));

		//Shipping / Payment Methods
		$order['payment_method']  = $this->cart->getPaymentMethod($order['payment_method_id'])->info();
		$order['shipping_method'] = $this->cart->getShippingMethod($order['shipping_method_id']);

		// Vouchers
		foreach ($order['order_vouchers'] as &$voucher) {
			$voucher['amount'] = $this->currency->format($voucher['amount'], $order['currency_code'], $order['currency_value']);
		}
		unset($voucher);

		//Products
		foreach ($order['order_products'] as &$product) {
			$product['price'] = $this->currency->format($product['price'], $order['currency_code'], $order['currency_value']);
			$product['cost']  = $this->currency->format($product['cost'], $order['currency_code'], $order['currency_value']);
			$product['total'] = $this->currency->format($product['total'], $order['currency_code'], $order['currency_value']);

			$product += $this->Model_Catalog_Product->getProduct($product['product_id']);
		}
		unset($product);

		//Totals
		foreach ($order['order_totals'] as &$total) {
			$total['text'] = $this->currency->format($total['value'], $order['currency_code'], $order['currency_value']);
		}
		unset($total);

		//Urls
		$this->data['order_info_url'] = $this->url->store($order['store_id'], 'account/order/info', 'order_id=' . $order_id);

		if (!empty($order['order_downloads'])) {
			$this->data['downloads_url'] = $this->url->store($order['store_id'], 'account/download');
		}

		$this->data += $order;

		$store = $this->config->getStore($order['store_id']);
		$this->data['store'] = $store;

		$this->mail->init();

		$subject =_l('%s - Order %s', $store['name'], $order_id);

		if (empty($order['email'])) {
			$order['email'] = $this->config->get('config_email_error');
			$subject .= " (No Order Email was found!)";
		}

		$this->mail->setTo($order['email']);
		$this->mail->setCc($this->config->get('config_email'));
		$this->mail->setFrom($this->config->get('config_email'));
		$this->mail->setSender($store['name']);
		$this->mail->setSubject($subject);

		//Text Email
		$this->template->load('mail/order_text');
		$this->mail->setText(html_entity_decode($this->render(), ENT_QUOTES, 'UTF-8'));

		//HTML email
		//TODO: Need to verify that these will always have line breaks!! use nl2br() if not...
		$this->data['shipping_address_html'] = $this->data['shipping_address'];
		$this->data['payment_address_html']  = $this->data['payment_address'];

		$this->template->load('mail/order_html');
		$this->mail->setHtml($this->render());

		$this->mail->send();


		// Admin Alert Mail
		if ($this->config->get('config_alert_mail')) {
			$this->template->load('mail/order_text_admin');

			$to = $this->config->get('config_email');

			if ($this->config->get('config_alert_emails')) {
				$to .= ',' . $this->config->get('config_alert_emails');
			}

			$this->mail->init();

			$this->mail->setTo($to);
			$this->mail->setFrom($this->config->get('config_email'));
			$this->mail->setSender($store['name']);
			$this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$this->mail->setText(html_entity_decode($this->render(), ENT_QUOTES, 'UTF-8'));
			$this->mail->send();
		}
	}
}
