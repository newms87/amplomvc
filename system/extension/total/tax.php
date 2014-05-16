<?php
class System_Extension_Total_Tax extends System_Extension_Total
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		foreach ($taxes as $key => $value) {
			if ($value > 0) {

				$tax_info = $this->tax->getRateInfo($key);

				$total_data['tax'] = array(
					'title' => $tax_info['name'] . ($tax_info['type'] == 'P' ? ' @ ' . $tax_info['rate'] . '%' : ''),
					'amount' => $value,
				);

				$total += $value;
			}
		}
	}
}
