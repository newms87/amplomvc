<?php
$this->db->createTable('history', <<<SQL
  `history_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `table` varchar(45) NOT NULL,
  `row_id` int(10) NOT NULL,
  `action` varchar(32) NOT NULL,
  `message` varchar(100) DEFAULT NULL,
  `data` text,
  `date` datetime NOT NULL,
  PRIMARY KEY (`history_id`)
SQL
);

$this->db->changeColumn('navigation', 'sort_order', 'sort_order', "FLOAT NOT NULL DEFAULT '0'");

$this->db->addColumn('plugin', 'version', "VARCHAR(45) NOT NULL AFTER `name`");