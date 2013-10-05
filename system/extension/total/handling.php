<?php
class System_Extension_Total_Handling extends Extension
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		if (($this->cart->getSubTotal() < $this->config->get('handling_total')) && ($this->cart->getSubTotal() > 0)) {
			$_ = $this->language->system_fetch('extension/total/handling');

			$total_data[] = array(
				'code'		=> 'handling',
				'title'		=> $_['text_handling'],
				'value'		=> $this->config->get('handling_fee'),
				'sort_order' => $this->config->get('handling_sort_order')
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
