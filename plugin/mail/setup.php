<?php 
class SetupMail implements SetupPlugin {

   public function install($registry, &$controller_adapters, &$db_requests, &$language_extensions, &$file_modifications){
      
   	$controller_adapters[] = array(
   		'for' 			 => 'mail/messages',
   		'admin'			 => true,
   		'plugin_file'	 => 'admin/mail',
   		'callback'		 => 'mail_settings',
   		'priority'		 => 0
   	);
      
      $file_modifications = array(
         'admin/view/template/mail/messages.tpl'=>'view/mail_messages.tpl'
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