<?php
class ModelShippingAmount extends Model 
{
	
	public function getQuote($address)
	{
		$this->load->language('shipping/amount');
		
		$valid_zone = $this->model_localisation_zone->inGeoZone($this->config->get('amount_geo_zone_id'), $address['country_id'], $address['zone_id']);
	
		$quote_data = array();
	
		if ($valid_zone) {
			$cost = false;
			$pricesets = $this->config->get('amount_priceset');
			
			$total_price = $this->cart->getSubTotal();
			
			foreach ($pricesets as $set) {
				switch($set['range']){
					case 'range':
						if ($total_price >= $set['from'] && $total_price <= $set['to']) {
							if($set['type'] == 'fixed')
								$cost = $set['cost'];
							else
								$cost = ($set['cost']/100)*$total_price;
						}
						break;
					case 'lt':
						if ($total_price < $set['total']) {
							if($set['type'] == 'fixed')
								$cost = $set['cost'];
							else
								$cost = ($set['cost']/100)*$total_price;
						}
						break;
					case 'lte':
						if ($total_price <= $set['total']) {
							if($set['type'] == 'fixed')
								$cost = $set['cost'];
							else
								$cost = ($set['cost']/100)*$total_price;
						}
						break;
					case 'gt':
						if ($total_price > $set['total']) {
							if($set['type'] == 'fixed')
								$cost = $set['cost'];
							else
								$cost = ($set['cost']/100)*$total_price;
						}
						break;
					case 'gte':
						if ($total_price >= $set['total']) {
							if($set['type'] == 'fixed')
								$cost = $set['cost'];
							else
								$cost = ($set['cost']/100)*$total_price;
						}
						break;
					case 'eq':
						if ($total_price == $set['total']) {
							if($set['type'] == 'fixed')
								$cost = $set['cost'];
							else
								$cost = ($set['cost']/100)*$total_price;
						}
						break;
					default:
						break;
				}
				
				if($cost !== false)
					break;
			}
			
			$zonerules = $this->config->get('amount_zonerule');
			$orig_cost = $cost;
			$fixed = false;
			foreach ($zonerules as $rule) {
				if($address['country_id'] != $rule['country_id'] || ($address['zone_id'] != $rule['zone_id'] && $rule['zone_id']!=0))
					continue;
				switch($rule['mod']){
					case 'add':
						if($rule['type'] == 'fixed')
							$cost += $rule['cost'];
						else
							$cost += $orig_cost*($rule['cost']/100);
						break;
					case 'subtract':
						if($rule['type'] == 'fixed')
							$cost -= $rule['cost'];
						else
							$cost -= $orig_cost*($rule['cost']/100);
						break;
					case 'fixed':
						$fixed = true;
						if($rule['type'] == 'fixed')
							$cost = $rule['cost'];
						else
							$cost = $orig_cost*($rule['cost']/100);
						break;
					default:
						break;
				}
				if($fixed)
					break;
			}
			
			if ($cost !== false) {
				$quote_data = array();
				
				$quote_data[] = array(
					'code'			=> 'amount',
					'code_title'	=> $this->_('text_title'),
					'method'			=> 'amount',
					'title'			=> $this->_('text_title_amount'),
					'cost'			=> $cost,
					'tax_class_id' => $this->config->get('amount_tax_class_id'),
					'text'			=> $this->currency->format($cost),
					'sort_order' 	=> 0,
				);
			}
		}
	
		return $quote_data;
	}
}
