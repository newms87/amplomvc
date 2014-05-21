<?php
class App_Controller_Mail_Voucher extends Controller
{
	public function index($voucher = null)
	{
		//Must have voucher data to send this email
		if (!$voucher) {
			return;
		}

		if (!empty($voucher['order_id'])) {
			$order = $this->order->get($voucher['order_id']);

			$language_id    = $order['language_id'];
			$currency_code  = $order['currency_code'];
			$currency_value = $order['currency_value'];
			$store          = $this->config->getStore($order['store_id']);
		} else {
			$language_id    = !empty($voucher['language_id']) ? $voucher['language_id'] : null; //Use the current language
			$currency_code  = null; //Use default currency
			$currency_value = null;
			$store          = $this->config->getDefaultStore();
		}

		$voucher['message'] = nl2br($voucher['message']);
		$voucher['amount']  = $this->currency->format($voucher['amount']);

		if ($currency_code) {
			$voucher['converted_amount'] = $this->currency->format($voucher['amount'], $currency_code, $currency_value);
		}

		$data += $voucher;

		$data['store_name'] = $store['name'];
		$data['store_url']  = $store['url'];

		$data['from_name']  = $voucher['from_name'];
		$data['redeem_url'] = $this->url->store($store['store_id'], 'common/home');

		$data['image'] = $this->image->get($voucher['image']);

		$image_size = getimagesize($data['image']);

		$data['image_width']  = $image_size[0];
		$data['image_height'] = $image_size[1];

		$this->mail->init();

		$this->mail->setTo($voucher['to_email']);
		$this->mail->setBcc(option('config_email'));
		$this->mail->setFrom($voucher['from_email']);
		$this->mail->setSender($store['name']);
		$this->mail->setSubject(_l("You have been sent a gift voucher from %s", $voucher['from_name']));

		$this->mail->setHtml($this->render('mail/voucher', $data));

		$this->mail->send();
	}
}
