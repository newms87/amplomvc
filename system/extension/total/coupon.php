<?php
class System_Extension_Total_Coupon extends TotalExtension
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		$this->language->system('extension/total/coupon');

		$this->System_Model_Coupon->loadAutoCoupons();

		$coupon_list = array();

		if (isset($this->session->data['coupons'])) {
			foreach ($this->session->data['coupons'] as $code=>$coupon) {
				$coupon_info = $this->System_Model_Coupon->getCoupon($code);
				if ($coupon_info) {
					$coupon_list[$code] = $coupon_info;
				}
			}
		}

		if ($coupon_list) {
			$products = $this->cart->getProducts();

			foreach ($coupon_list as $coupon_info) {
				$discount_total = 0;

				if (!$coupon_info['product']) {
					$sub_total = $this->cart->getSubTotal();
				} else {
					$sub_total = 0;


					foreach ($products as $product) {
						if (in_array($product['product_id'], $coupon_info['product'])) {
							$sub_total += $product['total'];
						}
					}
				}

				if ($coupon_info['type'] == 'F') {
					$coupon_info['discount'] = min($coupon_info['discount'], $sub_total);
				}

				foreach ($products as $product) {
					$discount = 0;

					if (!$coupon_info['product']) {
						$status = true;
					} else {
						if (in_array($product['product_id'], $coupon_info['product'])) {
							$status = true;
						} else {
							$status = false;
						}
					}

					if ($status) {
						if ($coupon_info['type'] == 'F') {
							$discount = $coupon_info['discount'] * ($product['total'] / $sub_total);
						} elseif ($coupon_info['type'] == 'P') {
							$discount = $product['total'] / 100 * $coupon_info['discount'];
						}

						if ($product['tax_class_id']) {
							$tax_rates = $this->tax->getRates($product['total'] - ($product['total'] - $discount), $product['tax_class_id']);

							foreach ($tax_rates as $tax_rate) {
								if ($tax_rate['type'] == 'P') {
									$taxes[$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
								}
							}
						}
					}

					$discount_total += $discount;
				}

				if ($coupon_info['shipping'] && $this->cart->hasShippingMethod()) {
					$shipping_method = $this->cart->getShippingMethod();

					if (!empty($shipping_method['tax_class_id'])) {
						$tax_rates = $this->tax->getRates($shipping_method['cost'], $shipping_method['tax_class_id']);

						foreach ($tax_rates as $tax_rate) {
							if ($tax_rate['type'] == 'P') {
								$taxes[$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
							}
						}
					}

					if ($this->cart->hasShippingAddress()) {
						$address = $this->cart->getShippingAddress();
					}
					else {
						$address = array(
							'zone_id'	=> 0,
							'country_id' => 0,
						);
					}

					if ($this->address->inGeoZone($address, $coupon_info['shipping_geozone'])) {
						$discount_total += $shipping_method['cost'];
					}
				}

				$data = array(
					'method_id' => $coupon_info['code'],
					'title'		=> $this->_('text_coupon_title', $coupon_info['code']),
					'value'		=> -$discount_total,
				);

				$total_data['code__' . $coupon_info['code']] = $data + $this->info();

				$total -= $discount_total;
			}
		}
	}

	//TODO - Model Cart Coupon should be moved to system functions
	public function confirm($order_info, $order_total)
	{
		if ($order_total['method_id']) {
			//TODO: This should be moved to $this->cart->getCoupon()
			$coupon_info = $this->System_Model_Coupon->getCoupon($order_total['method_id']);

			if ($coupon_info) {
				$this->System_Model_Coupon->redeem($coupon_info['coupon_id'], $order_info['order_id'], $order_info['customer_id'], $order_total['value']);
			}
		}
	}
}
