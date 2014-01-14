<?php
class Catalog_Controller_Mail_OrderUpdateNotify extends Controller
{
	public function index($comment, $order_status_id, $order)
	{
		//TODO: Need to implement changing language / locale

		$store = $this->config->getStore($order['store_id']);

		$subject = _l("%s - Order Update %s", html_entity_decode($store['name'], ENT_QUOTES, 'UTF-8'), $order['order_id']);

		$message = _l("Order ID: ") . $order['order_id'] . "\n";
		$message .= _l("Date Added: ") . $this->date->format($order['date_added'], 'short') . "\n\n";

		$order_status_from = $this->order->getOrderStatus($order['order_status_id']);
		$order_status_to   = $this->order->getOrderStatus($order_status_id);

		if ($order_status_to) {
			$message .= _l("Your Order Status has been updated from %s to %s. ", $order_status_from['title'], $order_status_to['title']) . "\n\n";
		}

		if ($order['customer_id']) {
			$message .= _l("To view your order click on the link below:\n");
			$message .= $this->url->store($order['store_id'], 'account/order/info', 'order_id=' . $order['order_id']) . "\n\n";
		}

		if ($comment) {
			$message .= _l("Comments:\n\n");
			$message .= $comment . "\n\n";
		}

		$message .= _l("Please reply to this email if you have any questions.");

		$this->mail->init();

		$this->mail->setTo($order['email']);
		$this->mail->setFrom($this->config->get('config_email'));
		$this->mail->setSender($store['name']);
		$this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
		$this->mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));

		$this->mail->send();
	}
}
