<?php
class System_Extension_Total_Total extends System_Extension_Total
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		$data = array(
			'amount' => max(0, $total),
		);

		return $data;
	}
}
