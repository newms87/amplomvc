<?php
class System_Extension_Total_Model_LowOrderFee extends Model
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		if ($this->cart->getSubTotal() && ($this->cart->getSubTotal() < $this->config->get('low_order_fee_total'))) {
			$this->language->load('total/low_order_fee');
			
			$total_data[] = array(
				'code'		=> 'low_order_fee',
				'title'		=> $this->_('text_low_order_fee'),
				'value'		=> $this->config->get('low_order_fee_fee'),
				'sort_order' => $this->config->get('low_order_fee_sort_order')
			);
			
			if ($this->config->get('low_order_fee_tax_class_id')) {
				$tax_rates = $this->tax->getRates($this->config->get('low_order_fee_fee'), $this->config->get('low_order_fee_tax_class_id'));
				
				foreach ($tax_rates as $tax_rate) {
					if (!isset($taxes[$tax_rate['tax_rate_id']])) {
						$taxes[$tax_rate['tax_rate_id']] = $tax_rate['amount'];
					} else {
						$taxes[$tax_rate['tax_rate_id']] += $tax_rate['amount'];
					}
				}
			}
			
			$total += $this->config->get('low_order_fee_fee');
		}
	}
}