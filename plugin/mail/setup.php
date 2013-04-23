<?php 
class SetupMail implements SetupPlugin {

   public function install($registry, &$controller_adapters, &$db_requests){
      
   	$controller_adapters[] = array(
   		'for' 			 => 'mail/messages',
   		'admin'			 => true,
   		'plugin_file'	 => 'admin/mail',
   		'callback'		 => 'mail_settings',
   		'priority'		 => 0
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