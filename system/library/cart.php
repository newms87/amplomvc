<?php

class Cart extends Library
{
	const PRODUCTS = 'products';
	const VOUCHERS = 'vouchers';

	const ERROR_PRODUCT_ID           = 101;
	const ERROR_PRODUCT_QUANTITY     = 102;
	const ERROR_PRODUCT_MINIMUM      = 103;
	const ERROR_PRODUCT_OPTION_EMPTY = 104;
	const ERROR_PRODUCT_OPTION_MULTI = 105;

	const ERROR_SHIPPING_ADDRESS            = 201;
	const ERROR_SHIPPING_METHOD             = 202;
	const ERROR_SHIPPING_GEOZONE            = 203;
	const ERROR_SHIPPING_METHOD_UNAVAILABLE = 204;
	const ERROR_SHIPPING_ADDRESS_COUNTRY    = 205;
	const ERROR_SHIPPING_ADDRESS_ZONE       = 206;
	const ERROR_PAYMENT_ADDRESS             = 207;
	const ERROR_PAYMENT_METHOD              = 208;
	const ERROR_PAYMENT_ADDRESS_COUNTRY     = 209;
	const ERROR_PAYMENT_ADDRESS_ZONE        = 210;

	const ERROR_CART_EMPTY        = 301;
	const ERROR_CART_STOCK        = 302;
	const ERROR_CHECKOUT_VALIDATE = 303;
	const ERROR_CHECKOUT_PAYMENT  = 304;
	const ERROR_CHECKOUT_SHIPPING = 305;

	private $totals = null;
	private $error_code = null;

	public function __construct()
	{
		parent::__construct();

		if (!$this->session->has('cart') || !is_array($this->session->get('cart'))) {
			$this->session->set('cart', array());
		}

		if (!$this->session->has('wishlist') || !is_array($this->session->get('wishlist'))) {
			$this->session->set('wishlist', array());
		}
	}

	/******************
	 * Error Handling *
	 ******************/

	public function isCode($code)
	{
		return $code === $this->error_code;
	}

	public function getErrorCode()
	{
		return $this->error_code;
	}

	/******************
	 * Cart Functions *
	 ******************/

	public function get($type = null)
	{
		if ($type) {
			return !empty($_SESSION['cart'][$type]) ? $_SESSION['cart'][$type] : array();
		}

		return $this->session->get('cart');
	}

	public function getItem($type, $key)
	{
		$items = $this->get($type);

		return isset($items[$key]) ? $items[$key] : null;
	}

	public function has($type)
	{
		return !empty($_SESSION['cart'][$type]);
	}

	public function isEmpty()
	{
		if ($this->session->has('cart')) {
			foreach ($this->session->get('cart') as $cart) {
				if (!empty($cart)) {
					return false;
				}
			}
		}

		return true;
	}

	public function countItems($type = null)
	{
		$count = 0;

		if (!$type) {
			foreach ($this->get() as $type => $items) {
				foreach ($items as $item) {
					$count += $item['quantity'];
				}
			}

			return $count;
		}

		$items = $this->get($type);

		foreach ($items as $item) {
			$count += $item['quantity'];
		}

		return $count;
	}

	//TODO: Need to implement a more dynamic cart system to incorporate other cart types (eg: subscriptions, user_custom_types, etc..)
	public function canCheckout()
	{
		return $this->hasProducts();
	}

	public function guestCheckoutAllowed()
	{
		return option('config_guest_checkout') && !option('config_customer_hide_price') && !$this->cart->hasDownload();
	}

	/**
	 * Add an item to the Cart.
	 *
	 * @param string $type - The cart set to add to. Can be Cart::PRODUCTS, Cart::VOUCHERS, or a custom cart set (eg: 'subscriptions')
	 * @param $item_id - The ID of the product to add
	 * @param int $quantity - number of this product / options selected to add
	 * @param array $options - A set of options selected for this product
	 *
	 * @return bool|string The key for the cart item added (used to reference this item to update / remove from cart)
	 */

	public function addItem($type, $item_id, $quantity = 1, $options = array())
	{
		$key = (int)$item_id . ':' . base64_encode(serialize($options));

		if (empty($_SESSION['cart'][$type][$key])) {
			$_SESSION['cart'][$type][$key] = array(
				'id'       => $item_id,
				'quantity' => $quantity,
				'options'  => $options,
				'key'      => $key,
			);
		} else {
			$_SESSION['cart'][$type][$key]['quantity'] += $quantity;
		}

		//Invalidate Rendered Data
		$this->totals = null;

		$this->saveCart();

		return $key;
	}

