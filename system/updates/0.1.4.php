<?php
$this->db->createTable('view', <<<SQL
  `view_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `title` VARCHAR(45) NOT NULL,
  `path` VARCHAR(45) NOT NULL,
  `query` TEXT NOT NULL,
  PRIMARY KEY (`view_id`)
SQL
);
