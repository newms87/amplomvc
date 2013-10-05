<?php
class System_Extension_Total_Total extends Extension
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		$_ = $this->language->system_fetch('extension/total/total');

		$total_data['total'] = array(
			'title'		=> $_['text_total'],
			'value'		=> max(0, $total),
		);
	}
}