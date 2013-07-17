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
$this->db->changeColumn('order', 'invoice_no', 'invoice_prefix', "VARCHAR(26) NOT NULL");
$this->db->changeColumn('order', 'currency_code', '', "VARCHAR(5) NOT NULL");
$this->db->changeColumn('order', 'payment_method', 'payment_method_id', "VARCHAR(128) NOT NULL DEFAULT");
$this->db->changeColumn('order', 'shipping_method', 'shipping_method_id', "VARCHAR(128) NOT NULL DEFAULT");

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
$this->db->dropColumn('order_voucher', 'order_id');
$this->db->changeColumn('order_voucher', 'order_voucher_id', 'order_id', "INT(11) UNSIGNED NOT NULL");
$this->db->changeColumn('order_voucher', 'voucher_id', '', "INT(11) UNSIGNED NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (`order_id`, `voucher_id`)");

//Order Fraud
$this->db->query("ALTER TABLE `" . DB_PREFIX . "order_fraud` CHANGE COLUMN `country_match` `countryMatch` VARCHAR(3) NOT NULL  , CHANGE COLUMN `country_code` `countryCode` VARCHAR(2) NOT NULL  , CHANGE COLUMN `high_risk_country` `highRiskCountry` VARCHAR(3) NOT NULL  , CHANGE COLUMN `ip_user_type` `ip_userType` VARCHAR(255) NOT NULL  , CHANGE COLUMN `ip_country_confidence` `ip_countryConf` VARCHAR(3) NOT NULL  , CHANGE COLUMN `ip_region_confidence` `ip_regionConf` VARCHAR(3) NOT NULL  , CHANGE COLUMN `ip_city_confidence` `ip_cityConf` VARCHAR(3) NOT NULL  , CHANGE COLUMN `ip_postal_confidence` `ip_postalConf` VARCHAR(3) NOT NULL  , CHANGE COLUMN `ip_postal_code` `ip_postalCode` VARCHAR(10) NOT NULL  , CHANGE COLUMN `ip_accuracy_radius` `ip_accuracyRadius` INT(11) NOT NULL  , CHANGE COLUMN `ip_net_speed_cell` `ip_netSpeedCell` VARCHAR(255) NOT NULL  , CHANGE COLUMN `ip_metro_code` `ip_metroCode` INT(3) NOT NULL  , CHANGE COLUMN `ip_area_code` `ip_areaCode` INT(3) NOT NULL  , CHANGE COLUMN `ip_time_zone` `ip_timeZone` VARCHAR(255) NOT NULL  , CHANGE COLUMN `ip_region_name` `ip_regionName` VARCHAR(255) NOT NULL  , CHANGE COLUMN `ip_country_name` `ip_countryName` VARCHAR(255) NOT NULL  , CHANGE COLUMN `ip_continent_code` `ip_continentCode` VARCHAR(2) NOT NULL  , CHANGE COLUMN `ip_corporate_proxy` `ip_corporateProxy` VARCHAR(3) NOT NULL  , CHANGE COLUMN `anonymous_proxy` `anonymousProxy` VARCHAR(3) NOT NULL  , CHANGE COLUMN `proxy_score` `proxyScore` INT(3) NOT NULL  , CHANGE COLUMN `is_trans_proxy` `isTransProxy` VARCHAR(3) NOT NULL  , CHANGE COLUMN `free_mail` `freeMail` VARCHAR(3) NOT NULL  , CHANGE COLUMN `carder_email` `carderEmail` VARCHAR(3) NOT NULL  , CHANGE COLUMN `high_risk_username` `highRiskUsername` VARCHAR(3) NOT NULL  , CHANGE COLUMN `high_risk_password` `highRiskPassword` VARCHAR(3) NOT NULL  , CHANGE COLUMN `bin_match` `binMatch` VARCHAR(10) NOT NULL  , CHANGE COLUMN `bin_country` `binCountry` VARCHAR(2) NOT NULL  , CHANGE COLUMN `bin_name_match` `binNameMatch` VARCHAR(3) NOT NULL  , CHANGE COLUMN `bin_name` `binName` VARCHAR(255) NOT NULL  , CHANGE COLUMN `bin_phone_match` `binPhoneMatch` VARCHAR(3) NOT NULL  , CHANGE COLUMN `bin_phone` `binPhone` VARCHAR(32) NOT NULL  , CHANGE COLUMN `customer_phone_in_billing_location` `custPhoneInBillingLoc` VARCHAR(8) NOT NULL  , CHANGE COLUMN `ship_forward` `shipForward` VARCHAR(3) NOT NULL  , CHANGE COLUMN `city_postal_match` `cityPostalMatch` VARCHAR(3) NOT NULL  , CHANGE COLUMN `ship_city_postal_match` `shipCityPostalMatch` VARCHAR(3) NOT NULL  , CHANGE COLUMN `risk_score` `riskScore` DECIMAL(10,5) NOT NULL  , CHANGE COLUMN `queries_remaining` `queriesRemaining` INT(11) NOT NULL  , CHANGE COLUMN `maxmind_id` `maxmindID` VARCHAR(8) NOT NULL  , CHANGE COLUMN `error` `err` TEXT NOT NULL");

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

$this->db->createTable('tag_translation', <<<SQL
	  `tag_id` INT UNSIGNED NOT NULL ,
	  `language_id` INT UNSIGNED NOT NULL ,
	  `text` VARCHAR(45) NOT NULL ,
	  PRIMARY KEY (`tag_id`, `language_id`)
SQL
);

//Voucher
$this->db->changeColumn('voucher', 'code', '', "VARCHAR(32) NOT NULL");
$this->db->dropColumn('voucher', 'order_id');

//Voucher Theme
$this->db->changeColumn('voucher_theme', 'image', '', "VARCHAR(511) NOT NULL");
$this->db->addColumn('voucher_theme', 'name', "VARCHAR(45) NOT NULL  AFTER `voucher_theme_id`");
$this->db->dropTable('voucher_theme_description');
