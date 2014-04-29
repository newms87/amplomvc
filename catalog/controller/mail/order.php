<?php
class Catalog_Controller_Mail_Order extends Controller
{
	public function index($order)
	{
		$order_id = $order['order_id'];

		//Order Information
		$data['logo'] = $this->image->get(option('config_logo'));
		$data['link'] = $this->url->store($order['store_id'], 'account/order/info', 'order_id=' . $order_id);

		$order['date_added'] = $this->date->format($order['date_added'], 'short');

		//Shipping / Payment addresses
		$order['payment_address']  = $this->address->format($this->order->getPaymentAddress($order_id));
		$order['shipping_address'] = $this->address->format($this->order->getShippingAddress($order_id));

		//Shipping / Payment Methods
		$order['payment_method']  = $this->System_Extension_Payment->get($order['transaction']['payment_code'])->info();
		$order['shipping_method'] = $this->System_Extension_Shipping->get($order['shipping']['shipping_code'])->info();

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
		$data['order_info_url'] = $this->url->store($order['store_id'], 'account/order/info', 'order_id=' . $order_id);

		if (!empty($order['order_downloads'])) {
			$data['downloads_url'] = $this->url->store($order['store_id'], 'account/download');
		}

		$data += $order;

		$store               = $this->config->getStore($order['store_id']);
		$data['store'] = $store;

		$this->mail->init();

		$subject = _l('%s - Order %s', $store['name'], $order_id);

		if (empty($order['email'])) {
			$order['email'] = option('config_email_error');
			$subject .= " (No Order Email was found!)";
		}

		$this->mail->setTo($order['email']);
		$this->mail->setCc(option('config_email'));
		$this->mail->setFrom(option('config_email'));
		$this->mail->setSender($store['name']);
		$this->mail->setSubject($subject);

		$this->mail->setText(html_entity_decode($this->render('mail/order_text', $data), ENT_QUOTES, 'UTF-8'));

		//HTML email
		//TODO: Need to verify that these will always have line breaks!! use nl2br() if not...
		$data['shipping_address_html'] = $data['shipping_address'];
		$data['payment_address_html']  = $data['payment_address'];

		$this->mail->setHtml($this->render('mail/order_html', $data));

		$this->mail->send();

		// Admin Alert Mail
		if (option('config_alert_mail')) {
			$to = option('config_email');

			if (option('config_alert_emails')) {
				$to .= ',' . option('config_alert_emails');
			}

			$this->mail->init();

			$this->mail->setTo($to);
			$this->mail->setFrom(option('config_email'));
			$this->mail->setSender($store['name']);
			$this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$this->mail->setText(html_entity_decode($this->render('mail/order_text_admin', $data), ENT_QUOTES, 'UTF-8'));
			$this->mail->send();
		}
	}
}
