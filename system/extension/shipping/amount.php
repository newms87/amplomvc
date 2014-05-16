<?php

class System_Extension_Shipping_Amount extends System_Extension_Shipping
{
	public function getQuotes($address)
	{
		if (!$this->validate($address)) {
			return array();
		}


		$total_price = $this->cart->getSubTotal();
		$cost        = false;

		if (empty($this->settings['priceset'])) {
			return array();
		}

		$pricesets = $this->settings['priceset'];

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

		if (!empty($this->settings['zonerule'])) {
			$zonerules = $this->settings['zonerule'];
			$orig_cost = $cost;
			foreach ($zonerules as $rule) {
				if ($address['country_id'] != $rule['country_id'] || ($address['zone_id'] != $rule['zone_id'] && $rule['zone_id'] != 0)) {
					continue;
				}
				switch ($rule['mod']) {
					case 'add':
						if ($rule['type'] === 'fixed') {
							$cost += $rule['cost'];
						} else {
							$cost += $orig_cost * ($rule['cost'] / 100);
						}
						break;
					case 'subtract':
						if ($rule['type'] === 'fixed') {
							$cost -= $rule['cost'];
						} else {
							$cost -= $orig_cost * ($rule['cost'] / 100);
						}
						break;
					case 'fixed':
						if ($rule['type'] === 'fixed') {
							$cost = $rule['cost'];
						} else {
							$cost = $orig_cost * ($rule['cost'] / 100);
						}
						//We are done for fixed price (exit foreach loop)
						break 2;
					default:
						break;
				}
			}
		}

		$quote_data = array();

		if ($cost !== false) {
			$key = 'amount-' . $cost;

			$quote_data[$key] = array(
				'shipping_key' => $key,
				'title'        => _l("Flat Rate Shipping"),
				'cost'         => $cost,
			);
		}

		return $quote_data;
	}
}
