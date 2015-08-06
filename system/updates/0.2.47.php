<?php
if ($this->db->hasTable('store')) {
	$this->db->changeColumn('store', 'store_id', 'site_id', "INT(11) UNSIGNED NOT NULL AUTO_INCREMENT");
	$this->db->addColumn('store', 'domain', "VARCHAR(255) NOT NULL AFTER `name`");
	$this->db->query("ALTER TABLE `" . DB_PREFIX . "store` RENAME TO  `" . DB_PREFIX . "site`");
}

clear_cache_all();
