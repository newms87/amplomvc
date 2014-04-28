<?php
$this->db->dropColumn('user', 'ip');

//Blocks
$this->db->createTable('block_instance', <<<SQL
	  `block_instance_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	  `path` VARCHAR(128) NOT NULL,
	  `name` VARCHAR(45) NOT NULL,
	  `title` VARCHAR(255) NOT NULL,
	  `show_title` TINYINT UNSIGNED NOT NULL,
	  `settings` TEXT NULL,
	  `status` TINYINT UNSIGNED NOT NULL,
	  PRIMARY KEY (`block_instance_id`)
SQL
);

$this->db->createTable('block_area', <<<SQL
  `block_area_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `path` VARCHAR(128) NOT NULL,
  `instance_name` VARCHAR(45) NOT NULL,
  `area` VARCHAR(45) NOT NULL,
  `store_id` INT UNSIGNED NOT NULL,
  `layout_id` INT UNSIGNED NOT NULL,
  `sort_order` INT(10) NOT NULL,
  PRIMARY KEY (`block_area_id`)
SQL
);


$this->db->changeColumn('block', 'path', 'path', "VARCHAR(128) NOT NULL");
$this->db->dropColumn('block', 'profile_settings');
$this->db->dropColumn('block', 'profiles');


//Page
$this->db->addColumn('page', 'template', "VARCHAR(128) NOT NULL AFTER `layout_id`");
$this->db->addColumn('page', 'name', "VARCHAR(128) NOT NULL AFTER `page_id`");
