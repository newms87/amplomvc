<?php
$this->db->dropTable('template');
$this->db->dropTable('voucher');
$this->db->dropTable('voucher_theme');
$this->db->dropTable('voucher_history');
$this->db->dropTable('order_voucher');

$this->db->createTable('voucher', <<<SQL
  `voucher_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(32) NOT NULL,
  `title` varchar(64) NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `message` text NOT NULL,
  `from_name` varchar(128) NOT NULL,
  `from_email` varchar(255) NOT NULL,
  `to_name` varchar(128) NOT NULL,
  `to_email` varchar(255)  NOT NULL,
  `template` varchar(128)  NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`voucher_id`)
SQL
);

$this->db->createTable('order_voucher', <<<SQL
  `order_id` int(11) unsigned NOT NULL,
  `voucher_id` int(11) NOT NULL,
  `amount` int(10) unsigned NOT NULL
SQL
);
