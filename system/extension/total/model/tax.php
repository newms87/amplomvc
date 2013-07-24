<?php
class System_Extension_Total_Model_Tax extends Model
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		foreach ($taxes as $key => $value) {
			if ($value > 0) {
				
				$tax_info = $this->tax->getRateInfo($key);
				
				$total_data[] = array(
					'code'		=> 'tax',
					'title'		=> $tax_info['name'] . ($tax_info['type'] == 'P' ? ' @ ' . $tax_info['rate'] . '%' : ''),
					'value'		=> $value,
					'sort_order' => $this->config->get('tax_sort_order')
				);

				$total += $value;
			}
		}
	}
}