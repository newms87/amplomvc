<?php
class Catalog_Controller_Payment_Twocheckout extends Controller 
{
	protected function index()
	{
		$this->template->load('payment/twocheckout');

		$order_info = $this->order->get($this->session->data['order_id']);
		
		$this->data['action'] = 'https://www.2checkout.com/checkout/spurchase';

		$this->data['sid'] = $this->config->get('twocheckout_account');
		$this->data['total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$this->data['cart_order_id'] = $this->session->data['order_id'];
		$this->data['card_holder_name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
		$this->data['street_address'] = $order_info['payment_address_1'];
		$this->data['city'] = $order_info['payment_city'];
		
		if ($order_info['payment_iso_code_2'] == 'US' || $order_info['payment_iso_code_2'] == 'CA') {
			$this->data['state'] = $order_info['payment_zone'];
		} else {
			$this->data['state'] = 'XX';
		}
		
		$this->data['zip'] = $order_info['payment_postcode'];
		$this->data['country'] = $order_info['payment_country'];
		$this->data['email'] = $order_info['email'];
		$this->data['phone'] = $order_info['telephone'];
		
		if ($this->cart->hasShipping()) {
			$this->data['ship_street_address'] = $order_info['shipping_address_1'];
			$this->data['ship_city'] = $order_info['shipping_city'];
			$this->data['ship_state'] = $order_info['shipping_zone'];
			$this->data['ship_zip'] = $order_info['shipping_postcode'];
			$this->data['ship_country'] = $order_info['shipping_country'];
		} else {
			$this->data['ship_street_address'] = $order_info['payment_address_1'];
			$this->data['ship_city'] = $order_info['payment_city'];
			$this->data['ship_state'] = $order_info['payment_zone'];
			$this->data['ship_zip'] = $order_info['payment_postcode'];
			$this->data['ship_country'] = $order_info['payment_country'];
		}
		
		$this->data['products'] = array();
		
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$this->data['products'][] = array(
				'product_id'  => $product['product_id'],
				'name'		=> $product['name'],
				'description' => $product['name'],
				'quantity'	=> $product['quantity'],
				'price'		=> $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value'], false)
			);
		}

		if ($this->config->get('twocheckout_test')) {
			$this->data['demo'] = 'Y';
		} else {
			$this->data['demo'] = '';
		}
		
		$this->data['lang'] = $this->language->code();

		$this->data['return_url'] = $this->url->link('payment/twocheckout/callback');

		$this->render();
	}
	
	public function callback()
	{
		$order_info = $this->order->get($_POST['cart_order_id']);
		
		if (!$this->config->get('twocheckout_test')) {
			$order_number = $_POST['order_number'];
		} else {
			$order_number = '1';
		}
		
		if (strtoupper(md5($this->config->get('twocheckout_secret') . $this->config->get('twocheckout_account') . $order_number . $_POST['total'])) == $_POST['key']) {
			if ($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) == $_POST['total']) {
				$this->order->update($_POST['cart_order_id'], $this->config->get('twocheckout_order_status_id'));
			} else {
				$this->order->update($_POST['cart_order_id'], $this->config->get('config_order_status_id'));// Ugh. Some one've faked the sum. What should we do? Probably drop a mail to the shop owner?
			}
			
			// We can't use $this->url->redirect() here, because of 2CO behavior. It fetches this page
			// on behalf of the user and thus user (and his browser) see this as located at 2checkout.com
			// domain. So user's cookies are not here and he will see empty basket and probably other
			// weird things.
			
			echo '<html>' . "\n";
			echo '<head>' . "\n";
			echo '  <meta http-equiv="Refresh" content="0; url=' . $this->url->link('checkout/success') . '">' . "\n";
			echo '</head>'. "\n";
			echo '<body>' . "\n";
			echo '  <p>Please follow <a href="' . $this->url->link('checkout/success') . '">link</a>!</p>' . "\n";
			echo '</body>' . "\n";
			echo '</html>' . "\n";
			exit();
		} else {
			echo 'The response from 2checkout.com can\'t be parsed. Contact site administrator, please!';
		}
	}
}