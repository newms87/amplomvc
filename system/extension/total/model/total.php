<?php
<<<<<<< HEAD:catalog/model/total/total.php
class Catalog_Model_Total_Total extends Model
=======
class System_Extension_Total_Model_Total extends Model 
>>>>>>> 35786c33a0470bb6e46908697b6ed90950ffb231:system/extension/total/model/total.php
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