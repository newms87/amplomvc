<?php
class Catalog_Controller_Payment_Paymate extends Controller
{
	protected function index()
	{
		$this->template->load('payment/paymate');

		if (!$this->config->get('paymate_test')) {
			$this->data['action'] = 'https://www.paymate.com/PayMate/ExpressPayment';
		} else {
			$this->data['action'] = 'https://www.paymate.com.au/PayMate/TestExpressPayment';
		}
		
		$order_info = $this->order->get($this->session->data['order_id']);
				
		$this->data['mid'] = $this->config->get('paymate_username');
		$this->data['amt'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		
		$this->data['currency'] = $order_info['currency_code'];
		$this->data['ref'] = $order_info['order_id'];
		
		$this->data['pmt_sender_email'] = $order_info['email'];
		$this->data['pmt_contact_firstname'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
		$this->data['pmt_contact_surname'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
		$this->data['pmt_contact_phone'] = $order_info['telephone'];
		$this->data['pmt_country'] = $order_info['payment_iso_code_2'];
		
		$this->data['regindi_address1'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
		$this->data['regindi_address2'] = html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');
		$this->data['regindi_sub'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
		$this->data['regindi_state'] = html_entity_decode($order_info['payment_zone'], ENT_QUOTES, 'UTF-8');
		$this->data['regindi_pcode'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
		
		$this->data['return'] = $this->url->link('payment/paymate/callback', 'hash=' . md5($order_info['order_id'] . $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) . $order_info['currency_code'] . $this->config->get('paymate_password')));

		$this->render();
	}
	
	public function callback()
	{
		$this->language->load('payment/paymate');
		
		if (isset($_POST['ref'])) {
			$order_id = $_POST['ref'];
		} else {
			$order_id = 0;
		}
		
		$order_info = $this->order->get($order_id);
		
		if ($order_info) {
			$error = '';
			
			if (!isset($_POST['responseCode']) || !isset($_GET['hash'])) {
				$error = $this->_('text_unable');
			} elseif ($_GET['hash'] != md5($order_info['order_id'] . $this->currency->format($_POST['paymentAmount'], $_POST['currency'], 1.0000000, false) . $_POST['currency'] . $this->config->get('paymate_password'))) {
				$error = $this->_('text_unable');
			} elseif ($_POST['responseCode'] != 'PA' && $_POST['responseCode'] != 'PP') {
				$error = $this->_('text_declined');
			}
		} else {
			$error = $this->_('text_unable');
		}
		
		if ($error) {
		$this->template->load('common/success');

				$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
				$this->breadcrumb->add($this->_('text_basket'), $this->url->link('cart/cart'));
				$this->breadcrumb->add($this->_('text_checkout'), $this->url->link('checkout/checkout'));
				$this->breadcrumb->add($this->_('text_failed'), $this->url->link('checkout/success'));

			$this->language->set('head_title', $this->_('text_failed'));

			$this->data['text_message'] = $this->_('text_failed_message', $error, $this->url->link('information/contact'));
			
			$this->data['continue'] = $this->url->link('common/home');

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
			$this->order->update($order_id, $this->config->get('paymate_order_status_id'));
			
			$this->url->redirect($this->url->link('checkout/success'));
		}
	}
}