<?php
class System_Extension_Shipping_Amount extends System_Extension_Shipping
{
	public function getQuote($address)
	{
		$valid_zone = $this->address->inGeoZone($address, $this->config->get('amount_geo_zone_id'));

		$quote_data = array();

		if ($valid_zone) {
			$cost      = false;
			$pricesets = $this->config->get('amount_priceset');

			$total_price = $this->cart->getSubTotal();

			foreach ($pricesets as $set) {
				switch ($set['range']) {
					case 'range':
						$is_valid = $total_price >= $set['from'] && $total_price <= $set['to'];
						break;
					case 'lt':
						$is_valid = $total_price < $set['total'];
						break;
					case 'lte':
						$is_valid = $total_price <= $set['total'];
						break;
					case 'gt':
						$is_valid = $total_price > $set['total'];
						break;
					case 'gte':
						$is_valid = $total_price >= $set['total'];
						break;
					case 'eq':
						$is_valid = $total_price == $set['total'];
						break;
					default:
						$is_valid = false;
						break;
				}

				if ($is_valid) {
					if ($set['type'] == 'fixed') {
						$cost = $set['cost'];
					} else {
						$cost = ($set['cost'] / 100) * $total_price;
					}

					break;
				}
			}

			$zonerules = $this->config->get('amount_zonerule');
			$orig_cost = $cost;
			$fixed     = false;
			foreach ($zonerules as $rule) {
				if ($address['country_id'] != $rule['country_id'] || ($address['zone_id'] != $rule['zone_id'] && $rule['zone_id'] != 0)) {
					continue;
				}
				switch ($rule['mod']) {
					case 'add':
						if ($rule['type'] == 'fixed') {
							$cost += $rule['cost'];
						} else {
							$cost += $orig_cost * ($rule['cost'] / 100);
						}
						break;
					case 'subtract':
						if ($rule['type'] == 'fixed') {
							$cost -= $rule['cost'];
						} else {
							$cost -= $orig_cost * ($rule['cost'] / 100);
						}
						break;
					case 'fixed':
						$fixed = true;
						if ($rule['type'] == 'fixed') {
							$cost = $rule['cost'];
						} else {
							$cost = $orig_cost * ($rule['cost'] / 100);
						}
						break;
					default:
						break;
				}
				if ($fixed) {
					break;
				}
			}

			if ($cost !== false) {
				$quote_data = array();

				$quote_data[] = array(
					'code'         => 'amount',
					'code_title'   => "Flat Rate Shipping",
					'method'       => 'amount',
					'title'        => "Standard Shipping",
					'cost'         => $cost,
					'tax_class_id' => $this->config->get('amount_tax_class_id'),
					'text'         => $this->currency->format($cost),
					'sort_order'   => 0,
				);
			}
		}

		return $quote_data;
	}
}