	public function updateItem($type, $key, $quantity)
	{
		if (!isset($_SESSION['cart'][$type][$key])) {
			return false;
		}

		if ((int)$quantity > 0) {
			$_SESSION['cart'][$type][$key]['quantity'] = (int)$quantity;
		} else {
			$this->removeItem($type, $key);
		}

		$this->totals = null;

		$this->saveCart();
	}

	public function removeItem($type, $key)
	{
		if (isset($_SESSION['cart'][$type][$key])) {
			unset($_SESSION['cart'][$type][$key]);
			$this->totals = null;

			$this->saveCart();
		}
	}

	public function merge($cart)
	{
		if (is_string($cart)) {
			$cart = unserialize($cart);
		}

		if (empty($cart)) {
			return false;
		}


		//TODO: Cannot load every time, need to resolve how to load from session and DB!!


		foreach ($cart as $type => $items) {
			foreach ($items as $key => $data) {
				if (!empty($_SESSION['cart'][$type][$key])) {
					$_SESSION['cart'][$type][$key]['quantity'] += $data['quantity'];
				} else {
					$_SESSION['cart'][$type][$key] = $data;
				}
			}
		}

		$this->totals = null;

		$this->saveCart();

		return true;
	}

	public function clear()
	{
		$this->totals = null;

		$this->session->set('cart', array());
		$this->session->set('wishlist', array());

		$this->clearPaymentAddress();
		$this->clearShippingAddress();
		$this->session->delete('comment');
		$this->session->delete('coupons');
		$this->session->delete('reward');
		$this->session->delete('vouchers');

		$this->order->clear();

		$this->saveCart();
	}

	public function getWeight()
	{
		$weight = 0;

		foreach ($this->getProducts() as $cart_product) {
			$product = $cart_product['product'];
			if ($product['shipping']) {
				$weight += $this->weight->get($product['weight'], $product['weight_class_id']);
			}
		}

		return $weight;
	}

	/**
	 * Cart Totals
	 */

	public function getSubTotal()
	{
		if (!$this->totals) {
			$this->getTotals();
		}

		return $this->totals['sub_total']['value'];
	}

	public function getTotal()
	{
		if (!$this->totals) {
			$this->getTotals();
		}

		return $this->totals['total']['value'];
	}

	public function getTotals($refresh = false)
	{
		if (!$this->totals || $refresh) {
			$this->totals = array();
			$total      = 0;
			$taxes      = $this->getTaxes();

			$total_extensions = $this->System_Extension_Total->getActive();

			foreach ($total_extensions as $code => $extension) {
				if (method_exists($extension, 'getTotal')) {
					$data = $extension->getTotal($this->totals, $total, $taxes);

					if ($data) {
						$this->totals[$code] = $data;
					}
				}

				if (isset($this->totals[$code])) {
					$this->totals[$code] += $extension->info();
				}
			}

			uasort($this->totals, function ($a, $b) {
				return $a['sort_order'] > $b['sort_order'];
			});
		}

		return $this->totals;
	}

	public function getTotalPoints()
	{
		$points_total = 0;

		$products = $this->getProducts();

		foreach ($products as $product) {
			$points_total += (int)$product['points'];
		}

		return $points_total;
	}

	/**
	 * Taxes
	 **/

	//TODO: This can probably be improved...
	public function getTaxes()
	{
		$tax_data = array();

		foreach ($this->getProducts() as $cart_product) {
			$product = $cart_product['product'];
			if ($product['tax_class_id']) {
				//TODO: Should be tax->calculate... right?
				$tax_rates = $this->tax->getRates($cart_product['total'], $product['tax_class_id']);

				foreach ($tax_rates as $tax_rate) {
					$amount = 0;

					if ($tax_rate['type'] == 'F') {
						$amount = ($tax_rate['amount'] * $cart_product['quantity']);
					} elseif ($tax_rate['type'] == 'P') {
						$amount = $tax_rate['amount'];
					}

					if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
						$tax_data[$tax_rate['tax_rate_id']] = $amount;
					} else {
						$tax_data[$tax_rate['tax_rate_id']] += $amount;
					}
				}
			}
		}

