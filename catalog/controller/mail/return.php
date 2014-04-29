<?php
class Catalog_Controller_Mail_Return extends Controller
{
	public function index($return_data)
	{
		$rmas = array_column($return_data['return_products'], 'rma');

		$data['rmas'] = $rmas;

		$order = $this->order->get($return_data['order_id']);
		$data['order'] = $order;

		$data['store'] = $this->config->getStore($order['store_id']);
		$data['logo'] = $this->image->get(option('config_logo'));

		//Send Customer Confirmation Email
		$this->mail->init();

		$this->mail->setTo($return_data['email']);
		$this->mail->setCc(option('config_email'));
		$this->mail->setFrom(option('config_email'));
		$this->mail->setSender(option('config_name'));
		$this->mail->setSubject(_l("Your return request has been submitted!"));

		$this->mail->setHtml($this->render('mail/return_html', $data));

		$this->mail->send();

		//Send Admin Notification Email
		$this->mail->init();

		$this->mail->setTo(option('config_email'));
		$this->mail->setFrom(option('config_email'));
		$this->mail->setSender(option('config_name'));
		$this->mail->setSubject(_l("A product return request has been received!"));

		$html = _l("Please review the return request for order ID (%s) and notify the customer if their product is eligible for a return.", $return_data['order_id']);

		if (!empty($rmas)) {
			$html .= _l("<br /><br />Product RMA(s):<br />%s", implode(', ', $rmas));
		}

		$this->mail->setHtml($html);

		$this->mail->send();
	}
}
