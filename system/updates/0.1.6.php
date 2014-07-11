<?php
$this->db->addColumn('option_value', 'info', "TEXT NOT NULL AFTER `display_value`");
$this->db->addColumn('product_option_value', 'info', "TEXT NOT NULL AFTER `display_value`");

$this->db->addColumn('view', 'group', "VARCHAR(45) NOT NULL DEFAULT 'default' AFTER `view_id`");

$this->db->createTable('dashboard', <<<SQL
  `dashboard_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `data` TEXT NOT NULL,
  PRIMARY KEY (`dashboard_id`)
SQL
);


$this->db->addColumn('view', 'listing_id', "VARCHAR(45) NOT NULL AFTER `title`");

$this->db->createTable('view_listing', <<<SQL
  `view_listing_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `slug` VARCHAR(45) NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  `query` TEXT NULL,
  `sql` TEXT NULL,
  PRIMARY KEY (`view_listing_id`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  UNIQUE KEY `slug_UNIQUE` (`slug`)
SQL
);
