<?php
if ($this->db->hasTable('store')) {
	$this->db->query("ALTER TABLE `" . DB_PREFIX . "store` RENAME TO  `" . DB_PREFIX . "site`");
}

$this->db->changeColumn('site', 'store_id', 'site_id', "INT(11) UNSIGNED NOT NULL AUTO_INCREMENT");
//TO update bad update (from previous version of this file) where no setting auto increment.
$this->db->changeColumn('site', 'site_id', 'site_id', "INT(11) UNSIGNED NOT NULL AUTO_INCREMENT");
$this->db->addColumn('site', 'domain', "VARCHAR(255) NOT NULL AFTER `name`");

clear_cache();
