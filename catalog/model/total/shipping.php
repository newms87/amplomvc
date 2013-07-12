<?php
class Catalog_Model_Total_Shipping extends Model 
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		if ($this->cart->hasShipping() && $this->cart->hasShippingMethod()) {
			$shipping_method = $this->cart->getShippingMethod();
			
			$total_data[] = array(
				'code'		=> 'shipping',
				'method_id' => $this->cart->getShippingMethodId(),
				'title'		=> $shipping_method['title'],
				'value'		=> $shipping_method['cost'],
				'sort_order' => $this->config->get('shipping_sort_order')
			);

			if ($shipping_method['tax_class_id']) {
				$this->tax->apply($taxes, $shipping_method['cost'], $shipping_method['tax_class_id']);
			}
			
			$total += $shipping_method['cost'];
		}
	}
}