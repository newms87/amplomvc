<?php
class Catalog_Controller_Payment_Paypoint extends Controller
{
	protected function index()
	{
		$this->template->load('payment/paypoint');

		$order_info = $this->order->get($this->session->data['order_id']);

		$this->data['merchant'] = $this->config->get('paypoint_merchant');
		$this->data['trans_id'] = $this->session->data['order_id'];
		$this->data['amount']   = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);

		if ($this->config->get('paypoint_password')) {
			$this->data['digest'] = md5($this->session->data['order_id'] . $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) . $this->config->get('paypoint_password'));
		} else {
			$this->data['digest'] = '';
		}

		$this->data['bill_name']      = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
		$this->data['bill_addr_1']    = $order_info['payment_address_1'];
		$this->data['bill_addr_2']    = $order_info['payment_address_2'];
		$this->data['bill_city']      = $order_info['payment_city'];
		$this->data['bill_state']     = $order_info['payment_zone'];
		$this->data['bill_post_code'] = $order_info['payment_postcode'];
		$this->data['bill_country']   = $order_info['payment_country'];
		$this->data['bill_tel']       = $order_info['telephone'];
		$this->data['bill_email']     = $order_info['email'];

		if ($this->cart->hasShipping()) {
			$this->data['ship_name']      = $order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname'];
			$this->data['ship_addr_1']    = $order_info['shipping_address_1'];
			$this->data['ship_addr_2']    = $order_info['shipping_address_2'];
			$this->data['ship_city']      = $order_info['shipping_city'];
			$this->data['ship_state']     = $order_info['shipping_zone'];
			$this->data['ship_post_code'] = $order_info['shipping_postcode'];
			$this->data['ship_country']   = $order_info['shipping_country'];
		} else {
			$this->data['ship_name']      = '';
			$this->data['ship_addr_1']    = '';
			$this->data['ship_addr_2']    = '';
			$this->data['ship_city']      = '';
			$this->data['ship_state']     = '';
			$this->data['ship_post_code'] = '';
			$this->data['ship_country']   = '';
		}

		$this->data['currency'] = $this->currency->getCode();
		$this->data['callback'] = $this->url->link('payment/paypoint/callback');

		switch ($this->config->get('paypoint_test')) {
			case 'live':
				$status = 'live';
				break;
			case 'successful':
			default:
				$status = 'true';
				break;
			case 'fail':
				$status = 'false';
				break;
		}

		$this->data['options'] = 'test_status=' . $status . ',dups=false,cb_post=false';

		$this->render();
	}

	public function callback()
	{
		if (isset($_GET['trans_id'])) {
			$order_id = $_GET['trans_id'];
		} else {
			$order_id = 0;
		}

		$order_info = $this->order->get($order_id);

		// Validate the request is from PayPoint
		if ($this->config->get('paypoint_password')) {
			if (!empty($_GET['hash'])) {
				$status = ($_GET['hash'] == str_replace('&hash=' . $_GET['hash'], '', $_SERVER['REQUEST_URI']) . '&' . $this->config->get('paypoint_password'));
			} else {
				$status = false;
			}
		} else {
			$status = true;
		}

		if ($order_info && $status) {
			$this->language->load('payment/paypoint');

			$this->data['title'] = $this->_('head_title', $this->config->get('config_name'));

			$this->data['base'] = $this->url->is_ssl() ? SITE_SSL : SITE_URL;

			$this->language->set('language', $this->language->getInfo('code'));
			$this->_('head_title', $this->config->get('config_name'));

			$this->_('text_success_wait', $this->url->link('checkout/success'));
			$this->_('text_failure_wait', $this->url->link('cart/cart'));

			if (isset($_GET['code']) && $_GET['code'] == 'A') {
				$this->template->load('payment/paypoint_success');

				$this->order->updateOrder($_GET['trans_id'], $this->config->get('config_order_complete_status_id'));

				$message = '';

				if (isset($_GET['code'])) {
					$message .= 'code: ' . $_GET['code'] . "\n";
				}

				if (isset($_GET['auth_code'])) {
					$message .= 'auth_code: ' . $_GET['auth_code'] . "\n";
				}

				if (isset($_GET['ip'])) {
					$message .= 'ip: ' . $_GET['ip'] . "\n";
				}

				if (isset($_GET['cv2avs'])) {
					$message .= 'cv2avs: ' . $_GET['cv2avs'] . "\n";
				}

				if (isset($_GET['valid'])) {
					$message .= 'valid: ' . $_GET['valid'] . "\n";
				}

				$this->Model_Checkout_Order->update_order($order_id, $this->config->get('paypoint_order_status_id'), $message, false);

				$this->data['continue'] = $this->url->link('checkout/success');

				$this->children = array(
					'common/column_left',
					'common/column_right',
					'common/content_top',
					'common/content_bottom',
					'common/footer',
					'common/header'
				);

				$this->response->setOutput($this->render());
			} else {
				$this->template->load('payment/paypoint_failure');

				$this->data['continue'] = $this->url->link('cart/cart');

				$this->children = array(
					'common/column_left',
					'common/column_right',
					'common/content_top',
					'common/content_bottom',
					'common/footer',
					'common/header'
				);

				$this->response->setOutput($this->render());
			}
		}
	}
}
