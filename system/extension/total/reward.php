<?php
class System_Extension_Total_Reward extends System_Extension_Total
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		if ($this->session->has('reward')) {
			$points = $this->customer->getRewardPoints();

			if ($this->session->get('reward') <= $points) {
				$discount_total = 0;

				$points_total = 0;

				foreach ($this->cart->getProducts() as $product) {
					if ($product['points']) {
						$points_total += $product['points'];
					}
				}

				$points = min($points, $points_total);

				foreach ($this->cart->getProducts() as $product) {
					$discount = 0;

					if ($product['points']) {
						$discount = $product['total'] * ($this->session->get('reward') / $points_total);

						if ($product['tax_class_id']) {
							$tax_rates = $this->tax->getRates($discount, $product['tax_class_id']);

							foreach ($tax_rates as $tax_rate) {
								if ($tax_rate['type'] == 'P') {
									$taxes[$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
								}
							}
						}
					}

					$discount_total += $discount;
				}

				$total_data['reward'] = array(
					'method_id' => $this->session->get('reward'),
					'title'     => _l("Reward (%s)", $this->session->get('reward')),
					'amount'     => -$discount_total,
				);

				$total -= $discount_total;
			}
		}
	}

	//TODO - The reward should be the method_id (not grabbed from title..)
	public function confirm($order_info, $order_total)
	{
		if ($order_total['method_id']) {
			$customer_reward = array(
				'customer_id' => $order_info['customer_id'],
				'description' => _l("Order %s", $order_info['order_id']),
				'points'      => -$order_total['method_id'],
				'date_added'  => $this->date->now(),
			);

			$this->insert('customer_reward', $customer_reward);
		}
	}
}
