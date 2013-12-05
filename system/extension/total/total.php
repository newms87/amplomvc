<?php
class System_Extension_Total_Total extends TotalExtension
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		$data = array(
			'value'		=> max(0, $total),
		);

		return $data;
	}
}
