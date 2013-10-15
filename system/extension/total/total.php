<?php
class System_Extension_Total_Total extends Extension
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		$data = array(
			'value'		=> max(0, $total),
		);

		return $data;
	}
}
