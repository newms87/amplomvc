<?php
$this->db->dropTable("affiliate");
$this->db->dropTable("affiliate_transaction");

$this->db->changeColumn("block", "name", "path", "VARCHAR(45) NOT NULL");
$this->db->changeColumn("transaction", "payment_method", "payment_code", "VARCHAR(127) NOT NULL");

$this->db->changeColumn("shipping", "shipping_method", "shipping_code", "VARCHAR(127) NOT NULL");
$this->db->addColumn('shipping', 'shipping_key', "VARCHAR(255) NOT NULL AFTER `shipping_code`");

$this->db->changeColumn('customer_subscription', 'payment_method', 'payment_code', "VARCHAR(128) NOT NULL");
$this->db->changeColumn('customer_subscription', 'payment_key', 'payment_key', "VARCHAR(255) NOT NULL");
$this->db->changeColumn('customer_subscription', 'shipping_method', 'shipping_code', "VARCHAR(128) NOT NULL");
$this->db->addColumn('customer_subscription', 'shipping_key', "VARCHAR(255) NOT NULL AFTER `shipping_code`");
