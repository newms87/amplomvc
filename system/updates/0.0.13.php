<?php
$this->db->createTable('user_meta', <<<SQL
	`user_meta_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(10) unsigned NOT NULL,
	`key` varchar(64) NOT NULL,
	`value` text NOT NULL,
	`serialized` tinyint(3) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`user_meta_id`)
SQL
);

//Customer Meta
if ($this->db->hasTable('customer_setting')) {
	$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer_setting` CHANGE COLUMN `customer_setting_id` `customer_meta_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, RENAME TO  `" . DB_PREFIX . "customer_meta`");
}
