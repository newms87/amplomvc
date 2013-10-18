<?php
//Extensions
$this->db->addColumn('extension', 'title', "VARCHAR(255) NOT NULL  AFTER `code`");
$this->db->addColumn('extension', 'settings', "TEXT NULL  AFTER `title`");
$this->db->changeColumn('extension', 'sort_order', 'sort_order', "INT(10) NOT NULL DEFAULT '0'");

//Options
$this->db->addColumn('option_value', 'display_value', "VARCHAR(256) NOT NULL  AFTER `value`");
$this->db->addColumn('product_option_value', 'display_value', "VARCHAR(256) NOT NULL  AFTER `value`");

//Navigation
$this->db->dropColumn('navigation', 'is_route');
$this->db->addColumn('navigation', 'condition', "VARCHAR(45) NOT NULL  AFTER `query`");
$this->db->changeColumn('navigation', 'parent_id', 'parent_id', "INT(10) UNSIGNED NOT NULL DEFAULT '0'  AFTER `navigation_group_id`");


//Product Table Fix
$this->db->query("ALTER TABLE `realmeal`.`ac_product` DROP PRIMARY KEY , ADD PRIMARY KEY (`product_id`)");
$this->db->changeColumn('product', 'product_id', 'product_id', "INT(11) UNSIGNED NOT NULL AUTO_INCREMENT");
