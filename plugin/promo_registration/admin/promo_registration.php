<?php   
class ControllerPlugin_Promo_RegistrationAdminPromoRegistration extends Controller {
   
   public function settings(){
      $this->language->plugin('promo_registration', 'admin/promo_reg');
      
      $configs = array('config_promo_registration', 'config_promo_registration_coupon_id','config_promo_registration_text', 'config_promo_registration_title');
      foreach($configs as $c){
         $this->data[$c] = isset($_POST[$c])?$_POST[$c]:$this->config->get($c);
      }
      
      $this->data['coupons'] = $this->model_sale_coupon->getCoupons();
   }
}
