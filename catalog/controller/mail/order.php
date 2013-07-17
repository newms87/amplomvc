<?php
class Catalog_Controller_Mail_Order extends Controller 
{
	public function index($order)
	{
		$order_id = $order['order_id'];
		
		//Order Information
		$this->data = $order;
		
		$this->data['logo'] = $this->image->get($this->config->get('config_logo'));
		$this->data['link'] = $this->url->store($order['store_id'], 'account/order/info', 'order_id=' . $order_id);
		$this->data['date_added'] = $this->date->format($order['date_added'], 'short');
		
		//Language data
		$language = $this->language->fetch('mail/order', $order['language_directory']);
		$language += $this->language->fetch($order['language_filename'], $order['language_directory']);
		
		$language['text_subject'] = sprintf($language['text_subject'], html_entity_decode($order['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);
		$language['text_greeting'] = sprintf($language['text_greeting'], html_entity_decode($order['store_name'], ENT_QUOTES, 'UTF-8'));
		
		$this->data += $language;
		
		//shipping address
		if ($order['shipping_address_format']) {
			$format = $order['shipping_address_format'];
		} else {
			$format = $this->config->get('config_address_format');
		}
		
		$insertables = array(
			'firstname' => $order['shipping_firstname'],
			'lastname'  => $order['shipping_lastname'],
			'company'	=> $order['shipping_company'],
			'address_1' => $order['shipping_address_1'],
			'address_2' => $order['shipping_address_2'],
			'city'		=> $order['shipping_city'],
			'postcode'  => $order['shipping_postcode'],
			'zone'		=> $order['shipping_zone'],
			'zone_code' => $order['shipping_zone_code'],
			'country'	=> $order['shipping_country'],
		);
	
		$this->data['shipping_address'] = $this->tool->insertables($insertables, $format, '{', '}');
		
		//payment address
		if ($order['payment_address_format']) {
			$format = $order['payment_address_format'];
		} else {
			$format = $this->config->get('config_address_format');
		}
		
		$insertables = array(
			'firstname' => $order['payment_firstname'],
			'lastname'  => $order['payment_lastname'],
			'company'	=> $order['payment_company'],
			'address_1' => $order['payment_address_1'],
			'address_2' => $order['payment_address_2'],
			'city'		=> $order['payment_city'],
			'postcode'  => $order['payment_postcode'],
			'zone'		=> $order['payment_zone'],
			'zone_code' => $order['payment_zone_code'],
			'country'	=> $order['payment_country'],
		);
	
		$this->data['payment_address'] = $this->tool->insertables($insertables, $format, '{', '}');
		
		
		// Vouchers
		foreach ($order['order_vouchers'] as &$voucher) {
			$voucher['amount'] = $this->currency->format($voucher['amount'], $order['currency_code'], $order['currency_value']);
		}unset($voucher);
		
		$this->data['order_vouchers'] = $order['order_vouchers'];
		
		//Products
		foreach ($order['order_products'] as &$product) {
			$product['price'] = $this->currency->format($product['price'], $order['currency_code'], $order['currency_value']);
			$product['cost'] = $this->currency->format($product['cost'], $order['currency_code'], $order['currency_value']);
			$product['total'] = $this->currency->format($product['total'], $order['currency_code'], $order['currency_value']);
			
			foreach ($product['option'] as &$option) {
				if (strlen($option['value']) > 22) {
					$option['value'] = substr($option['value'], 0, 20) . '..';
				}
			}unset($option);
		}unset($product);
		
		$this->data['order_products'] = $order['order_products'];
		
		//Totals
		foreach ($order['order_totals'] as &$total) {
			$total['text'] = html_entity_decode($total['text'], ENT_NOQUOTES, 'UTF-8');
		}unset($total);
		
		$this->data['order_totals'] = $order['order_totals'];
		
		//Urls
		$this->data['order_info_url'] = $this->url->store($order['store_id'], 'account/order/info', 'order_id=' . $order_id);
		
		$this->data['downloads_url'] = $order['order_downloads'] ? $this->url->store($order['store_id'], 'account/download') : '';
		
		//Generate HTML email
		$this->template->load('mail/order_html');
		$this->data['shipping_address_html'] = nl2br(htmlentities($this->data['shipping_address']));
		$this->data['payment_address_html'] = nl2br(htmlentities($this->data['payment_address']));
		
		$html = $this->render();
		
		//Generate Text email
		$this->template->load('mail/order_text');
		
		$text = $this->render();
		
		$this->mail->init();
		
		$this->mail->setTo($order['email']);
		$this->mail->setCc($this->config->get('config_email'));
		$this->mail->setFrom($this->config->get('config_email'));
		$this->mail->setSender($order['store_name']);
		$this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
		$this->mail->setHtml($html);
		$this->mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
		
		$this->mail->send();

		// Admin Alert Mail
		if ($this->config->get('config_alert_mail')) {
			$this->template->load('mail/order_text_admin');
			
			$text = $this->render();
			
			$subject = $language->format('text_subject', $this->config->get('config_name'), $order_id);
		
			$this->mail->init();
			
			$this->mail->setTo($this->config->get('config_email'));
			$this->mail->setFrom($this->config->get('config_email'));
			$this->mail->setSender($order['store_name']);
			$this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$this->mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
			$this->mail->send();
			
			//Send additional alert emails
			$this->mail->setTo($this->config->get('config_alert_emails'));
			$this->mail->send();
		}
	}
}