<?php
class System_Extension_Total_Handling extends System_Extension_Total
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		if (($this->cart->getSubTotal() < $this->config->get('handling_total')) && ($this->cart->getSubTotal() > 0)) {
			$total_data['handling'] = array(
				'title' => _l("Shipping & Handling"),
				'value' => $this->config->get('handling_fee'),
			);

			if ($this->config->get('handling_tax_class_id')) {
				$tax_rates = $this->tax->getRates($this->config->get('handling_fee'), $this->config->get('handling_tax_class_id'));

				foreach ($tax_rates as $tax_rate) {
					if (!isset($taxes[$tax_rate['tax_rate_id']])) {
						$taxes[$tax_rate['tax_rate_id']] = $tax_rate['amount'];
					} else {
						$taxes[$tax_rate['tax_rate_id']] += $tax_rate['amount'];
					}
				}
			}

			$total += $this->config->get('handling_fee');
		}
	}
}
