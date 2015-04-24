<?php
$this->db->createTable('api_user', <<<SQL
  `api_user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `username` varchar(128) NOT NULL,
  `api_key` varchar(64) NOT NULL,
  `private_key` text,
  `public_key` text,
  `user_role_id` int(10) unsigned NOT NULL,
  `permissions` text,
  `date_added` datetime NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `token` varchar(45) DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL,
  PRIMARY KEY (`api_user_id`)
SQL
);

$this->db->createTable('api_token', <<<SQL
  `token` varchar(64) NOT NULL,
  `api_user_id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `date_expires` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`token`)
SQL
);


$this->db->addColumn('user_role', 'user_id', "INT UNSIGNED NOT NULL AFTER `user_role_id`");
$this->db->addColumn('user_role', 'type', "VARCHAR(45) NOT NULL AFTER `user_id`");

