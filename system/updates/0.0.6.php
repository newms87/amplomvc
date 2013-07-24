<?php
//Settings
$this->db->addColumn('setting', 'translate', "TINYINT(1) UNSIGNED NOT NULL AFTER `serialized`");

//Language
$this->db->addColumn('language', 'datetime_format_long', "VARCHAR(45) NOT NULL DEFAULT 'M d, Y H:i A'  AFTER `datetime_format`");

//Order
$this->db->dropColumn('order', 'fpo');
$this->db->dropColumn('order', 'currency_id');
$this->db->dropColumn('order', 'payment_zone');
$this->db->dropColumn('order', 'payment_country');
$this->db->dropColumn('order', 'shipping_zone');
$this->db->dropColumn('order', 'shipping_country');
$this->db->dropColumn('order', 'shipping_code');
$this->db->dropColumn('order', 'store_url');
$this->db->dropColumn('order', 'store_name');
$this->db->dropColumn('order', 'invoice_prefix');
$this->db->changeColumn('order', 'invoice_no', 'invoice_id', "VARCHAR(45) NOT NULL");
$this->db->changeColumn('order', 'currency_code', '', "VARCHAR(5) NOT NULL");
$this->db->changeColumn('order', 'payment_method', 'payment_method_id', "VARCHAR(128) NOT NULL");
$this->db->changeColumn('order', 'shipping_method', 'shipping_method_id', "VARCHAR(128) NOT NULL");

//Order Total
$this->db->dropColumn('order_total', 'text');
$this->db->addColumn('order_total', 'method_id', "VARCHAR(64) NOT NULL AFTER `code`");
$this->db->dropTable('order_status');
$this->db->addColumn('order', 'confirmed', "TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `order_status_id`");

//Order Product
$this->db->dropColumn('order_product', 'name');
$this->db->dropColumn('order_product', 'model');
$this->db->changeColumn('order_product', 'is_final', 'return_policy_id', "INT(10) UNSIGNED NOT NULL DEFAULT '0'");
$this->db->addColumn('order_product', 'shipping_policy_id', "INT(10) UNSIGNED NOT NULL DEFAULT '0'");

//Order Voucher
$this->db->dropColumn('order_voucher', 'date_added');
$this->db->dropColumn('order_voucher', 'amount');
$this->db->dropColumn('order_voucher', 'message');
$this->db->dropColumn('order_voucher', 'to_email');
$this->db->dropColumn('order_voucher', 'to_name');
$this->db->dropColumn('order_voucher', 'from_email');
$this->db->dropColumn('order_voucher', 'from_name');
$this->db->dropColumn('order_voucher', 'voucher_theme_id');
$this->db->dropColumn('order_voucher', 'code');
$this->db->dropColumn('order_voucher', 'description');
$this->db->dropColumn('order_voucher', 'order_voucher_id');
$this->db->changeColumn('order_voucher', 'order_id', '', "INT(11) UNSIGNED NOT NULL");
$this->db->changeColumn('order_voucher', 'voucher_id', '', "INT(11) UNSIGNED NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (`order_id`, `voucher_id`)");

//Order Fraud
$this->db->dropTable('order_fraud');
$this->db->createTable('order_fraud', <<<SQL
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `countryMatch` varchar(3) NOT NULL,
  `countryCode` varchar(2) NOT NULL,
  `highRiskCountry` varchar(3) NOT NULL,
  `distance` int(11) NOT NULL,
  `ip_region` varchar(255) NOT NULL,
  `ip_city` varchar(255) NOT NULL,
  `ip_latitude` decimal(10,6) NOT NULL,
  `ip_longitude` decimal(10,6) NOT NULL,
  `ip_isp` varchar(255) NOT NULL,
  `ip_org` varchar(255) NOT NULL,
  `ip_asnum` int(11) NOT NULL,
  `ip_userType` varchar(255) NOT NULL,
  `ip_countryConf` varchar(3) NOT NULL,
  `ip_regionConf` varchar(3) NOT NULL,
  `ip_cityConf` varchar(3) NOT NULL,
  `ip_postalConf` varchar(3) NOT NULL,
  `ip_postalCode` varchar(10) NOT NULL,
  `ip_accuracyRadius` int(11) NOT NULL,
  `ip_netSpeedCell` varchar(255) NOT NULL,
  `ip_metroCode` int(3) NOT NULL,
  `ip_areaCode` int(3) NOT NULL,
  `ip_timeZone` varchar(255) NOT NULL,
  `ip_regionName` varchar(255) NOT NULL,
  `ip_domain` varchar(255) NOT NULL,
  `ip_countryName` varchar(255) NOT NULL,
  `ip_continentCode` varchar(2) NOT NULL,
  `ip_corporateProxy` varchar(3) NOT NULL,
  `anonymousProxy` varchar(3) NOT NULL,
  `proxyScore` int(3) NOT NULL,
  `isTransProxy` varchar(3) NOT NULL,
  `freeMail` varchar(3) NOT NULL,
  `carderEmail` varchar(3) NOT NULL,
  `highRiskUsername` varchar(3) NOT NULL,
  `highRiskPassword` varchar(3) NOT NULL,
  `binMatch` varchar(10) NOT NULL,
  `binCountry` varchar(2) NOT NULL,
  `binNameMatch` varchar(3) NOT NULL,
  `binName` varchar(255) NOT NULL,
  `binPhoneMatch` varchar(3) NOT NULL,
  `binPhone` varchar(32) NOT NULL,
  `custPhoneInBillingLoc` varchar(8) NOT NULL,
  `shipForward` varchar(3) NOT NULL,
  `cityPostalMatch` varchar(3) NOT NULL,
  `shipCityPostalMatch` varchar(3) NOT NULL,
  `score` decimal(10,5) NOT NULL,
  `explanation` text NOT NULL,
  `riskScore` decimal(10,5) NOT NULL,
  `queriesRemaining` int(11) NOT NULL,
  `maxmindID` varchar(8) NOT NULL,
  `err` text NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`order_id`)
SQL
);


//Product
$this->db->changeColumn('product', 'is_final', 'return_policy_id', "INT(10) UNSIGNED NOT NULL DEFAULT '0'");
$this->db->changeColumn('product', 'shipping_return', 'shipping_policy_id', "INT(10) UNSIGNED NOT NULL DEFAULT '0'");

//Product Tag
$this->db->query("TRUNCATE " . DB_PREFIX . "product_tag");
$this->db->dropColumn('product_tag', 'tag');
$this->db->dropColumn('product_tag', 'language_id');
$this->db->changeColumn('product_tag', 'product_id', '', "INT(11) NOT NULL");
$this->db->changeColumn('product_tag', 'product_tag_id', 'tag_id', "INT(11) NOT NULL, DROP PRIMARY KEY , ADD PRIMARY KEY (`product_id`, `tag_id`)");

//Tag
$this->db->createTable('tag', <<<SQL
	  `tag_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	  `text` VARCHAR(45) NOT NULL ,
	  PRIMARY KEY (`tag_id`) ,
	  UNIQUE INDEX `text_UNIQUE` (`text` ASC)
SQL
);

//Voucher
$this->db->changeColumn('voucher', 'code', '', "VARCHAR(32) NOT NULL");
$this->db->dropColumn('voucher', 'order_id');

//Voucher Theme
$this->db->changeColumn('voucher_theme', 'image', '', "VARCHAR(511) NOT NULL");
$this->db->addColumn('voucher_theme', 'name', "VARCHAR(45) NOT NULL  AFTER `voucher_theme_id`");
$this->db->dropTable('voucher_theme_description');
