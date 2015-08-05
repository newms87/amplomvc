<?php
if ($this->db->hasTable('store')) {
	$this->db->query("ALTER TABLE `" . DB_PREFIX . "store` RENAME TO  `" . DB_PREFIX . "site`");
}

$this->db->changeColumn('site', 'store_id', 'site_id', "INT UNSIGNED NOT NULL");
$this->db->addColumn('site', 'domain', "VARCHAR(255) NOT NULL AFTER `name`");

clear_cache();
