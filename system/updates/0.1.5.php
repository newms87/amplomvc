<?php
$this->db->changeColumn('coupon', 'code', 'code', "varchar(32) NOT NULL");
$this->db->addColumn('coupon', 'auto_apply', "TINYINT UNSIGNED NOT NULL AFTER `code`");

$this->db->dropColumn('customer', 'cart');
$this->db->dropColumn('customer', 'wishlist');
$this->db->dropColumn('customer', 'payment_code');

$this->db->changeColumn('order_total', 'value', 'amount', "DECIMAL(15,4) NOT NULL DEFAULT '0.0000'");

$this->db->dropColumn('voucher', 'to_name');
$this->db->dropColumn('voucher', 'to_email');
$this->db->dropColumn('voucher', 'from_name');
$this->db->dropColumn('voucher', 'from_email');
$this->db->addColumn('voucher', 'data', "TEXT NOT NULL AFTER `template`");

$this->db->addColumn('product', 'product_class', "VARCHAR(45) NOT NULL AFTER `product_id`");
