<?php
class Catalog_Model_Shipping_Flat extends System_Extension_Shipping
{
	private $flat_info;
	private $rates;

	function __construct($registry)
	{
		parent::__construct($registry);

		$this->flat_info = $this->config->loadGroup('shipping_flat');

		$this->rates = array();

		foreach ($this->flat_info['flat_rates'] as $rate) {
			$this->rates[$rate['method']] = $rate;
		}
	}

	public function getQuotes($address)
	{
		$quote_data = array();

		$total_products = (int)$this->cart->countProducts();
		$total_weight   = (int)$this->cart->getWeight();

		foreach ($this->rates as $key => $rate) {
			$valid = true;

			//Wrong Shipping Zone
			if (!$this->address->inGeoZone($address, $rate['geo_zone_id'])) {
				continue;
			}

			switch ($rate['rule']['type']) {
				case 'item_qty':
					list($min, $max) = explode(',', $rate['rule']['value'], 2);

					if ($total_products < (int)$min || ($max && $total_products > (int)$max)) {
						$valid = false;
					}
					break;
				case 'weight':
					list($min, $max) = explode(',', $rate['rule']['value'], 2);

					if ($total_weight < (int)$min || ($max && $total_weight > (int)$max)) {
						$valid = false;
					}
					break;
				default:
					break;
			}

			if (!$valid) {
				continue;
			}

			$quote_data[] = $this->data($key);
		}

		return $quote_data;
	}

	public function data($method)
	{
		if (!isset($this->rates[$method])) {
			return null;
		}

		$method_info = $this->rates[$method];

		$method_data = array(
			'code'         => 'flat',
			'code_title'   => $this->flat_info['flat_title'],
			'method'       => $method_info['method'],
			'title'        => $method_info['title'],
			'cost'         => $method_info['cost'],
			'text'         => $this->currency->format($method_info['cost']),
			'tax_class_id' => $method_info['tax_class_id'],
			'sort_order'   => $this->flat_info['flat_sort_order'],
		);

		return $method_data;
	}
}
