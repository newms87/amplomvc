<?php
$this->db->createTable('view', <<<SQL
  `view_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `title` VARCHAR(45) NOT NULL,
  `path` VARCHAR(45) NOT NULL,
  `query` TEXT NOT NULL,
  `show` TINYINT UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`view_id`)
SQL
);


$this->db->dropTable('layout_header');
$this->db->dropTable('page_header');
$this->db->dropTable('view_count');
$this->db->dropTable('contact');
$this->db->dropTable('type_to_contact');
