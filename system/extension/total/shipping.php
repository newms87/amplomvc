<?php
class System_Extension_Total_Shipping extends System_Extension_Total
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		if ($this->cart->hasShipping() && $this->cart->hasShippingMethod()) {
			$shipping_method = $this->cart->getShippingQuote();


			//TODO: Implement tax class for shipping!
			if (!empty($shipping_method['tax_class_id'])) {
				$this->tax->apply($taxes, $shipping_method['cost'], $shipping_method['tax_class_id']);
			}

			$total += $shipping_method['cost'];

			$data = array(
				'title'      => $shipping_method['title'],
				'amount'      => $shipping_method['cost'],
			);

			return $data;
		}
	}
}
