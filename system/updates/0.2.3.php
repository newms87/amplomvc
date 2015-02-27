<?php
$this->db->changeColumn('address', 'address_1', 'address', "varchar(128) NOT NULL");

if ($this->db->hasColumn('address', 'first_name')) {
	$this->db->queryRows("UPDATE {$this->db->t['address']} SET first_name = CONCAT(first_name, ' ', last_name)");
	$this->db->changeColumn('address', 'first_name', 'name', "VARCHAR(128) NULL DEFAULT ''");
	$this->db->dropColumn('address', 'last_name');
}

$this->db->query("UPDATE {$this->db->t['country']} SET address_format = ''");

$this->db->addColumn('customer', 'username', "VARCHAR(45) NOT NULL DEFAULT '' AFTER `customer_id`");

$this->db->query("UPDATE {$this->db->t['customer']} SET username = email WHERE username = ''");

$this->db->changeColumn('history', 'row_id', 'record_id', "INT UNSIGNED NOT NULL");

$this->db->addColumn('plugin', 'date_installed', "DATETIME NOT NULL AFTER `status`");
$this->db->addColumn('plugin', 'date_updated', "DATETIME NOT NULL AFTER `date_installed`");
$this->db->dropTable('plugin_merged_files');
$this->db->dropTable('plugin_registry');
