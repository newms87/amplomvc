<?php
class Catalog_Model_Total_Total extends Model 
{
	public function getTotal(&$total_data, &$total, &$taxes)
	{
		$this->load->language('total/total');
	
		$total_data[] = array(
			'code'		=> 'total',
			'title'		=> $this->_('text_total'),
			'text'		=> $this->currency->format(max(0, $total)),
			'value'		=> max(0, $total),
			'sort_order' => $this->config->get('total_sort_order')
		);
	}
}