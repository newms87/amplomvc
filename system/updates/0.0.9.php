<?php
//Url Alias
$this->db->changeColumn('url_alias', 'keyword', 'alias', "VARCHAR(255) NOT NULL  AFTER `url_alias_id`");
$this->db->changeColumn('url_alias', 'route', 'path', "VARCHAR(255) NOT NULL");
$this->db->query("UPDATE " . DB_PREFIX . "url_alias SET store_id = 0 WHERE store_id = '-1'");

//Update .htaccess Files
$htaccess = DIR_SITE . '.htaccess';
file_put_contents($htaccess, str_replace('_route_', '_path_', file_get_contents($htaccess)));

//Product Class
$this->db->addColumn('product', 'product_class_id', "INT UNSIGNED NOT NULL AFTER `product_id`");

$this->db->createTable('product_class', <<<SQL
  `product_class_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `admin_template` text NOT NULL,
  `front_template` text NOT NULL,
  `defaults` text,
  PRIMARY KEY (`product_class_id`)
SQL
);

$link = array(
	'display_name' => "Product Classes",
	'name'         => 'catalog_products_product_classes',
	'href'         => 'catalog/product_class',
	'sort_order'   => 10,
	'parent'       => 'catalog_products',
);

$this->extend->addNavigationLink('admin', $link);

//Route to Path change
$this->db->changeColumn('view_count', 'route', 'path', "VARCHAR(255) NOT NULL");
