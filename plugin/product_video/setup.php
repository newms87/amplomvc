<?php 
class SetupProductVideo implements SetupPlugin {

   public function install($registry, &$controller_adapters, &$db_requests){
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