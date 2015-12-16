<?php
$this->db->createTable('contact', <<<SQL
  `contact_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) unsigned NOT NULL,
  `type` varchar(45) DEFAULT NULL,
  `company` varchar(128) DEFAULT NULL,
  `first_name` varchar(64) DEFAULT NULL,
  `last_name` varchar(64) DEFAULT NULL,
  `email` varchar(256) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`contact_id`),
  INDEX CUSTOMER (`customer_id`),
  INDEX TYPE_CLIENT (`type`, `customer_id`)
SQL
);
