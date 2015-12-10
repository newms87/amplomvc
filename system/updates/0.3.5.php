<?php
$this->db->createTable('file', <<<SQL
  `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(45) NOT NULL,
  `mime_type` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `path` varchar(1024) NOT NULL,
  `title` varchar(100) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`file_id`),
  KEY `type_name` (`type`,`name`)
SQL
);
