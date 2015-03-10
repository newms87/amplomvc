<?php
$this->db->createTable('invoice', <<<SQL
  `invoice_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `number` VARCHAR(45) NOT NULL,
  `customer_id` INT UNSIGNED NOT NULL,
  `payment_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `date_created` DATETIME NOT NULL,
  `date_paid` DATETIME NULL,
  `date_updated` DATETIME NULL,
  `date_due` DATETIME NULL,
  `status` INT UNSIGNED NOT NULL,
  `data` TEXT NULL,
  PRIMARY KEY (`invoice_id`)
SQL
);

$this->db->createTable('meta', <<<SQL
  `meta_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(60) NOT NULL,
  `record_id` INT UNSIGNED NOT NULL,
  `key` VARCHAR(45) NOT NULL,
  `value` TEXT NOT NULL,
  `serialized` TINYINT UNSIGNED NOT NULL,
  `date` DATETIME NOT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `RECORD` (`record_id`),
  KEY `TYPE` (`type`)
SQL
);
