<?php
class System_Extension_Total_LowOrderFee extends System_Extension_Total
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		$subtotal = isset($total_data['sub_total']['value']) ? (int)$total_data['sub_total']['value'] : 0;

		if ($subtotal < $this->settings['total']) {
			$total += $this->settings['fee'];

			if ($this->settings['tax_class_id']) {
				$tax_rates = $this->tax->getRates($this->settings['fee'], $this->settings['tax_class_id']);

				foreach ($tax_rates as $tax_rate) {
					if (!isset($taxes[$tax_rate['tax_rate_id']])) {
						$taxes[$tax_rate['tax_rate_id']] = $tax_rate['amount'];
					} else {
						$taxes[$tax_rate['tax_rate_id']] += $tax_rate['amount'];
					}
				}
			}

			$data = array(
				'value' => $this->settings['fee'],
			);

			return $data;
		}
	}
}
