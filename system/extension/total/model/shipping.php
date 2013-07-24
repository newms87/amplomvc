<?php
<<<<<<< HEAD:catalog/model/total/shipping.php
class Catalog_Model_Total_Shipping extends Model
=======
class System_Extension_Total_Model_Shipping extends Model
>>>>>>> 35786c33a0470bb6e46908697b6ed90950ffb231:system/extension/total/model/shipping.php
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