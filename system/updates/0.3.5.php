<?php
$this->db->createTable('file', <<<SQL
  `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned NOT NULL,
  `folder_id` int(10) unsigned NOT NULL,
  `category` varchar(45) NOT NULL,
  `type` varchar(45) NOT NULL,
  `mime_type` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `path` varchar(1024) NOT NULL,
  `url` varchar(1024) NOT NULL,
  `title` varchar(100) NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`file_id`),
  KEY `type_name` (`type`,`name`),
  KEY `folder_name` (`folder_id`,`name`)
SQL
);
