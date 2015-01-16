<?php
$this->db->addColumn('store', 'prefix', "VARCHAR(15) NOT NULL");
$this->db->createIndex('store', 'name_UNIQUE', array('name' =>  'ASC'));

$this->db->changeColumn('navigation', 'href', 'path', "TEXT NOT NULL");

$this->db->dropColumn('block_area', 'store_id');
$this->db->dropColumn('customer', 'store_id');
$this->db->dropColumn('layout_route', 'store_id');
$this->db->dropColumn('setting', 'store_id');
$this->db->dropColumn('url_alias', 'store_id');

$this->db->dropTable('download_description');
$this->db->dropTable('extension');
$this->db->dropTable('navigation_store');
$this->db->dropTable('page_store');

$this->db->changeColumn('address', 'firstname', 'firstname', "VARCHAR(60) NULL DEFAULT ''");
$this->db->changeColumn('address', 'lastname', 'lastname', "VARCHAR(60) NULL DEFAULT ''");
$this->db->changeColumn('address', 'company', 'company', "VARCHAR(45) NULL");
$this->db->changeColumn('address', 'postcode', 'postcode', "VARCHAR(15) NOT NULL");

$this->db->createTable('log', <<<SQL
  `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uri` varchar(100) NOT NULL,
  `query` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `ip` varchar(25) DEFAULT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`log_id`)
SQL
);
