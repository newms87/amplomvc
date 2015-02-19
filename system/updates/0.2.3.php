<?php
$this->db->changeColumn('address', 'address_1', 'address', "varchar(128) NOT NULL");

$this->db->queryRows("UPDATE " . DB_PREFIX . "address SET first_name = CONCAT(first_name, ' ', last_name)");
$this->db->changeColumn('address', 'first_name', 'name', "VARCHAR(128) NULL DEFAULT ''");
$this->db->dropColumn('address', 'last_name');
