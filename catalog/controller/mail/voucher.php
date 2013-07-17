<?php
class Catalog_Controller_Mail_Voucher extends Controller 
{
	public function index($voucher = null)
	{
		//Must have voucher data to send this email
		if (!$voucher) return;
		
		$this->template->load('mail/voucher');
		
		if (!empty($voucher['order_id'])) {
			$order = $this->order->get($voucher['order_id']);
			
			$language_id = $order['language_id'];
			$currency_code = $order['currency_code'];
			$currency_value = $order['currency_value'];
			$store = $this->config->getStore($order['store_id']);
		} else {
			$language_id = !empty($voucher['language_id']) ? $voucher['language_id'] : null; //Use the current language
			$currency_code = null; //Use default currency
			$currency_value = null;
			$store = $this->config->getDefaultStore();
		}
		
		$this->language->loadTemporary('mail/voucher', $language_id);
		
		$voucher['message'] = nl2br($voucher['message']);
		$voucher['amount'] = $this->currency->format($voucher['amount']);
		
		if ($currency_code) {
			$voucher['converted_amount'] = $this->currency->format($voucher['amount'], $currency_code, $currency_value);
		}
		
		$this->data += $voucher;
		
		$this->data['store_name'] = $store['name'];
		$this->data['store_url'] = $store['url'];
		
		$this->data['text_subject'] = $this->_('text_subject', $voucher['from_name']);
		$this->data['text_from'] = $this->_('text_from', $voucher['from_name']);
		$this->data['text_redeem'] = $this->_('text_redeem', $this->url->store($store['store_id'], 'common/home'), $store['name']);
		
		$this->data['image'] = $this->image->get($voucher['image']);
		
		$image_size = getimagesize($this->data['image']);
		
		$this->data['image_width'] = $image_size[0];
		$this->data['image_height'] = $image_size[1];
		
		$this->mail->init();
		
		$this->mail->setTo($voucher['to_email']);
		$this->mail->setBcc($this->config->get('config_email'));
		$this->mail->setFrom($voucher['from_email']);
		$this->mail->setSender($store['name']);
		$this->mail->setSubject(html_entity_decode($this->_('text_subject', $voucher['from_name']), ENT_QUOTES, 'UTF-8'));
		
		$this->mail->setHtml($this->render());
		
		$this->mail->send();
		
		$this->language->unloadTemporary();
	}
}