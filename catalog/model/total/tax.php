<?php
class ModelTotalTax extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		foreach ($taxes as $key => $value) {
			if ($value > 0) {
				
				$tax_info = $this->tax->getRateInfo($key);
				
				$total_data[] = array(
					'code'		=> 'tax',
					'title'		=> $tax_info['name'] . ($tax_info['type'] == 'P' ? ' @ ' . $tax_info['rate'] . '%' : ''),
					'text'		=> $this->currency->format($value),
					'value'		=> $value,
					'sort_order' => $this->config->get('tax_sort_order')
				);

				$total += $value;
			}
		}
	}
}