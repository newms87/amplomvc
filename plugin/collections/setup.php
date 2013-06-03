<?php
class Collections_Setup extends PluginSetup 
{
	function __construct($registry)
	{
		parent::__construct($registry);
		
		define("COLLECTION_LAYOUT_NAME", "Collections");
		define("COLLECTION_NAVIGATION_LINK_NAME", "catalog_collection");
	}
	
	public function install()
	{
		//Create collection table
		$table = DB_PREFIX . "collection";
		
		$sql =<<<SQL
CREATE TABLE IF NOT EXISTS `$table` (
  `collection_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `image` TEXT NULL ,
  `meta_description` TEXT NULL ,
  `meta_keywords` TEXT NULL ,
  `description` TEXT NULL ,
  `status` INT UNSIGNED NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`collection_id`)
)
SQL;

		$this->db->query($sql);
		
		//Create collection_product table
		$table = DB_PREFIX . "collection_product";
		
		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS `$table` (
  `collection_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`collection_id`,`product_id`)
)
SQL;
		
		$this->db->query($sql);
		
		//Create collection_category table
		$table = DB_PREFIX . "collection_category";
		
		$sql = <<<SQL
CREATE  TABLE IF NOT EXISTS `$table` (
  `collection_id` INT UNSIGNED NOT NULL ,
  `category_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`collection_id`, `category_id`)
);
SQL;
		
		$this->db->query($sql);
		
		//Create collection_store table
		$table = DB_PREFIX . "collection_store";
		
		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS `$table` (
  `collection_id` int(10) unsigned NOT NULL,
  `store_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`collection_id`,`store_id`)
)
SQL;
		
		$this->db->query($sql);
		
		//Add Collections Navigation
		$link = array(
			'display_name' => "Collections",
			'name' => COLLECTION_NAVIGATION_LINK_NAME,
			'href' => 'catalog/collection',
			'is_route' => 1,
			'sort_order' => 4,
		);
		
		$this->extend->add_navigation_link($link, 'catalog', 'admin');
		
		//Add Collections Layout
		$this->extend->add_layout(COLLECTION_LAYOUT_NAME, 'product/collection');
		
		//Enable image sorting for the 'collection' table on column 'image'
		$this->extend->enable_image_sorting('collection', 'image');
	}
	
	public function uninstall($keep_data = false)
	{
		$keep_data = true;
		
		//Remove Collections Navigation
		$this->extend->remove_navigation_link(COLLECTION_NAVIGATION_LINK_NAME);
		
		//disable image sorting for 'collection' table
		$this->extend->disable_image_sorting('collection', 'image');
		
		//Remove Collections Layout
		$this->extend->remove_layout(COLLECTION_LAYOUT_NAME);
		
		//Remove data last as good practice
		if (!$keep_data) {
			$table = DB_PREFIX . "collection";
			$this->db->query("DROP TABLE $table");
			
			$table = DB_PREFIX . "collection_product";
			$this->db->query("DROP TABLE $table");
			
			$table = DB_PREFIX . "collection_store";
			$this->db->query("DROP TABLE $table");
		}
	}
}