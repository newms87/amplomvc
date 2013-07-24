<?php
class System_Extension_Total_Model_Total extends Model 
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		$this->language->load('total/total');
	
		$total_data[] = array(
			'code'		=> 'total',
			'title'		=> $this->_('text_total'),
			'value'		=> max(0, $total),
			'sort_order' => $this->config->get('total_sort_order')
		);
	}
}