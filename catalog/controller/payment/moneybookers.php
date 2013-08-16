<?php
class Catalog_Controller_Payment_Moneybookers extends Controller
{
	protected function index()
	{
		$this->template->load('payment/moneybookers');

		$this->language->load('payment/moneybookers');

		$this->data['action'] = 'https://www.moneybookers.com/app/payment.pl?p=OpenCart';

		$this->data['pay_to_email']   = $this->config->get('moneybookers_email');
		$this->data['description']    = $this->config->get('config_name');
		$this->data['transaction_id'] = $this->session->data['order_id'];
		$this->data['return_url']     = $this->url->link('checkout/success');
		$this->data['cancel_url']     = $this->url->link('checkout/checkout');
		$this->data['status_url']     = $this->url->link('payment/moneybookers/callback');
		$this->data['language']       = $this->language->code();
		$this->data['logo']           = HTTP_IMAGE . $this->config->get('config_logo');

		$order_info = $this->order->get($this->session->data['order_id']);

		$this->data['pay_from_email'] = $order_info['email'];
		$this->data['firstname']      = $order_info['payment_firstname'];
		$this->data['lastname']       = $order_info['payment_lastname'];
		$this->data['address']        = $order_info['payment_address_1'];
		$this->data['address2']       = $order_info['payment_address_2'];
		$this->data['phone_number']   = $order_info['telephone'];
		$this->data['postal_code']    = $order_info['payment_postcode'];
		$this->data['city']           = $order_info['payment_city'];
		$this->data['state']          = $order_info['payment_zone'];
		$this->data['country']        = $order_info['payment_iso_code_3'];
		$this->data['amount']         = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$this->data['currency']       = $order_info['currency_code'];

		$products = '';

		foreach ($this->cart->getProducts() as $product) {
			$products .= $product['quantity'] . ' x ' . $product['name'] . ', ';
		}

		$this->data['detail1_text'] = $products;

		$this->data['order_id'] = $this->encryption->encrypt($this->session->data['order_id']);

		$this->render();
	}

	public function callback()
	{
		if (isset($_POST['order_id'])) {
			$order_id = $this->encryption->decrypt($_POST['order_id']);
		} else {
			$order_id = 0;
		}

		$order_info = $this->order->get($order_id);

		if ($order_info) {
			$this->order->update($order_id, $this->config->get('config_order_complete_status_id'));

			$verified = true;

			// md5sig validation
			if ($this->config->get('moneybookers_secret')) {
				$hash = $_POST['merchant_id'];
				$hash .= $_POST['transaction_id'];
				$hash .= strtoupper(md5($this->config->get('moneybookers_secret')));
				$hash .= $_POST['mb_amount'];
				$hash .= $_POST['mb_currency'];
				$hash .= $_POST['status'];

				$md5hash = strtoupper(md5($hash));
				$md5sig  = $_POST['md5sig'];

				if ($md5hash != $md5sig) {
					$verified = false;
				}
			}

			if ($verified) {
				switch ($_POST['status']) {
					case '2':
						$this->Model_Checkout_Order->update_order($order_id, $this->config->get('moneybookers_order_status_id'), '', true);
						break;
					case '0':
						$this->Model_Checkout_Order->update_order($order_id, $this->config->get('moneybookers_pending_status_id'), '', true);
						break;
					case '-1':
						$this->Model_Checkout_Order->update_order($order_id, $this->config->get('moneybookers_canceled_status_id'), '', true);
						break;
					case '-2':
						$this->Model_Checkout_Order->update_order($order_id, $this->config->get('moneybookers_failed_status_id'), '', true);
						break;
					case '-3':
						$this->Model_Checkout_Order->update_order($order_id, $this->config->get('moneybookers_chargeback_status_id'), '', true);
						break;
				}
			} else {
				$this->error_log->write('md5sig returned (' + $md5sig + ') does not match generated (' + $md5hash + '). Verify Manually. Current order state: ' . $this->config->get('config_order_complete_status_id'));
			}
		}
	}
}
