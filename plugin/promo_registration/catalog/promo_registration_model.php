<?php
class ModelPluginPromoRegistration extends Model {
	
	public function promo_addCustomer($data, $customer_id){
		if($this->config->get('config_promo_registration')){
			$coupon_id = $this->config->get('config_promo_registration_coupon_id');
			$this->query("INSERT INTO " . DB_PREFIX . "coupon_customer SET customer_id = '$customer_id', coupon_id='$coupon_id'");
		}
	}
}
