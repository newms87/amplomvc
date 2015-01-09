<?php
$this->db->addColumn('store', 'prefix', "VARCHAR(15) NOT NULL");
$this->db->query("ALTER TABLE `ps`.`" . DB_PREFIX . "store` ADD UNIQUE INDEX `name_UNIQUE` (`name` ASC)");

$this->db->changeColumn('navigation', 'href', 'path', "TEXT NOT NULL");

$this->db->dropColumn('block_area', 'store_id');
$this->db->dropColumn('customer', 'store_id');
$this->db->dropColumn('layout_route', 'store_id');
$this->db->dropColumn('setting', 'store_id');
$this->db->dropColumn('url_alias', 'store_id');

$this->db->dropTable('download_description');
$this->db->dropTable('extension');
$this->db->dropTable('navigation_store');
$this->db->dropTable('page_store');

$this->db->changeColumn('address', 'firstname', 'firstname', "VARCHAR(60) NULL DEFAULT ''");
$this->db->changeColumn('address', 'lastname', 'lastname', "VARCHAR(60) NULL DEFAULT ''");
$this->db->changeColumn('address', 'company', 'company', "VARCHAR(45) NULL");
$this->db->changeColumn('address', 'postcode', 'postcode', "VARCHAR(15) NOT NULL");