		return $tax_data;
	}

	/**
	 *  Cart Products
	 */

	public function addProduct($product_id, $quantity = 1, $options = array())
	{
		if ($this->validateProduct($product_id, $quantity, $options) && (int)$quantity > 0) {
			return $this->addItem(self::PRODUCTS, $product_id, $quantity, $options);
		}
	}

	public function updateProduct($key, $quantity)
	{
		//Invalidate cart product cache
		list($product_id, $cart_key) = explode(':', $key);
		$customer_id = $this->customer->getId();

		$this->cache->delete("product.$product_id.$customer_id.$cart_key");

		$this->updateItem(self::PRODUCTS, $key, $quantity);
	}

	public function removeProduct($key)
	{
		//Invalidate cart product cache
		list($product_id, $cart_key) = explode(':', $key);
		$customer_id = $this->customer->getId();

		$this->cache->delete("product.$product_id.$customer_id.$cart_key");

		$this->removeItem(self::PRODUCTS, $key);
	}

	public function hasProducts()
	{
		return $this->has(self::PRODUCTS);
	}

	public function getProduct($key)
	{
		$product = $this->getItem(self::PRODUCTS, $key);

		if (!$product || !$this->fillCartProduct($key, $product)) {
			if (!empty($product['name'])) {
				$this->message->add('warning', _l("%s is no longer available and has been removed from your cart. We apologize for the inconvenience.", $product['product']['name']));
			}

			$this->remove(self::PRODUCTS, $key);

			return null;
		}

		return $product;
	}

	public function getProductIds()
	{
		$products = $this->get(self::PRODUCTS);

		return array_column($products, 'id');
	}

	public function getProducts()
	{
		$cart_products = $this->get(self::PRODUCTS);

		foreach ($cart_products as $key => &$product) {
			if (!$this->fillCartProduct($key, $product)) {
				if (!empty($product['name'])) {
					$this->message->add('warning', _l("%s is no longer available and has been removed from your cart. We apologize for the inconvenience.", $product['product']['name']));
				}

				unset($cart_products[$key]);
				$this->removeItem(self::PRODUCTS, $key);

				continue;
			}
		}
		unset($product);

		return $cart_products;
	}

	private function fillCartProduct($key, &$product)
	{
		list($product_id, $cart_key) = explode(':', $key);
		$customer_id = $this->customer->getId();

		$data = $this->cache->get("product.$product_id.$customer_id.$cart_key");

		if ($data) {
			$product = $data;
		} else {
			//fillProductDetails will cache results, so we need to save the quantity
			$qty = $product['quantity'];

			if (!$this->Catalog_Model_Catalog_Product->fillProductDetails($product, $product['id'], $product['quantity'], $product['options'])) {
				return false;
			}

			//Allow Extensions to modify product total / details
			$total_extensions = $this->System_Extension_Total->getActive();

			foreach ($total_extensions as $extension) {
				if (method_exists($extension, 'calculateProductTotal')) {
					$extension->calculateProductTotal($product);
				}
			}

			//Restore quantity (for caching reasons)
			$product['quantity'] = $qty;

			$this->cache->set("product.$product_id.$customer_id.$cart_key", $product);
		}

		return true;
	}

	public function countProducts()
	{
		return $this->countItems(self::PRODUCTS);
	}

	public function productPurchasable($product)
	{
		if (is_integer($product)) {
			$product = $this->Model_Catalog_Product->getProduct($product);
		}

		if (!$product['status']) {
			return false;
		}

		if ($product['quantity'] < 1 && !option('config_stock_checkout')) {
			return false;
		}

		if ($this->date->isInFuture($product['date_available'], false) || $this->date->isInPast($product['date_expires'], false)) {
			return false;
		}

		return true;
	}

	public function validateProduct($product_id, $quantity, &$selected_options = array())
	{
		$product_info = $this->Model_Catalog_Product->getProduct($product_id);

		if ($product_info) {
			$product_options = $this->Model_Catalog_Product->getProductOptions($product_id);

			//Validate all of the options for this product (including ones that were not in $selected_options)
			foreach ($product_options as $product_option) {

				//If there are values for this product option, and the option is required, validate it!
				if (!empty($product_option['product_option_values']) && $product_option['required']) {

					//If the option was not selected by the customer, throw an error!
					if (empty($selected_options[$product_option['product_option_id']])) {
						$this->error['add']['option'][$product_option['product_option_id']] = _l("Please select a %s.", $product_option['display_name']);
						$this->error_code                                                   = self::ERROR_PRODUCT_OPTION_EMPTY;
						return false;
					} elseif ($product_option['group_type'] === 'single') {
						if (is_array($selected_options[$product_option['product_option_id']])) {
							if (count($selected_options[$product_option['product_option_id']]) > 1) {
								$this->error['add']['option'][$product_option['product_option_id']] = _l("You can only select 1 option for %s.", $product_option['display_name']);
								$this->error_code                                                   = self::ERROR_PRODUCT_OPTION_MULTI;
								return false;
							}
						} else {
							//Convert to array format to standardize
							$selected_options[$product_option['product_option_id']] = array($selected_options[$product_option['product_option_id']]);
						}
					}
				}
			}

			//validate Product Option resrictions
			/*
				foreach ($selected_options as $product_option_id => $selected_po) {
					foreach ($selected_po as $selected_pov) {
						if (isset($selected_pov['option_value_id']) && isset($restrictions[$selected_pov['option_value_id']])) {
							foreach ($options as $selected_po2) {
								foreach ($selected_po2 as $selected_pov2) {
									if (in_array($selected_pov2['option_value_id'], $restrictions[$selected_pov['option_value_id']])) {
										$this->error['add']['option'][$product_option_id] = $this->language->get('error_pov_restriction');
										return false;
									}
								}
							}
						}
					}
				}
			*/
		} else {
			$this->error_code   = self::ERROR_PRODUCT_ID;
			$this->error['add'] = _l("The product was not found in our system.");
		}

		return empty($this->error['add']);
	}

	/**
	 * Cart Stock
	 */

	public function hasStock()
	{
		foreach ($this->getProducts() as $cart_product) {
			$product = $cart_product['product'];
			if (!$cart_product['in_stock']) {
				$this->error_code    = self::ERROR_PRODUCT_QUANTITY;
				$this->error['cart'] = _l('We do not have the request quantity for <a href="%s">%s</a> (marked with <span class="out_of_stock"></span>) available at this time.', site_url('product/product', 'product_id=' . $product['product_id']), $product['name']);
			}
		}

		return empty($this->error['cart']);
	}

	public function validateMinimumQuantity()
	{
		$product_total = 0;

		$cart_products = $this->getProducts();

		foreach ($cart_products as $cart_product) {
			foreach ($cart_products as $cart_product_2) {
				//Add up all products with same ID (including ourselves)
				if ($cart_product_2['id'] === $cart_product['id']) {
					$product_total += $cart_product_2['quantity'];
				}
			}

			if ($product_total < $cart_product['product']['minimum']) {
				$this->error_code    = self::ERROR_PRODUCT_MINIMUM;
				$this->error['cart'] = _l('You must order at least %s units for %s', $cart_product['product']['minimum'], $cart_product['product']['name']);
			}
		}

		return empty($this->error['cart']);
	}

	public function validate()
	{
		if ($this->isEmpty()) {
			$this->error_code    = self::ERROR_CART_EMPTY;
			$this->error['cart'] = _l("Your shopping cart is empty!");
		} elseif (!option('config_stock_checkout') && !$this->hasStock()) {
			$this->error_code    = self::ERROR_CART_STOCK;
			$this->error['cart'] = _l("There are products in your cart that are out of stock");
		} else {
			$this->validateMinimumQuantity();
		}

		return empty($this->error['cart']);
	}

	public function validateCheckout()
	{
		if (!$this->validate()) {
			$this->error_code        = self::ERROR_CHECKOUT_VALIDATE;
			$this->error['checkout'] = _l("The contents of you cart have changed. Please proceed to checkout again.");
		} elseif (!$this->validatePaymentMethod()) {
			$this->error_code        = self::ERROR_CHECKOUT_PAYMENT;
			$this->error['checkout'] = _l("The Payment Options have changed! Please select a new Payment Method.");
		} elseif (!$this->validateShippingMethod()) {
			$this->error_code        = self::ERROR_CHECKOUT_SHIPPING;
			$this->error['checkout'] = _l("The Shipping Options have changed! Please select a new Shipping Method.");
		}

		return empty($this->error['checkout']);
	}

	/**
	 * Wishlist Functions
	 */

	public function getWishlist()
	{
		return $this->session->get('wishlist');
	}

	public function mergeWishlist($wishlist)
	{
		if (is_string($wishlist)) {
			$wishlist = unserialize($wishlist);
		}

		if (empty($wishlist)) {
			return false;
		}

		if (!$this->session->get('wishlist')) {
			$this->session->set('wishlist', array());
		}

		foreach ($wishlist as $product_id) {
			if (!in_array($product_id, $this->session->get('wishlist'))) {
				$_SESSION['wishlist'][] = $product_id;
			}
		}

		return true;
	}

	/**
	 * Product Compare Functions
	 */

	public function get_compare_list()
	{
		return $this->session->get('compare');
	}

	public function get_compare_count()
	{
		if ($this->session->has('compare')) {
			return count($this->session->get('compare'));
		}

		return null;
	}


	/**
	 * Shipping & Payment API
	 */

	public function hasDownload()
	{
		foreach ($this->getProducts() as $product) {
			if (!empty($product['downloads'])) {
				return true;
			}
		}

		return false;
	}

	public function hasPaymentAddress()
	{
		return $this->session->has('payment_address_id');
	}

	public function getPaymentAddressId()
	{
		return (int)$this->session->get('payment_address_id');
	}

	public function getPaymentAddress()
	{
		return $this->customer->getAddress($this->session->get('payment_address_id'));
	}

	public function setPaymentAddress($address)
	{
		//New Address
		if (!$address) {
			$this->error['payment_address'] = _l("No Payment Address was specified.");
			return false;
		}

		if (is_array($address)) {
			$address_id = $this->customer->addAddress($address);

			if (!$address_id) {
				$this->error['payment_address'] = $this->address->getError();
				return false;
			}
		} //Set Existing Address
		else {
			$address_id = (int)$address;
		}

		//Address changed, invalidate the payment method
		if (!$this->getPaymentAddressId() !== $address_id) {
			$this->clearPaymentMethod();
		}

		if (!empty($address_id)) {
			$this->session->set('payment_address_id', $address_id);
		}

		if (!$this->validatePaymentAddress()) {
			$this->clearPaymentAddress();
		}

		return empty($this->error['payment_address']);
	}

	public function clearPaymentAddress()
	{
		$this->session->delete('payment_address_id');
		$this->clearPaymentMethod();
	}

	public function validatePaymentAddress()
	{
		unset($this->error['payment_address']);

		if (!$this->hasPaymentAddress()) {
			$this->error_code               = self::ERROR_PAYMENT_ADDRESS;
			$this->error['payment_address'] = _l("You must specify a Billing Address!");
			return false;
		}

		if (!$this->canBillTo($this->getPaymentAddress())) {
			$this->clearPaymentAddress();
			return false;
		}

		return true;
	}

	public function canBillTo($address)
	{
		if (!$this->address->validate($address)) {
			$this->error['payment_address'] = $this->address->getError();

			return false;
		}

		return true;
	}

	/** Shipping Address Operations **/
	public function hasShipping()
	{
		foreach ($this->getProducts() as $cart_product) {
			if ($cart_product['product']['shipping']) {
				return true;
			}
		}

		return false;
	}

	public function hasShippingAddress()
	{
		return $this->session->has('shipping_address_id');
	}

	public function getShippingAddressId()
	{
		return (int)$this->session->get('shipping_address_id');
	}

	public function getShippingAddress()
	{
		return $this->customer->getAddress($this->session->get('shipping_address_id'));
	}

	public function setShippingAddress($address)
	{
		if (!$address) {
			$this->error['shipping_address'] = _l("No Shipping Address Specified.");
			return false;
		}

		//New Address
		if (is_array($address)) {
			$address_id = $this->customer->addAddress($address);

			if (!$address_id) {
				$this->error['shipping_address'] = $this->customer->getError();
				return false;
			}
		} //Set Existing Address
		else {
			$address_id = (int)$address;
		}

		//Address changed, invalidate the shipping method
		if ($this->getShippingAddressId() !== $address_id) {
			$this->clearShippingMethod();
		}

		if (!empty($address_id)) {
			$this->session->set('shipping_address_id', $address_id);
		}

		if (!$this->validateShippingAddress()) {
			$this->clearShippingAddress();
		}

		return empty($this->error['shipping_address']);
	}

	public function clearShippingAddress()
	{
		$this->session->delete('shipping_address_id');
		$this->clearShippingMethod();
	}

	public function validateShippingAddress()
	{
		unset($this->error['shipping_address']);

		if (!$this->hasShippingAddress()) {
			$this->error_code                = self::ERROR_SHIPPING_ADDRESS;
			$this->error['shipping_address'] = _l("You must specify a Delivery Address!");
			return false;
		}

		if (!$this->canShipTo($this->getShippingAddress())) {
			$this->clearShippingAddress();
			return false;
		}

		return true;
	}

	public function canShipTo($address)
	{
		if (!$this->address->validate($address)) {
			$this->error['shipping_address'] = $this->address->getError();
		} elseif (!$this->address->inGeoZone($address, option('config_allowed_shipping_zone'))) {
			$this->error_code                = self::ERROR_SHIPPING_ADDRESS_GEOZONE;
			$this->error['shipping_address'] = _l("We do not ship to the location you selected.");
		}

		return empty($this->error['shipping_address']);
	}

	/** Payment Method Operations **/

	public function hasPaymentMethod()
	{
		return $this->session->has('payment_code');
	}

	public function getPaymentCode()
	{
		return $this->session->get('payment_code');
	}

	public function getPaymentKey()
	{
		return $this->session->get('payment_key');
	}

	public function getPaymentMethod()
	{
		$payment_code = $this->getPaymentCode();

		if ($payment_code) {
			$payment_ext = $this->System_Extension_Payment->get($payment_code);

			if ($payment_ext->isActive()) {
				return $payment_ext;
			}
		}

		return false;
	}

	public function getPaymentMethods($payment_address = null)
	{
		if (!empty($payment_address)) {
			if (!is_array($payment_address)) {
				$payment_address = $this->customer->getAddress($payment_address);
			}
		} else {
			$payment_address = $this->getPaymentAddress();
		}

		// Payment Methods
		$payment_extensions = $this->System_Extension_Payment->getActive();
		$methods            = array();

		foreach ($payment_extensions as $code => $extension) {
			if ($extension->validate($payment_address)) {
				$methods[$code] = $extension->info();
			}
		}

		if (empty($methods)) {
			$this->error['payment_method'] = _l("There are no available Payment Methods for your order! Please contact <a href=\"mailto:%s\">Customer Support</a> to complete your order.", option('config_email'));

			$this->clearPaymentMethod();

			return false;
		}

		uasort($methods, function ($a, $b) {
			return $a['sort_order'] > $b['sort_order'];
		});

		//Validate the currently selected payment method
		$payment_code = $this->getPaymentCode();
		if ($payment_code && !isset($methods[$payment_code])) {
			$this->clearPaymentMethod();
		}

		return $methods;
	}

	public function setPaymentMethod($payment_code, $payment_key = null)
	{
		$payment_methods = $this->getPaymentMethods();

		if (!isset($payment_methods[$payment_code])) {
			$this->error_code              = self::ERROR_PAYMENT_METHOD;
			$this->error['payment_method'] = _l("There was no Payment Method specified!");

			return false;
		}

		$this->session->set('payment_code', $payment_code);
		$this->session->set('payment_key', $payment_key);

		return true;
	}

	public function clearPaymentMethod()
	{
		$this->session->delete('payment_code');
		$this->session->delete('payment_key');
	}

	public function validatePaymentMethod()
	{
		if (!$this->validatePaymentAddress()) {
			$this->error_code        = self::ERROR_PAYMENT_ADDRESS;
			$this->error['payment_method'] = _l("You must specify a Billing Address!");
		}  elseif (!$this->hasPaymentMethod()) {
			$this->error_code = self::ERROR_PAYMENT_METHOD;
			$this->error['payment_method'] = _l("There was no Payment Method specified");
		} else {
			$payment_method = $this->getPaymentMethod();

			if (!$payment_method->validate($this->getPaymentAddress())) {
				$this->clearPaymentMethod();
				$this->error_code        = self::ERROR_PAYMENT_METHOD;
				$this->error['payment_method'] = _l("The Payment Method selected is not available for this billing address!");
			}
		}

		return empty($this->error['payment_method']);
	}

	/** Shipping Method Operations **/

	public function hasShippingMethod()
	{
		return $this->session->has('shipping_code') && $this->session->has('shipping_key');
	}

	public function getShippingCode()
	{
		return $this->session->get('shipping_code');
	}

	public function getShippingKey()
	{
		return $this->session->get('shipping_key');
	}

	public function getShippingQuote()
	{
		$shipping_method = $this->getShippingMethod();

		if ($shipping_method) {
			$quotes = $shipping_method->getQuotes($this->getShippingAddress());

			$shipping_key = $this->getShippingKey();

			if (isset($quotes[$shipping_key])) {
				return $quotes[$shipping_key];
			}
		}

		return false;
	}

	public function getShippingMethod()
	{
		$shipping_code = $this->getShippingCode();

		if ($shipping_code) {
			$shipping_ext = $this->System_Extension_Shipping->get($shipping_code);

			if ($shipping_ext->isActive()) {
				return $shipping_ext;
			}
		}

		return false;
	}

	public function getShippingMethods($shipping_address = null)
	{
		if (!empty($shipping_address)) {
			if (!is_array($shipping_address)) {
				$shipping_address = $this->customer->getAddress($shipping_address);
			}
		} else {
			$shipping_address = $this->getShippingAddress();
		}

		// Shipping Methods
		$shipping_extensions = $this->System_Extension_Shipping->getActive();
		$methods             = array();

		foreach ($shipping_extensions as $code => $extension) {
			if ($extension->validate($shipping_address)) {
				$methods[$code] = $extension->info();
			}
		}

		//No Shipping Methods Available!
		if (empty($methods)) {
			$this->error_code               = self::ERROR_SHIPPING_METHOD_UNAVAILABLE;
			$this->error['shipping_method'] = _l("There are no available Shipping Methods for your order! Please contact <a href=\"%s\">Customer Support</a> to complete your order.", site_url('page/page', 'page_id=' . option('config_contact_page_id')));

			$this->clearShippingMethod();

			return false;
		}

		uasort($methods, function ($a, $b) {
			return $a['sort_order'] > $b['sort_order'];
		});

		//Validate the currently selected payment method
		$shipping_code = $this->getShippingCode();

		if ($shipping_code && !isset($methods[$shipping_code])) {
			$this->clearShippingMethod();
		}

		return $methods;
	}

	public function setShippingMethod($shipping_code, $shipping_key = null)
	{
		$shipping_methods = $this->getShippingMethods();

		if (!isset($shipping_methods[$shipping_code])) {
			$this->error_code              = self::ERROR_SHIPPING_METHOD;
			$this->error['shipping_method'] = _l("There was no Shipping Method specified!");

			return false;
		}

		$this->session->set('shipping_code', $shipping_code);
		$this->session->set('shipping_key', $shipping_key);

		return true;
	}

	public function clearShippingMethod()
	{
		$this->session->delete('shipping_code');
		$this->session->delete('shipping_key');
	}

	public function validateShippingMethod()
	{
		if ($this->hasShipping()) {
			if (!$this->validateShippingAddress()) {
				$this->error_code        = self::ERROR_SHIPPING_ADDRESS;
				$this->error['shipping_method'] = _l("You must specify a Delivery Address!");
			} elseif (!$this->hasShippingMethod()) {
				$this->error_code = self::ERROR_SHIPPING_METHOD;
				$this->error['shipping_method'] = _l("There was no Delivery Method specified");
			} else {
				$shipping_method = $this->getShippingMethod();

				if (!$shipping_method->validate($this->getShippingAddress())) {
					$this->clearShippingMethod();
					$this->error_code        = self::ERROR_SHIPPING_METHOD_UNAVAILABLE;
					$this->error['shipping_method'] = _l("There delivery method was not valid for the requested delivery address!");
				}
			}
		}

		return empty($this->error['shipping_method']);
	}

	public function getAllowedShippingZones()
	{
		$geo_zone_id = option('config_allowed_shipping_zone');

		if ($geo_zone_id > 0) {
			$allowed_geo_zones = $this->cache->get('zone.allowed.' . $geo_zone_id);

			if (is_null($allowed_geo_zones)) {
				$allowed_geo_zones = array();

				$zones = $this->Model_Localisation_Zone->getZonesByGeoZone($geo_zone_id);

				foreach ($zones as $zone) {
					if (empty($allowed_geo_zones[$zone['country_id']])) {
						$allowed_geo_zones[$zone['country_id']] = $this->Model_Localisation_Country->getCountry($zone['country_id']);
						$allowed_geo_zones[$zone['country_id']]['zones'] = array();
					}

					$allowed_geo_zones[$zone['country_id']]['zones'][$zone['zone_id']] = $zone;
				}

				$this->cache->set('zone.allowed.' . $geo_zone_id, $allowed_geo_zones);
			}

			return $allowed_geo_zones;
		}

		return array();
	}


	/**********************
	 *       Vouchers     *
	 **********************/

	public function hasVouchers($voucher_id = null)
	{
		if ($voucher_id) {
			return !empty($_SESSION['vouchers'][$voucher_id]);
		}

		return $this->session->has('vouchers');
	}

	public function getVoucherIds()
	{
		return $this->session->has('vouchers') ? $this->session->get('vouchers') : array();
	}

	public function getVouchers()
	{
		$vouchers = array();

		foreach ($this->getVoucherIds() as $voucher_id) {
			$vouchers[] = $this->System_Model_Voucher->getVoucher($voucher_id);
		}
	}

	public function addVoucher($voucher_id)
	{
		if (!$this->session->has('vouchers')) {
			$_SESSION['vouchers'][] = $voucher_id;
		} else {
			$this->session->set('vouchers', array($voucher_id));
		}
	}

	public function removeVoucher($voucher_id)
	{
		unset($_SESSION['vouchers'][$voucher_id]);
	}

	public function removeAllVouchers()
	{
		$this->session->delete('vouchers');
	}

	/**
	 * Guest API
	 */

	public function saveGuestInfo($info)
	{
		$this->session->set('guest_info', $info);
	}

	public function loadGuestInfo()
	{
		return $this->session->has('guest_info') ? $this->session->get('guest_info') : null;
	}

	/**
	 * Comments
	 */

	public function getComment()
	{
		return $this->session->get('comment');
	}

	public function setComment($comment)
	{
		$this->session->set('comment', strip_tags($comment));
	}

	/** Policies **/
	public function getShippingPolicy($shipping_policy_id)
	{
		$shipping_policies = $this->getShippingPolicies();

		if (isset($shipping_policies[$shipping_policy_id])) {
			$policy = $shipping_policies[$shipping_policy_id];

			$policy['description'] = html_entity_decode($policy['description'], ENT_QUOTES, 'UTF-8');

			$policy['is_default'] = $shipping_policy_id == option('config_default_shipping_policy');
			return $policy;
		}

		return null;
	}

	public function getProductShippingPolicy($product_id)
	{
		$shipping_policy_id = $this->queryVar("SELECT shipping_policy_id FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$product_id);

		if (!is_null($shipping_policy_id)) {
			return $this->getShippingPolicy($shipping_policy_id);
		}

		return null;
	}

	public function getShippingPolicies()
	{
		return $this->config->load('policies', 'shipping_policies', 0);
	}

	public function getReturnPolicy($return_policy_id)
	{
		$return_policies = $this->getReturnPolicies();

		if (isset($return_policies[$return_policy_id])) {
			$policy = $return_policies[$return_policy_id];

			$policy['description'] = html_entity_decode($policy['description'], ENT_QUOTES, 'UTF-8');

			$policy['is_default'] = $return_policy_id == option('config_default_return_policy');
			return $policy;
		}

		return null;
	}

	public function getProductReturnPolicy($product_id)
	{
		$return_policy_id = $this->queryVar("SELECT return_policy_id FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$product_id);

		if (!is_null($return_policy_id)) {
			return $this->getReturnPolicy($return_policy_id);
		}

		return null;
	}

	public function getReturnPolicies()
	{
		return $this->config->load('policies', 'return_policies', 0);
	}

	public function isCheckout()
	{
		return $this->url->is('block/checkout') || $this->url->is('checkout/checkout');
	}

	public function saveCart()
	{
		if (!$this->session->get('customer_id')) {
			return;
		}

		$customer_update = array(
			'cart'     => serialize($this->session->get('cart')),
			'wishlist' => serialize($this->session->get('wishlist')),
		);

		$this->update('customer', $customer_update, (int)$this->session->get('customer_id'));
	}
}
