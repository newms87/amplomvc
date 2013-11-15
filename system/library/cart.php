<?php
class Cart extends Library
{
	const PRODUCTS = 'products';
	const VOUCHERS = 'vouchers';

	private $totals = null;
	private $error_code = null;

	public function __construct($registry)
	{
		parent::__construct($registry);

		$this->language->system('cart');

		if (!isset($this->session->data['cart']) || !is_array($this->session->data['cart'])) {
			$this->session->data['cart'] = array();
		}

		if (!isset($this->session->data['wishlist']) || !is_array($this->session->data['wishlist'])) {
			$this->session->data['wishlist'] = array();
		}
	}

	/******************
	 * Error Handling *
	 ******************/

	public function _e($code, $type, $key)
	{
		$this->error[$type] = $this->language->get($key);
		$this->error_code   = $code;
	}

	public function get_error_code()
	{
		return $this->error_code;
	}

	//TODO: This function exists in the Library abstract class. Use that maybe?
	public function get_errors($type = null, $pop = false, $name_format = false)
	{
		//Get Specific Error
		if ($type) {
			if (isset($this->error[$type])) {
				$e = $this->error[$type];

				if ($pop) {
					unset($this->error[$type]);
				}
			} else {
				return array();
			}
		} //Get All Errors
		else {
			$e = $this->error;

			if ($pop) {
				$this->error = array();
			}
		}

		if ($name_format) {
			return $this->tool->name_format($name_format, $e);
		}

		return $e;
	}

	public function has_error($type)
	{
		$type_list = explode('>', $type);

		$error = $this->error;
		foreach ($type_list as $t) {
			if (!isset($error[$t])) {
				return false;
			}
			$error = $error[$t];
		}

		return true;
	}

	/******************
	 * Cart Functions *
	 ******************/

	public function get($type = null)
	{
		if ($type) {
			return !empty($this->session->data['cart'][$type]) ? $this->session->data['cart'][$type] : array();
		}

		return $this->session->data['cart'];
	}

	public function getItem($type, $key)
	{
		$items = $this->get($type);

		return isset($items[$key]) ? $items[$key] : null;
	}

	public function has($type)
	{
		return !empty($this->session->data['cart'][$type]);
	}

