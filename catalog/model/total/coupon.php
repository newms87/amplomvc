<?php
class Catalog_Model_Total_Coupon extends Model 
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		$this->load->language('total/coupon');
		
		$this->Model_Cart_Coupon->loadAutoCoupons();
		
		$coupon_list = array();
		
		if (isset($this->session->data['coupons'])) {
			foreach ($this->session->data['coupons'] as $code=>$coupon) {
				$coupon_info = $this->Model_Cart_Coupon->getCoupon($code);
				if ($coupon_info) {
					$coupon_list[$code] = $coupon_info;
				}
			}
		}
		
		if ($coupon_list) {
			foreach ($coupon_list as $coupon_info) {
				$discount_total = 0;
				
				if (!$coupon_info['product']) {
					$sub_total = $this->cart->getSubTotal();
				} else {
					$sub_total = 0;
					
					
					foreach ($this->cart->getProducts() as $product) {
						if (in_array($product['product_id'], $coupon_info['product'])) {
							$sub_total += $product['total'];
						}
					}
				}
				
				if ($coupon_info['type'] == 'F') {
					$coupon_info['discount'] = min($coupon_info['discount'], $sub_total);
				}
				
				foreach ($this->cart->getProducts() as $product) {
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
							
							foreach ($tax_rates as $tax_rate) 
{
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
						
						foreach ($tax_rates as $tax_rate) 
{
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
					
					if ($this->Model_Localisation_Zone->inGeoZone($coupon_info['shipping_geozone'], $address['country_id'], $address['zone_id'])) {
						$discount_total += $shipping_method['cost'];
					}
				}
				
				$total_data[] = array(
					'code'		=> 'coupon',
					'title'		=> sprintf($this->_('text_coupon'), $coupon_info['code']),
					'text'		=> $this->currency->format(-$discount_total),
					'value'		=> -$discount_total,
					'sort_order' => $this->config->get('coupon_sort_order')
				);

				$total -= $discount_total;
			}
		}
	}
	
	public function confirm($order_info, $order_total)
	{
		$code = '';
		
		$start = strpos($order_total['title'], '(') + 1;
		$end = strrpos($order_total['title'], ')');
		
		if ($start && $end) {
			$code = substr($order_total['title'], $start, $end - $start);
		}
		
		$coupon_info = $this->Model_Cart_Coupon->getCoupon($code);
			
		if ($coupon_info) {
			$this->Model_Cart_Coupon->redeem($coupon_info['coupon_id'], $order_info['order_id'], $order_info['customer_id'], $order_total['value']);
		}
	}
}
