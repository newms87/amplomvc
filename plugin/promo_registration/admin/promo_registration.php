<?php
class Admin_PromoRegistration extends Controller 
{
	
	public function settings()
	{
		$this->language->plugin('promo_registration', 'admin/promo_reg');
		
		$configs = array('config_promo_registration', 'config_promo_registration_coupon_id','config_promo_registration_text', 'config_promo_registration_title');
		foreach ($configs as $c) {
			$this->data[$c] = isset($_POST[$c])?$_POST[$c]:$this->config->get($c);
		}
		
		$this->data['coupons'] = $this->Model_Sale_Coupon->getCoupons();
	}
}
