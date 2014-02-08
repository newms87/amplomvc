<?php
$this->db->dropTable("affiliate");
$this->db->dropTable("affiliate_transaction");

$this->db->changeColumn("block", "name", "path", "VARCHAR(45) NOT NULL");
$this->db->changeColumn("transaction", "payment_method", "payment_code", "VARCHAR(127) NOT NULL");
$this->db->changeColumn("shipping", "shipping_method", "shipping_code", "VARCHAR(127) NOT NULL");
