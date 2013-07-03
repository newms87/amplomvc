<?php
class Catalog_Model_Shipping_Flat extends Model 
{
	function getQuote($address)
	{
		$flat_rates = $this->Model_Setting_Setting->getSetting('shipping_flat');
		
		$quote_data = array();
		
		$sort_order = $flat_rates['flat_sort_order'];
		
		$total_products = (int)$this->cart->countProducts();
		$total_weight = (int)$this->cart->getWeight();
		
		foreach ($flat_rates['flat_rates'] as $rate) {
			$valid = true;
			
			//Wrong Shipping Zone
			if(!$this->Model_Localisation_Zone->inGeoZone($rate['geo_zone_id'], $address['country_id'], $address['zone_id'])) continue;
			
			switch($rate['rule']['type']){
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
			
			if(!$valid) continue;
			
			$quote_data[] = array(
				'code'			=> 'flat',
				'code_title'	=> $flat_rates['flat_title'],
				'method'			=> $rate['method'],
				'title'			=> $rate['title'],
				'cost'			=> $rate['cost'],
				'tax_class_id' => $rate['tax_class_id'],
				'text'			=> $this->currency->format($rate['cost']),
				'sort_order' 	=> $sort_order,
			);
			
			$sort_order += .1;
		}
	
		return $quote_data;
	}

	public function getTitle($method)
	{
		$flat_rates = $this->Model_Setting_Setting->getSetting('shipping_flat');
		
		foreach ($flat_rates['flat_rates'] as $rate) {
			if ($rate['method'] === $method) {
				return $rate['title'];
			}
		}
	}
}