	public function isEmpty()
	{
		if (!empty($this->session->data['cart'])) {
			foreach ($this->session->data['cart'] as $cart) {
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

		if (!$type ) {
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

		return $count;;
	}

	//TODO: Need to implement a more dynamic cart system to incorporate other cart types (eg: subscriptions, user_custom_types, etc..)
	public function canCheckout()
	{
		return $this->hasProducts();
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

	public function add($type, $item_id, $quantity = 1, $options = array())
	{
		$key = (int)$item_id . ':' . base64_encode(serialize($options));

		if (empty($this->session->data['cart'][$type][$key])) {
			$this->session->data['cart'][$type][$key] = array(
				'id'       => $item_id,
				'quantity' => $quantity,
				'options'  => $options,
				'key'      => $key,
			);
		} else {
			$this->session->data['cart'][$type][$key]['quantity'] += $quantity;
		}

		//Invalidate Rendered Data
		$this->totals = null;

		return $key;
	}

	public function update($type, $key, $quantity)
	{
		if (!isset($this->session->data['cart'][$type][$key])) {
			return false;
		}

		if ((int)$quantity > 0) {
			$this->session->data['cart'][$type][$key]['quantity'] = (int)$quantity;
		} else {
			$this->remove($type, $key);
		}

		$this->totals = null;
	}

	public function remove($type, $key)
	{
		if (isset($this->session->data['cart'][$type][$key])) {
			unset($this->session->data['cart'][$type][$key]);
			$this->totals = null;
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

		foreach ($cart as $type => $items) {
			foreach ($items as $key => $data) {
				if (!empty($this->session->data['cart'][$type][$key])) {
					$this->session->data['cart'][$type][$key]['quantity'] += $data['quantity'];
				} else {
					$this->session->data['cart'][$type][$key] = $data;
				}
			}
		}

		$this->totals = null;

		return true;
	}

	public function clear()
	{
		$this->totals = null;

		$this->session->data['cart']     = array();
		$this->session->data['wishlist'] = array();

		unset($this->session->data['shipping_address_id']);
		unset($this->session->data['payment_address_id']);
		unset($this->session->data['shipping_method_id']);
		unset($this->session->data['payment_method_id']);
		unset($this->session->data['comment']);
		unset($this->session->data['coupons']);
		unset($this->session->data['reward']);
		unset($this->session->data['vouchers']);

		$this->order->clear();
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
			$total_data = array();
			$total      = 0;
			$taxes      = $this->getTaxes();

			$total_extensions = $this->System_Extension_Total->getActive();

			foreach ($total_extensions as $code => $extension) {
				if (method_exists($extension, 'getTotal')) {
					$data = $extension->getTotal($total_data, $total, $taxes);

					if ($data) {
						$total_data[$code] = $data;
					}
				}

				if (isset($total_data[$code])) {
					$total_data[$code] += $extension->info();
				}
			}

			uasort($total_data, function ($a, $b) { return $a['sort_order'] > $b['sort_order']; });

			$this->totals = $total_data;
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
			$this->add(self::PRODUCTS, $product_id, $quantity, $options);
		}
	}

	public function updateProduct($key, $quantity)
	{
		$this->update(self::PRODUCTS, $key, $quantity);
	}

	public function removeProduct($key)
	{
		$this->remove(self::PRODUCTS, $key);
	}

	public function hasProducts()
	{
		return $this->has(self::PRODUCTS);
	}

	public function getProduct($key)
	{
		return $this->getItem(self::PRODUCTS, $key);
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
			if (!$this->Catalog_Model_Catalog_Product->fillProductDetails($product, $product['id'], $product['quantity'], $product['options'])) {
				$this->message->add('warning', _l("%s is no longer available and has been removed from your cart. We apologize for the inconvenience.", $product['product']['name']));

				unset($cart_products[$key]);
				$this->remove(self::PRODUCTS, $key);

				continue;
			}

			//Allow Extensions to modify product total / details
			$total_extensions = $this->System_Extension_Total->getActive();

			foreach ($total_extensions as $extension) {
				if (method_exists($extension, 'calculateProductTotal')) {
					$extension->calculateProductTotal($product);
				}
			}
		}
		unset($product);

		return $cart_products;
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

		if ($product['quantity'] < 1 && !$this->config->get('config_stock_checkout')) {
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

			foreach ($product_options as $product_option) {
				if (!empty($product_option['product_option_values']) && $product_option['required']) {
					if (empty($selected_options[$product_option['product_option_id']])) {
						$this->error['add']['option'][$product_option['product_option_id']] = $this->_('error_required', $product_option['display_name']);
						return false;
					} elseif ($product_option['group_type'] === 'single') {
						if (is_array($selected_options[$product_option['product_option_id']])) {
							if (count($selected_options[$product_option['product_option_id']]) > 1) {
								$this->error['add']['option'][$product_option['product_option_id']] = $this->_('error_selected_multi', $product_option['display_name']);
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
			$this->_e('PV-1', 'add', 'error_invalid_product_id');
			return false;
		}

		return true;
	}

	/**
	 * Cart Stock
	 */

	public function hasStock()
	{
		foreach ($this->getProducts() as $cart_product) {
			$product = $cart_product['product'];
			if (!$cart_product['in_stock']) {
				$this->_e('C-2', 'cart', _l('We do not have the request quantity for <a href="%s">%s</a> (marked with <span class="out_of_stock"></span>) available at this time.', $this->url->link('product/product', 'product_id=' . $product['product_id']), $product['name']));
				return false;
			}
		}

		return true;
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
				$this->_e('C-3', 'cart', _l('You must order at least %s units for %s', $cart_product['product']['minimum'], $cart_product['product']['name']));
				return false;
			}
		}

		return true;
	}

	public function validate()
	{
		if ($this->isEmpty()) {
			$this->_e('C-1', 'cart', 'error_cart_empty');
			return false;
		}

		if (!$this->config->get('config_stock_checkout') && !$this->hasStock()) {
			return false;
		}

		if (!$this->validateMinimumQuantity()) {
			return false;
		}

		return true;
	}

	public function validateCheckout()
	{
		if (!$this->validate()) {
			$this->_e('CKO-1', 'checkout', 'error_checkout_validate');
			return false;
		}

		if (!$this->validatePaymentDetails()) {
			$this->_e('CKO-2', 'checkout', 'error_checkout_payment');
			return false;
		}

		if (!$this->validateShippingDetails()) {
			$this->_e('CKO-3', 'checkout', 'error_checkout_shipping');
			return false;
		}

		return true;
	}

	/**
	 * Wishlist Functions
	 */

	public function getWishlist()
	{
		return !empty($this->session->data['wishlist']) ? $this->session->data['wishlist'] : null;
	}

	public function mergeWishlist($wishlist)
	{
		if (is_string($wishlist)) {
			$wishlist = unserialize($wishlist);
		}

		if (empty($wishlist)) {
			return false;
		}

		if (!isset($this->session->data['wishlist'])) {
			$this->session->data['wishlist'] = array();
		}

		foreach ($wishlist as $product_id) {
			if (!in_array($product_id, $this->session->data['wishlist'])) {
				$this->session->data['wishlist'][] = $product_id;
			}
		}

		return true;
	}

	/**
	 * Product Compare Functions
	 */

	public function get_compare_list()
	{
		return !empty($this->session->data['compare']) ? $this->session->data['compare'] : null;
	}

	public function get_compare_count()
	{
		return !empty($this->session->data['compare']) ? count($this->session->data['compare']) : null;
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
		return !empty($this->session->data['payment_address_id']);
	}

	public function getPaymentAddressId()
	{
		return isset($this->session->data['payment_address_id']) ? $this->session->data['payment_address_id'] : false;
	}

	public function getPaymentAddress()
	{
		if (isset($this->session->data['payment_address_id'])) {
			return $this->customer->getAddress($this->session->data['payment_address_id']);
		}

		return false;
	}

	public function setPaymentAddress($address = null)
	{
		if (empty($address)) {
			unset($this->session->data['payment_address_id']);
			$this->setPaymentMethod();
			return true;
		} elseif (is_array($address)) {
			$address_id = $this->address->add($address);

			if (!$address_id) {
				$this->_e('PA-10', 'payment_address', 'error_payment_address_details');
				return false;
			}
		} else {
			$address_id = (int)$address;
		}

		if (!empty($address_id)) {
			$this->session->data['payment_address_id'] = $address_id;
		}

		if (!$this->validatePaymentAddress()) {
			$this->_e('SA-11', 'payment_address', 'error_payment_address_invalid');
			unset($this->session->data['payment_address_id']);

			$this->setPaymentMethod();
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
		return !empty($this->session->data['shipping_address_id']);
	}

	public function getShippingAddressId()
	{
		return isset($this->session->data['shipping_address_id']) ? $this->session->data['shipping_address_id'] : false;
	}

	public function getShippingAddress()
	{
		if (isset($this->session->data['shipping_address_id'])) {
			return $this->customer->getAddress($this->session->data['shipping_address_id']);
		}

		return false;
	}

	public function setShippingAddress($address = null)
	{
		if (empty($address)) {
			unset($this->session->data['shipping_address_id']);
			$this->setShippingMethod();
			return true;
		} elseif (is_array($address)) {
			$address_id = $this->address->add($address);

			if (!$address_id) {
				$this->_e('SA-10', 'shipping_address', 'error_shipping_address_details');
				return false;
			}
		} else {
			$address_id = (int)$address;
		}

		if (!empty($address_id)) {
			$this->session->data['shipping_address_id'] = $address_id;
		}

		if (!$this->validateShippingAddress()) {
			$this->_e('SA-11', 'shipping_address', 'error_shipping_address_invalid');
			unset($this->session->data['shipping_address_id']);
			return false;
		}

		$this->setShippingMethod();

		return true;
	}

	//TODO: Move this to System_Extension_Payment controller...
	/** Payment Method Operations **/

	public function hasPaymentMethod()
	{
		return !empty($this->session->data['payment_method_id']);
	}

	public function getPaymentMethodId()
	{
		if (isset($this->session->data['payment_method_id'])) {
			return $this->session->data['payment_method_id'];
		}

		return false;
	}

	public function getPaymentMethod($payment_method_id = null, $payment_address = null)
	{
		if (!$payment_method_id) {
			$payment_method_id = $this->getPaymentMethodId();
		}

		if ($payment_method_id) {
			if (!empty($payment_address)) {
				if (!is_array($payment_address)) {
					$payment_address = $this->customer->getAddress($payment_address);
				}
			} else {
				$payment_address = $this->getPaymentAddress();
			}

			$extension = $this->System_Extension_Payment->get($payment_method_id);

			if ($extension->isActive() && $extension->validate($payment_address, $this->getTotal())) {
				return $extension;
			}
		}

		return false;
	}

	public function getPaymentMethodData($payment_method_id = null)
	{
		if (!$payment_method_id) {
			return false;
		}

		return $this->System_Extension_Payment->get($payment_method_id)->info();
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
			if ($extension->validate($payment_address, $this->getTotal())) {
				$methods[$code] = $extension->info();
			}
		}

		if (empty($methods)) {
			$this->error['checkout']['payment_method'] = $this->_('error_payment_methods', $this->config->get('config_email'));

			$this->setPaymentMethod();

			return false;
		}

		uasort($methods, function ($a, $b) { return $a['sort_order'] > $b['sort_order']; });

		//Validate the currenlty selected payment method
		if ($this->hasPaymentMethod() && !isset($methods[$this->getPaymentMethodId()])) {
			$this->setPaymentMethod(null);
		}

		return $methods;
	}

	public function setPaymentMethod($method = null)
	{
		if (!$method) {
			unset($this->session->data['payment_method_id']);
		} else {
			$payment_methods = $this->getPaymentMethods();

			if (is_string($method)) {
				if (!isset($payment_methods[$method])) {
					$this->_e('PM-1a', 'payment_method', 'error_payment_method');
					return false;
				}

				$payment_method_id = $payment_methods[$method]['code'];
			} else {
				if (!isset($payment_methods[$method['code']])) {
					$this->_e('PM-1b', 'payment_method', 'error_payment_method');
					return false;
				}

				$payment_method_id = $method['code'];
			}

			$this->session->data['payment_method_id'] = $payment_method_id;
		}

		return true;
	}

	//TODO: Move this to System_Extension_Shipping controller...
	/** Shipping Method Operations **/

	public function hasShippingMethod()
	{
		return !empty($this->session->data['shipping_method_id']);
	}

	public function getShippingMethodId()
	{
		if (isset($this->session->data['shipping_method_id'])) {
			return $this->session->data['shipping_method_id'];
		}

		return false;
	}

	public function getShippingMethod($shipping_method_id = null, $shipping_address = null)
	{
		if (!$shipping_method_id) {
			$shipping_method_id = $this->getShippingMethodId();
		}

		if ($shipping_method_id) {
			//Invalid Shipping method ID
			if (!strpos($shipping_method_id, '__')) {
				$code   = $shipping_method_id;
				$method = false;
			} else {
				list($code, $method) = explode("__", $shipping_method_id, 2);
			}

			if (!empty($shipping_address)) {
				if (!is_array($shipping_address)) {
					$shipping_address = $this->customer->getAddress($shipping_address);
				}
			} elseif ($this->hasShippingAddress()) {
				$shipping_address = $this->getShippingAddress();
			} else {
				$this->_e('SM-2', 'shipping_method', 'error_shipping_address');
				return false;
			}

			//TODO: This is validated in getQuote typically.. maybe rethink how we handle this.
			if (!$this->isAllowedShippingZone($shipping_address)) {
				$this->_e('SM-3', 'shipping_method', 'error_shipping_zone');
				return false;
			}

			$classname = "Catalog_Model_Shipping_" . $this->tool->formatClassname($code);
			$quotes    = $this->$classname->getQuote($shipping_address);

			if (!empty($quotes)) {
				if (!$method) {
					return $quotes;
				}

				foreach ($quotes as $quote) {
					if ($quote['method'] === $method) {
						return $quote;
					}
				}
			}
		}

		return false;
	}

	public function getShippingMethods($shipping_address = null)
	{
		//Find Available Shipping Methods
		$results = $this->Model_Setting_Extension->getExtensions('shipping');

		$methods = array();

		foreach ($results as $result) {
			$quotes = $this->getShippingMethod($result['code'], $shipping_address);

			if (!empty($quotes)) {
				foreach ($quotes as $quote) {
					$methods[$quote['code'] . '__' . $quote['method']] = $quote;
				}
			}
		}

		if ($methods) {
			//Validate the currently selected shipping method
			if (!$shipping_address && $this->hasShippingMethod() && !isset($methods[$this->getShippingMethodId()])) {
				$this->setShippingMethod();
			}

			uasort($methods, function ($a, $b) { return $a['sort_order'] > $b['sort_order']; });

			return $methods;
		}

		//No Shipping Options Available!
		$msg = $this->_('error_shipping_methods', $this->url->link('information/contact'));
		$this->_e('SM-4', 'shipping_method', $msg);

		return false;
	}

	public function getShippingMethodData($shipping_method_id)
	{
		if (!$shipping_method_id) {
			return false;
		}

		//Invalid Shipping method ID
		if (!strpos($shipping_method_id, '__')) {
			$code   = $shipping_method_id;
			$method = false;
		} else {
			list($code, $method) = explode("__", $shipping_method_id, 2);
		}

		$classname = "Catalog_Model_Shipping_" . $this->tool->formatClassname($code);

		if (method_exists($this->$classname, 'data')) {
			return $this->$classname->data($method);
		}

		return false;
	}

	public function setShippingMethod($method = null)
	{
		if (!$method) {
			unset($this->session->data['shipping_method_id']);
		} else {
			$shipping_methods = $this->getShippingMethods();

			if (is_string($method)) {
				if (!isset($shipping_methods[$method])) {
					$this->_e('SM-1a', 'shipping_method', 'error_shipping_method');
					return false;
				}

				$shipping_method_id = $method;
			} else {
				$shipping_method_id = $method['code'] . '__' . $method['method'];

				if (!isset($shipping_methods[$shipping_method_id])) {
					$this->_e('SM-1b', 'shipping_method', 'error_shipping_method');
					return false;
				}
			}

			$this->session->data['shipping_method_id'] = $shipping_method_id;
		}

		return true;
	}

	public function validateShippingDetails()
	{
		if ($this->hasShipping()) {
			if (!$this->validateShippingAddress()) {
				$this->_e('CO-10', 'checkout', 'error_shipping_address');
				return false;
			}

			if (!$this->getShippingMethod()) {
				$this->_e('CO-11', 'checkout', 'error_shipping_method');
				return false;
			}
		}

		return true;
	}

	public function validatePaymentDetails()
	{
		if (!$this->validatePaymentAddress()) {
			$this->_e('CO-12', 'checkout', 'error_payment_address');
			return false;
		}

		if (!$this->getPaymentMethod()) {
			$this->_e('CO-13', 'checkout', 'error_payment_method');
			return false;
		}

		return true;
	}

	public function isAllowedShippingZone($shipping_address)
	{
		if (!empty($shipping_address['country_id']) && !empty($shipping_address['zone_id'])) {
			return $this->address->inGeoZone($shipping_address, $this->config->get('config_allowed_shipping_zone'));
		}

		return false;
	}

	public function getAllowedShippingZones()
	{
		$geo_zone_id = $this->config->get('config_allowed_shipping_zone');

		if ($geo_zone_id > 0) {
			$allowed_geo_zones = $this->cache->get('zone.allowed.' . $geo_zone_id);

			if (!$allowed_geo_zones) {
				$allowed_geo_zones = array();

				$zones = $this->Model_Localisation_Zone->getZonesByGeoZone($geo_zone_id);

				foreach ($zones as $zone) {
					$country = $this->Model_Localisation_Country->getCountry($zone['country_id']);

					$allowed_geo_zones[] = array(
						'country' => $country,
						'zone'    => $zone
					);
				}

				$this->cache->set('zone.allowed.' . $geo_zone_id, $allowed_geo_zones);
			}

			return $allowed_geo_zones;
		}

		return array();
	}

	public function validatePaymentAddress($address = null)
	{
		unset($this->error['payment_address']);

		if (empty($address)) {
			if ($this->hasPaymentAddress()) {
				$address = $this->getPaymentAddress();
			} else {
				$this->_e('PA-1', 'payment_address', 'error_payment_address');
				return false;
			}
		}

		$country_id = !empty($address['country_id']) ? (int)$address['country_id'] : 0;
		$zone_id    = !empty($address['zone_id']) ? (int)$address['zone_id'] : 0;

		if (!$this->db->queryVar("SELECT COUNT(*) as total FROM " . DB_PREFIX . "country WHERE country_id = '$country_id'")) {
			$this->_e('PA-2', 'payment_address', 'error_country_id');
			return false;
		}

		if (!$this->db->queryVar("SELECT COUNT(*) as total FROM " . DB_PREFIX . "zone WHERE zone_id = '$zone_id' AND country_id = '$country_id'")) {
			$this->_e('PA-3', 'payment_address', 'error_zone_id');
			return false;
		}

		return true;
	}

	public function validateShippingAddress($address = null)
	{
		unset($this->error['shipping_address']);

		if (empty($address)) {
			if ($this->hasShippingAddress()) {
				$address = $this->getShippingAddress();
			} else {
				$this->_e('SA-1', 'shipping_address', 'error_shipping_address');
				return false;
			}
		}

		$country_id = !empty($address['country_id']) ? (int)$address['country_id'] : 0;
		$zone_id    = !empty($address['zone_id']) ? (int)$address['zone_id'] : 0;

		if (!$this->db->queryVar("SELECT COUNT(*) as total FROM " . DB_PREFIX . "country WHERE country_id = '$country_id'")) {
			$this->_e('SA-2', 'shipping_address', 'error_country_id');
			return false;
		}

		if (!$this->db->queryVar("SELECT COUNT(*) as total FROM " . DB_PREFIX . "zone WHERE zone_id = '$zone_id' AND country_id = '$country_id'")) {
			$this->_e('SA-3', 'shipping_address', 'error_zone_id');
			return false;
		}

		if (!$this->isAllowedShippingZone($address)) {
			$this->_e('SA-4', 'shipping_address', 'error_shipping_geo_zone');
			return false;
		}

		return true;
	}


	/**********************
	 *       Vouchers     *
	 **********************/

	public function hasVouchers($voucher_id = null)
	{
		if ($voucher_id) {
			return !empty($this->session->data['vouchers'][$voucher_id]);
		}

		return !empty($this->session->data['vouchers']);
	}

	public function getVoucherIds()
	{
		return isset($this->session->data['vouchers']) ? $this->session->data['vouchers'] : array();
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
		if (!isset($this->session->data['vouchers'])) {
			$this->session->data['vouchers'][] = $voucher_id;
		} else {
			$this->session->data['vouchers'] = array($voucher_id);
		}
	}

	public function removeVoucher($voucher_id)
	{
		unset($this->session->data['vouchers'][$voucher_id]);
	}

	public function removeAllVouchers()
	{
		unset($this->session->data['vouchers']);
	}

	/**
	 * Guest API
	 */

	public function saveGuestInfo($info)
	{
		$this->session->data['guest_info'] = $info;
	}

	public function loadGuestInfo()
	{
		return isset($this->session->data['guest_info']) ? $this->session->data['guest_info'] : null;
	}

	/**
	 * Comments
	 */

	public function getComment()
	{
		return !empty($this->session->data['comment']) ? $this->session->data['comment'] : null;
	}

	public function setComment($comment)
	{
		$this->session->data['comment'] = strip_tags($comment);
	}

	/** Policies **/
	public function getShippingPolicy($shipping_policy_id)
	{
		$shipping_policies = $this->getShippingPolicies();

		if (isset($shipping_policies[$shipping_policy_id])) {
			$policy = $shipping_policies[$shipping_policy_id];

			$policy['description'] = html_entity_decode($policy['description'], ENT_QUOTES, 'UTF-8');

			return $policy;
		}

		return null;
	}

	public function getProductShippingPolicy($product_id)
	{
		$shipping_policy_id = $this->db->queryVar("SELECT shipping_policy_id FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$product_id);

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

			return $policy;
		}

		return null;
	}

	public function getProductReturnPolicy($product_id)
	{
		$return_policy_id = $this->db->queryVar("SELECT return_policy_id FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$product_id);

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
}
