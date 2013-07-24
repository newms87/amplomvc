<?php
<<<<<<< HEAD:catalog/model/total/handling.php
class Catalog_Model_Total_Handling extends Model
=======
class System_Extension_Total_Model_Handling extends Model
>>>>>>> 35786c33a0470bb6e46908697b6ed90950ffb231:system/extension/total/model/handling.php
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		if (($this->cart->getSubTotal() < $this->config->get('handling_total')) && ($this->cart->getSubTotal() > 0)) {
			$this->language->load('total/handling');
			
			$total_data[] = array(
				'code'		=> 'handling',
				'title'		=> $this->_('text_handling'),
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