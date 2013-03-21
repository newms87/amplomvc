<?php
class ModelShippingAmount extends Model {
	function getQuote($address) {
		$this->load->language('shipping/amount');
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('amount_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
	
		$method_data = array();
	
		if (!$this->config->get('amount_geo_zone_id') || $query->num_rows) {
		   $cost = false;
			$pricesets = $this->config->get('amount_priceset');
         
         $total_price = $this->cart->getSubTotal();
         
         foreach($pricesets as $set){
            switch($set['range']){
               case 'range':
                  if($total_price >= $set['from'] && $total_price <= $set['to']){
                     if($set['type'] == 'fixed')
                        $cost = $set['cost'];
                     else
                        $cost = ($set['cost']/100)*$total_price;
                  }
                  break;
               case 'lt':
                  if($total_price < $set['total']){
                     if($set['type'] == 'fixed')
                        $cost = $set['cost'];
                     else
                        $cost = ($set['cost']/100)*$total_price;
                  }
                  break;
               case 'lte':
                  if($total_price <= $set['total']){
                     if($set['type'] == 'fixed')
                        $cost = $set['cost'];
                     else
                        $cost = ($set['cost']/100)*$total_price;
                  }
                  break;
               case 'gt':
                  if($total_price > $set['total']){
                     if($set['type'] == 'fixed')
                        $cost = $set['cost'];
                     else
                        $cost = ($set['cost']/100)*$total_price;
                  }
                  break;
               case 'gte':
                  if($total_price >= $set['total']){
                     if($set['type'] == 'fixed')
                        $cost = $set['cost'];
                     else
                        $cost = ($set['cost']/100)*$total_price;
                  }
                  break;
               case 'eq':
                  if($total_price == $set['total']){
                     if($set['type'] == 'fixed')
                        $cost = $set['cost'];
                     else
                        $cost = ($set['cost']/100)*$total_price;
                  }
                  break;
               default:
                  break;
            }
            
            if($cost !== false)
               break;
         }
         
         $zonerules = $this->config->get('amount_zonerule');
         $orig_cost = $cost;
         $fixed = false;
         foreach($zonerules as $rule){
            if($address['country_id'] != $rule['country_id'] || ($address['zone_id'] != $rule['zone_id'] && $rule['zone_id']!=0))
               continue; 
            switch($rule['mod']){
               case 'add':
                  if($rule['type'] == 'fixed')
                     $cost += $rule['cost'];
                  else
                     $cost += $orig_cost*($rule['cost']/100);
                  break;
               case 'subtract':
                  if($rule['type'] == 'fixed')
                     $cost -= $rule['cost'];
                  else
                     $cost -= $orig_cost*($rule['cost']/100);
                  break;
               case 'fixed':
                  $fixed = true;
                  if($rule['type'] == 'fixed')
                     $cost = $rule['cost'];
                  else
                     $cost = $orig_cost*($rule['cost']/100);
                  break;
               default:
                  break;
            }
            if($fixed)
               break;
         }
         
         if($cost !== false){
   			$quote_data = array();
   			
      		$quote_data['amount'] = array(
        		'code'         => 'amount.amount',
        		'title'        => $this->_('text_description'),
        		'cost'         => $cost,
        		'tax_class_id' => $this->config->get('flat_tax_class_id'),
   			'text'         => $this->currency->format($cost)
      		);
   
      		$method_data = array(
        		'code'       => 'amount',
        		'title'      => $this->_('text_title'),
        		'quote'      => $quote_data,
   			'sort_order' => $this->config->get('amount_sort_order'),
        		'error'      => false
      		);
         }
		}
	
		return $method_data;
	}
}
