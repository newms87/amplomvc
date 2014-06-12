<?php
$this->db->changeColumn('coupon', 'code', 'code', "varchar(32) NOT NULL");
$this->db->addColumn('coupon', 'auto_apply', "TINYINT UNSIGNED NOT NULL AFTER `code`");

$this->db->dropColumn('customer', 'cart');
$this->db->dropColumn('customer', 'wishlist');
$this->db->dropColumn('customer', 'payment_code');

$this->db->changeColumn('order_total', 'value', 'amount', "DECIMAL(15,4) NOT NULL DEFAULT '0.0000'");
