#<?php // editor friendly :)

//=====
class ControllerAccountRegister extends Controller {
   
//.....

   public function validate() {
      if ((strlen($_POST['firstname']) < 1) || (strlen($_POST['firstname']) > 32)) {
            $this->error['firstname'] = $this->_('error_firstname');
      }

      if ((strlen($_POST['lastname']) < 1) || (strlen($_POST['lastname']) > 32)) {
            $this->error['lastname'] = $this->_('error_lastname');
      }

      if ((strlen($_POST['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['email'])) {
            $this->error['email'] = $this->_('error_email');
      }

      if ($this->model_account_customer->getTotalCustomersByEmail($_POST['email'])) {
            $this->error['email'] = $this->_('error_exists');
      }
//-----
//>>>>>php
      if($this->config->get('config_promo_registration')){
         $check = array('firstname','lastname','email','password','confirm');
         
         foreach($this->error as $key=>$e){
            if(!in_array($key,$check))
               unset($this->error[$key]);
         }
      }
//-----
//=====
   }
//.....
}
//-----