<?php
class Catalog_Controller_Mail_OrderUpdateNotify extends Controller 
{
	public function index($order_info)
	{
			$language = $this->language->fetch('mail/order', $order['language_directory']);
			$language += $this->language->fetch($order['language_filename'], $order['language_directory']);
			
			$subject = sprintf($language['text_update_subject'], html_entity_decode($order['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);

			$message  = $language['text_update_order'] . ' ' . $order_id . "\n";
			$message .= $language['text_update_date_added'] . ' ' . $this->date->format($order['date_added'], $language['date_format_short']) . "\n\n";
			
			$order_status_query = $this->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order['language_id'] . "'");
			
			if ($order_status_query->num_rows) {
				$message .= $language['text_update_order_status'] . "\n\n";
				$message .= $order_status_query->row['name'] . "\n\n";
			}
			
			if ($order['customer_id']) {
				$message .= $language['text_update_link'] . "\n";
				$message .= $order['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id . "\n\n";
			}
			
			if ($comment) {
				$message .= $language['text_update_comment'] . "\n\n";
				$message .= $comment . "\n\n";
			}
				
			$message .= $language['text_update_footer'];
			
			$this->mail->setTo($order['email']);
			$this->mail->setFrom($this->config->get('config_email'));
			$this->mail->setSender($order['store_name']);
			$this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$this->mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
	}
}