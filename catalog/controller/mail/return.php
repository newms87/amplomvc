<?php
class Catalog_Controller_Mail_Return extends Controller
{
	public function index($return_data)
	{
		$rmas = array_column_recursive($return_data['return_products'], 'rma');

		$this->data['rmas'] = $rmas;

		$order = $this->order->get($return_data['order_id']);
		$this->data['order'] = $order;

		$this->data['store'] = $this->config->getStore($order['store_id']);
		$this->data['logo'] = $this->image->get($this->config->get('config_logo'));

		//Send Customer Confirmation Email
		$this->mail->init();

		$this->mail->setTo($return_data['email']);
		$this->mail->setCc($this->config->get('config_email'));
		$this->mail->setFrom($this->config->get('config_email'));
		$this->mail->setSender($this->config->get('config_name'));
		$this->mail->setSubject(_l("Your return request has been submitted!"));

		$this->template->load('mail/return_html');
		$this->mail->setHtml($this->render());

		$this->mail->send();

		//Send Admin Notification Email
		$this->mail->init();

		$this->mail->setTo($this->config->get('config_email'));
		$this->mail->setFrom($this->config->get('config_email'));
		$this->mail->setSender($this->config->get('config_name'));
		$this->mail->setSubject(_l("A product return request has been received!"));

		$html = _l("Please review the return request for order ID (%s) and notify the customer if their product is eligible for a return.", $return_data['order_id']);

		if (!empty($rmas)) {
			$html .= _l("<br /><br />Product RMA(s):<br />%s", implode(', ', $rmas));
		}

		$this->mail->setHtml($html);

		$this->mail->send();
	}
}
