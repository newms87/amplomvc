<?php
$this->db->changeColumn('coupon', 'code', 'code', "varchar(32) NOT NULL");
$this->db->addColumn('coupon', 'auto_apply', "TINYINT UNSIGNED NOT NULL AFTER `code`");

$this->db->dropColumn('customer', 'cart');
$this->db->dropColumn('customer', 'wishlist');
$this->db->dropColumn('customer', 'payment_code');

$this->db->changeColumn('order_total', 'value', 'amount', "DECIMAL(15,4) NOT NULL DEFAULT '0.0000'");
$this->db->addColumn('order_product', 'product_class', "VARCHAR(45) NOT NULL AFTER `product_id`");

$this->db->dropColumn('voucher', 'to_name');
$this->db->dropColumn('voucher', 'to_email');
$this->db->dropColumn('voucher', 'from_name');
$this->db->dropColumn('voucher', 'from_email');
$this->db->dropColumn('voucher', 'message');
$this->db->dropColumn('voucher', 'date_added');
$this->db->addColumn('voucher', 'order_id', "INT UNSIGNED NULL AFTER `voucher_id`");
$this->db->addColumn('voucher', 'data', "TEXT NOT NULL AFTER `template`");

$this->db->addColumn('product', 'product_class', "VARCHAR(45) NOT NULL AFTER `product_id`");

$this->db->dropTable('order_voucher');

$this->db->createTable('voucher_history', <<<SQL
  `voucher_history_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `voucher_id` INT UNSIGNED NOT NULL,
  `order_id` INT UNSIGNED NOT NULL,
  `amount` INT UNSIGNED NOT NULL,
  `message` VARCHAR(255) NOT NULL,,
  `date` DATETIME NOT NULL,
  PRIMARY KEY (`voucher_history_id`)
SQL
);

$this->db->createTable('order_product_meta', <<<SQL
  `order_product_meta_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT UNSIGNED NOT NULL,
  `order_product_id` INT UNSIGNED NOT NULL,
  `key` VARCHAR(45) NOT NULL,
  `value` TEXT NOT NULL,
  `serialized` TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (`order_meta_id`)
SQL
);
