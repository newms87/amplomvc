<?php
class ProductClass_Setup extends PluginSetup
{
	public function install()
	{
		//Database Changes
		$this->db->addColumn('product', 'product_class_id', "INT UNSIGNED NOT NULL AFTER `product_id`");
		
		$this->db->createTable('product_class', <<<SQL
		  `product_class_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		  `name` VARCHAR(45) NOT NULL,
		  `admin_template` TEXT NOT NULL,
		  `front_template` TEXT NOT NULL,
		  PRIMARY KEY (`product_class_id`)
SQL
		);
		
		//Add Navigation Links
		$link = array(
			'display_name' => "Product Classes",
			'name' => 'catalog_products_product_classes',
			'href' => 'catalog/product_class',
			'sort_order' => 1,
		);
		
		$this->extend->add_navigation_link($link, 'catalog_products', 'admin');
	}
	
	public function uninstall($keep_data = true)
	{
		//Remove Navigation Links
		$this->extend->remove_navigation_link('catalog_product_product_classes');
		
		//Remove data last as good practice
		if (!$keep_data) {
			$this->db->dropTable('product_class');
			$this->db->dropColumn('product', 'product_class_id');
		}
	}
}