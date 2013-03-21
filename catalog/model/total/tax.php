<?php
class ModelTotalTax extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		foreach ($taxes as $key => $value) {
			if ($value > 0) {
			   
            //TODO: Make this more intuitive
            $r = $this->session->data['applied_tax_rates'][$key];
            
				$total_data[] = array(
					'code'       => 'tax',
					'title'      => $this->tax->getRateName($key) . ($r['type']=='P'?' @ ' . $r['rate'].'%':''), //TODO - need to add tax rate to data? 
					'text'       => $this->currency->format($value),
					'value'      => $value,
					'sort_order' => $this->config->get('tax_sort_order')
				);

				$total += $value;
			}
		}
	}
}