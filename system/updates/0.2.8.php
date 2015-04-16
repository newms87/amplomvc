<?php
$this->db->createTable('api_user', <<<SQL
  `api_user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `username` varchar(128) NOT NULL,
  `api_key` varchar(64) NOT NULL,
  `api_secret` varchar(256) NOT NULL,
  `user_role_id` int(10) unsigned NOT NULL,
  `permissions` text,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`api_id`)
SQL
);
