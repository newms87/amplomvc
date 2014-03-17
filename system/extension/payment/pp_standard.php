<?php
class System_Extension_Payment_PpStandard extends System_Extension_Payment
{
	public function __construct($registry)
	{
		parent::__construct($registry);

		//TODO: This is a hack. Find a better way to integrate this.
		if (!empty($this->settings['button_graphic'])) {
			$this->info['title'] = "<img src=\"{$this->settings['button_graphic']}\" border=\"0\" alt=\"Paypal\" />";
		}
	}


	public function subscribe()
	{
		//TODO: implement this!
		echo "This is not implemented yet!";
		exit;

		$this->config->loadGroup('pp_standard');

		$testmode = $this->settings['test'];

		if ($testmode) {
			$this->data['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

			if ($this->settings['test_email']) {
				$this->data['business'] = $this->settings['test_email'];
			} else {
				$this->data['business'] = $this->settings['email'];
			}
		} else {
			$this->data['action']   = 'https://www.paypal.com/cgi-bin/webscr';
			$this->data['business'] = $this->settings['email'];
		}

		$subscription = $this->subscription->getCartSubscription();

		if ($subscription) {
			$this->view->load('payment/pp_standard_subscribe');

			$this->data['order_id']  = $subscription['order_id'];
			$this->data['item_name'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

			$products = $this->cart->getProducts();

			foreach ($products as &$product) {
				foreach ($product['selected_options'] as &$selected_option) {
					$selected_option['product_option'] = $this->Model_Catalog_Product->getProductOption($product['product_id'], $selected_option['product_option_id']);
					$selected_option['value']          = $this->tool->limit_characters($selected_option['value'], 20);
				}
				unset($product_option);

				$product['price'] = $this->currency->format($product['price'], $subscription['currency_code'], false, false);
			}
			unset($product);

			$this->data['subscriptions'] = $products;

			$this->data['discount_amount_cart'] = 0;

			$total = $this->currency->format($subscription['total'] - $this->cart->getSubTotal(), $subscription['currency_code'], false, false);

			if ($total > 0) {
				$this->data['products'][] = array(
					'name'     => _l("Shipping, Handling, Discounts & Taxes"),
					'model'    => '',
					'price'    => $total,
					'quantity' => 1,
					'weight'   => 0
				);
			} else {
				$this->data['discount_amount_cart'] -= $total;
			}

			$payment_address_info = $this->Model_Localisation_Country->getCountry($subscription['payment_country_id']);

			$this->data['currency_code'] = $subscription['currency_code'];
			$this->data['first_name']    = html_entity_decode($subscription['payment_firstname'], ENT_QUOTES, 'UTF-8');
			$this->data['last_name']     = html_entity_decode($subscription['payment_lastname'], ENT_QUOTES, 'UTF-8');
			$this->data['address1']      = html_entity_decode($subscription['payment_address_1'], ENT_QUOTES, 'UTF-8');
			$this->data['address2']      = html_entity_decode($subscription['payment_address_2'], ENT_QUOTES, 'UTF-8');
			$this->data['city']          = html_entity_decode($subscription['payment_city'], ENT_QUOTES, 'UTF-8');
			$this->data['zip']           = html_entity_decode($subscription['payment_postcode'], ENT_QUOTES, 'UTF-8');
			$this->data['country']       = $payment_address_info['iso_code_2'];
			$this->data['email']         = $subscription['email'];
			$this->data['invoice']       = $subscription['invoice_id'] . ' - ' . html_entity_decode($subscription['payment_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($subscription['payment_lastname'], ENT_QUOTES, 'UTF-8');
			$this->data['lc']            = $this->language->info('code');
			$this->data['notify_url']    = $this->url->link('payment/pp_standard/callback');
			$this->data['cancel_return'] = $this->url->link('checkout/checkout');
			$this->data['page_style']    = $this->settings['page_style'];

			if ($this->settings['pdt_enabled']) {
				$this->data['return'] = $this->url->link('payment/pp_standard/auto_return');
			} else {
				$this->data['return'] = $this->url->link('checkout/success');
			}

			$server = URL_IMAGE;

			//Ajax Urls
			$this->data['url_check_order_status'] = $this->url->link('block/checkout/confirm/check_order_status', 'order_id=' . $subscription['order_id']);

			//Template Data
			$this->data['image_url']     = $server . $this->config->get('config_logo');
			$this->data['paymentaction'] = $this->settings['transaction'] ? 'sale' : 'authorization';
			$this->data['custom']        = $this->encryption->encrypt($subscription['order_id']);

			$this->data['testmode'] = $testmode;

			$this->render();
		}
	}

	public function validate($address)
	{
		if (!parent::validate($address)) {
			return false;
		}

		$currencies = array(
			'AUD',
			'CAD',
			'EUR',
			'GBP',
			'JPY',
			'USD',
			'NZD',
			'CHF',
			'HKD',
			'SGD',
			'SEK',
			'DKK',
			'PLN',
			'NOK',
			'HUF',
			'CZK',
			'ILS',
			'MXN',
			'MYR',
			'BRL',
			'PHP',
			'TWD',
			'THB',
			'TRY',
		);

		if (!in_array(strtoupper($this->currency->getCode()), $currencies)) {
			return false;
		}

		return true;
	}
}
