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
