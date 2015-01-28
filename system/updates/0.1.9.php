<?php
$this->db->dropTable('page_store');

$this->db->addColumn('page', 'content', "TEXT NOT NULL DEFAULT '' AFTER `template`");
$this->db->addColumn('page', 'style', "TEXT NOT NULL DEFAULT '' AFTER `content`");
$this->db->addColumn('page', 'date_updated', "DATETIME NOT NULL AFTER `cache`");
$this->db->addColumn('page', 'layout_id', "INT UNSIGNED NOT NULL AFTER `title`");
$this->db->addColumn('page', 'updated_user_id', "INT UNSIGNED NOT NULL DEFAULT '0' AFTER `date_updated`");

$this->db->createTable('page_history', <<<SQL
  `page_history_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `page_id` INT UNSIGNED NOT NULL ,
  `name` VARCHAR(128) NOT NULL ,
  `title` VARCHAR(45) NOT NULL ,
  `template` VARCHAR(128) NOT NULL ,
  `content` TEXT NOT NULL ,
  `style` TEXT NOT NULL ,
  `meta_keywords` TEXT NULL ,
  `meta_description` TEXT NULL ,
  `display_title` TINYINT UNSIGNED NOT NULL ,
  `date` DATETIME NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`page_history_id`)
SQL
);


//Length / Weight
$this->db->dropTable('weight_class');
$this->db->dropTable('weight_class_description');
$this->db->dropTable('length_class');
$this->db->dropTable('length_class_description');

$this->db->changeColumn('product', 'weight_class_id', 'weight_unit', "VARCHAR(5) NOT NULL DEFAULT ''");
$this->db->changeColumn('product', 'length_class_id', 'length_unit', "VARCHAR(5) NOT NULL DEFAULT ''");

