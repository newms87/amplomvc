<?php

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

//Voucher
$this->db->changeColumn('voucher', 'code', '', "VARCHAR(32) NOT NULL");

//Voucher Theme
$this->db->changeColumn('voucher_theme', 'image', '', "VARCHAR(511) NOT NULL");
$this->db->addColumn('voucher_theme', 'name', "VARCHAR(45) NOT NULL  AFTER `voucher_theme_id`");
$this->db->dropTable('voucher_theme_description');
$this->db->dropTable('order_voucher');

//Order Total
$this->db->dropColumn('order_total', 'text');
$this->db->addColumn('order_total', 'method_id', "VARCHAR(64) NOT NULL AFTER `code`");

//Fraud
$this->db->query("ALTER TABLE `" . DB_PREFIX . "order_fraud` CHANGE COLUMN `country_match` `countryMatch` VARCHAR(3) NOT NULL  , CHANGE COLUMN `country_code` `countryCode` VARCHAR(2) NOT NULL  , CHANGE COLUMN `high_risk_country` `highRiskCountry` VARCHAR(3) NOT NULL  , CHANGE COLUMN `ip_user_type` `ip_userType` VARCHAR(255) NOT NULL  , CHANGE COLUMN `ip_country_confidence` `ip_countryConf` VARCHAR(3) NOT NULL  , CHANGE COLUMN `ip_region_confidence` `ip_regionConf` VARCHAR(3) NOT NULL  , CHANGE COLUMN `ip_city_confidence` `ip_cityConf` VARCHAR(3) NOT NULL  , CHANGE COLUMN `ip_postal_confidence` `ip_postalConf` VARCHAR(3) NOT NULL  , CHANGE COLUMN `ip_postal_code` `ip_postalCode` VARCHAR(10) NOT NULL  , CHANGE COLUMN `ip_accuracy_radius` `ip_accuracyRadius` INT(11) NOT NULL  , CHANGE COLUMN `ip_net_speed_cell` `ip_netSpeedCell` VARCHAR(255) NOT NULL  , CHANGE COLUMN `ip_metro_code` `ip_metroCode` INT(3) NOT NULL  , CHANGE COLUMN `ip_area_code` `ip_areaCode` INT(3) NOT NULL  , CHANGE COLUMN `ip_time_zone` `ip_timeZone` VARCHAR(255) NOT NULL  , CHANGE COLUMN `ip_region_name` `ip_regionName` VARCHAR(255) NOT NULL  , CHANGE COLUMN `ip_country_name` `ip_countryName` VARCHAR(255) NOT NULL  , CHANGE COLUMN `ip_continent_code` `ip_continentCode` VARCHAR(2) NOT NULL  , CHANGE COLUMN `ip_corporate_proxy` `ip_corporateProxy` VARCHAR(3) NOT NULL  , CHANGE COLUMN `anonymous_proxy` `anonymousProxy` VARCHAR(3) NOT NULL  , CHANGE COLUMN `proxy_score` `proxyScore` INT(3) NOT NULL  , CHANGE COLUMN `is_trans_proxy` `isTransProxy` VARCHAR(3) NOT NULL  , CHANGE COLUMN `free_mail` `freeMail` VARCHAR(3) NOT NULL  , CHANGE COLUMN `carder_email` `carderEmail` VARCHAR(3) NOT NULL  , CHANGE COLUMN `high_risk_username` `highRiskUsername` VARCHAR(3) NOT NULL  , CHANGE COLUMN `high_risk_password` `highRiskPassword` VARCHAR(3) NOT NULL  , CHANGE COLUMN `bin_match` `binMatch` VARCHAR(10) NOT NULL  , CHANGE COLUMN `bin_country` `binCountry` VARCHAR(2) NOT NULL  , CHANGE COLUMN `bin_name_match` `binNameMatch` VARCHAR(3) NOT NULL  , CHANGE COLUMN `bin_name` `binName` VARCHAR(255) NOT NULL  , CHANGE COLUMN `bin_phone_match` `binPhoneMatch` VARCHAR(3) NOT NULL  , CHANGE COLUMN `bin_phone` `binPhone` VARCHAR(32) NOT NULL  , CHANGE COLUMN `customer_phone_in_billing_location` `custPhoneInBillingLoc` VARCHAR(8) NOT NULL  , CHANGE COLUMN `ship_forward` `shipForward` VARCHAR(3) NOT NULL  , CHANGE COLUMN `city_postal_match` `cityPostalMatch` VARCHAR(3) NOT NULL  , CHANGE COLUMN `ship_city_postal_match` `shipCityPostalMatch` VARCHAR(3) NOT NULL  , CHANGE COLUMN `risk_score` `riskScore` DECIMAL(10,5) NOT NULL  , CHANGE COLUMN `queries_remaining` `queriesRemaining` INT(11) NOT NULL  , CHANGE COLUMN `maxmind_id` `maxmindID` VARCHAR(8) NOT NULL  , CHANGE COLUMN `error` `err` TEXT NOT NULL");
