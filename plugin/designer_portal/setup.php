<?php 
class SetupDesignerPortal implements SetupPlugin {
   
   public function install($registry, &$controller_adapters, &$db_requests, &$language_extensions, &$file_modifications){
         
      $controller_adapters[] = array(
         'for'           => 'common/header',
         'admin'         => false,
         'plugin_file'   => 'designer_portal',
         'callback'      => 'are_you_designer_link',
         'priority'      => 0
      );
      
      $controller_adapters[] = array(
         'for'           => 'common/header',
         'admin'         => true,
         'plugin_file'   => 'designer_portal',
         'callback'      => 'are_you_designer_link',
         'priority'      => 0
      );
      
      $file_modifications = array(
         'admin/controller/catalog/product.php'=>'file_mods/admin_controller_catalog_product.php'
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