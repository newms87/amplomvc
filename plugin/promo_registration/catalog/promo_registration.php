<?php   
class ControllerPlugin_Promo_registrationCatalogPromoRegistration extends Controller {
   
   public function promo_header(){
      $allowed=array(1,32, 16, 26, 6, 27);
      $user_id = isset($this->session->data['user_id'])?$this->session->data['user_id']:0;
      if($this->config->get('config_promo_registration') && !in_array($user_id, $allowed)){
         $allowed = array('information/information');
         if(!(in_array($_GET['route'], $allowed) || strpos($_GET['route'],'account/')===0)){
            if($this->customer->isLogged() && $_GET['route'] == 'account/account'){
               $redirect = $this->url->link("account/success");
            }
            else{
               $redirect = $this->url->link("account/register");
            }
            $this->url->redirect($redirect, 302);
         }
         unset($this->data['page_header']);
         $this->data['my_links'] = array();
         $this->data['links'] = array();
      }
   }
   
   public function promo_footer(){
      $allowed=array(1,32, 16, 26, 6, 27);
      $user_id = isset($this->session->data['user_id'])?$this->session->data['user_id']:0;
      if($this->config->get('config_promo_registration') && !in_array($user_id, $allowed)){
         $footer = array();
         $footer[] = array('title'=>'Registration','href'=>$this->url->link("account/register"));
         foreach($this->data['footer_item'] as $item){
            if($item['title'] == 'Early Registration Promotion'){
               $footer[] = $item;
               break;
            }
         }
         $this->data['footer_item'] = $footer;
      }
   }
   
   public function promo_registration(){
      $allowed=array(1,32, 16, 26, 6, 27);
      $user_id = isset($this->session->data['user_id'])?$this->session->data['user_id']:0;
      if($this->config->get('config_promo_registration') && !in_array($user_id, $allowed)){
         
         $this->language->set('heading_title', $this->config->get('config_promo_registration_title'));
         
         $this->breadcrumb->add($this->_('text_early_registration'), $this->url->link('account/register'));
         
         $template =$this->config->get('config_theme') . '/template/account/register_promo.tpl';
         if (file_exists(DIR_THEME . $template)) {
            $this->template_file = $template;
         }
         
         $this->data['text_promo'] = html_entity_decode($this->config->get('config_promo_registration_text'));
      }
   }
   
   public function promo_success(){
      if($this->config->get('config_promo_registration')){
         $this->language->plugin('promo_registration', 'catalog/promo_reg');
      }
   }
   
   public function promo_reg_validate(){
      if($this->config->get('config_promo_registration')){
         $check = array('firstname','lastname','email','password','confirm');
         $return = true;
         foreach($this->error as $key=>$e){
            if(!in_array($key,$check))
               unset($this->error[$key]);
            else
               $return = false;
         }
         return $return;
      }
      return $this->error?false:true;
   }
}