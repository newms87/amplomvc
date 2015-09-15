<?php

$this->db->createTable('category', <<<SQL
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(45) NOT NULL,
  `name` varchar(64) NOT NULL,
  `title` varchar(127) NOT NULL,
  `parent_id` int(10) unsigned NOT NULL,
  `status` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `TYPE_NAME` (`type`,`name`),
  KEY `TYPE_STATUS_NAME` (`type`,`status`,`name`)
SQL
);
