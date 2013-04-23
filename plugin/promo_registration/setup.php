<?php 
class SetupPromoRegistration implements SetupPlugin {
      
   public function install($registry, &$controller_adapters, &$db_requests){
      
   	$controller_adapters[] = array(
   		'for' 			 => 'setting/setting',
   		'admin'			 => true,
   		'plugin_file'	 => 'admin/promo_registration',
   		'callback'		 => 'settings',
   	);
   	
      $controller_adapters[] = array(
         'for'           => 'common/header',
         'admin'         => false,
         'plugin_file'   => 'catalog/promo_registration',
         'callback'      => 'promo_header',
      );
      
      $controller_adapters[] = array(
         'for'           => 'common/footer',
         'admin'         => false,
         'plugin_file'   => 'catalog/promo_registration',
         'callback'      => 'promo_footer',
      );
      
      $controller_adapters[] = array(
         'for'           => 'account/register',
         'admin'         => false,
         'plugin_file'   => 'catalog/promo_registration',
         'callback'      => 'promo_registration',
      );
      
      $controller_adapters[] = array(
         'for'           => 'account/success',
         'admin'         => false,
         'plugin_file'   => 'catalog/promo_registration',
         'callback'      => 'promo_success',
      );
      
      $db_requests[] = array(
            'plugin_path'  => 'catalog/promo_registration',
            'table'        => 'customer',
            'query_type'   => array('insert'),
            'when'         => 'after',
            'callback'     => 'promo_addCustomer'
           );
   }
   
   public function update($version, $registry){
      switch($version){
         case '1.53':
         case '1.52':
         case '1.51':
         default:
            break;
      }
   }
   
   public function uninstall($registry){
   }
}