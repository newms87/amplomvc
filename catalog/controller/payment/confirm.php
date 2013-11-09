<?php
class Catalog_Controller_Payment_Confirm extends Controller
{
	public function index()
	{
		if (!empty($_GET['order_id'])) {
			if (!empty($_GET['code'])) {
				$extension = $this->System_Extension_Payment->get($_GET['code']);

				if (method_exists($extension, 'confirm')) {
					$extension->confirm();
				} else {
					$url_contact = $this->url->link('page/page', 'page_id=' . $this->config->get('config_contact_page_id'));
					$this->message->add('error', _l("We were unable to confirm your order payment. Please <a href=\"%s\">contact us</a> to complete your order.", $url_contact));
					$this->url->redirect('checkout/checkout');
				}
			} else {
				$this->order->update($_GET['order_id'], $this->config->get('config_order_complete_status_id'));
			}
		} else {
			$this->message->add('error', _l("We were unable to find your order your request. Please try completing your order again."));
			$this->url->redirect('checkout/checkout');
		}

		$this->url->redirect('checkout/success');
	}
}
