<?php 
class SetupCollections implements SetupPlugin {

   public function install($registry, &$controller_adapters, &$db_requests, &$language_extensions, &$file_modifications){
   	
		$table = DB_PREFIX . "collection";
		
		$sql =<<<SQL
CREATE  TABLE `$table` (
  `collection_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `image` VARCHAR(45) NULL ,
  `meta_description` TEXT NULL ,
  `meta_keywords` TEXT NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`collection_id`)
)
SQL;

		$registry->get('db')->query($sql);
		
		$table = DB_PREFIX . "collection_product";
		
		$sql = <<<SQL
CREATE TABLE `$table` (
  `collection_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`collection_id`,`product_id`)
)
SQL;
   	
   	$registry->get('db')->query($sql);
		
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
   	$table = DB_PREFIX . "collection";
		$registry->get('db')->query("DROP TABLE $table");
		
		$table = DB_PREFIX . "collection_product";
		$registry->get('db')->query("DROP TABLE $table");
   }
}