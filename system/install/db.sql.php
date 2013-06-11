<?php

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}information_description`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}information_description` (
  `information_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `description` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`information_id`,`language_id`)
)
SQL;


$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}layout_route`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}layout_route` (
  `layout_route_id` int(11) NOT NULL AUTO_INCREMENT,
  `layout_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `route` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`layout_route_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}customer_ip_blacklist`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}customer_ip_blacklist` (
  `customer_ip_blacklist_id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`customer_ip_blacklist_id`),
  KEY `ip` (`ip`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}banner`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}banner` (
  `banner_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_bin NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`banner_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}page_header`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}page_header` (
  `page_header_id` int(10) unsigned NOT NULL,
  `page_header` text,
  `language_id` int(10) unsigned NOT NULL DEFAULT '1',
  `priority` int(10) unsigned NOT NULL DEFAULT '0',
  `status` int(10) unsigned NOT NULL DEFAULT '1'
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}order_download`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}order_download` (
  `order_download_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `order_product_id` int(11) NOT NULL,
  `name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `filename` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `mask` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `remaining` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`order_download_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}address`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}address` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `firstname` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `lastname` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `company` varchar(32) COLLATE utf8_bin NOT NULL,
  `address_1` varchar(128) COLLATE utf8_bin NOT NULL,
  `address_2` varchar(128) COLLATE utf8_bin NOT NULL,
  `city` varchar(128) COLLATE utf8_bin NOT NULL,
  `postcode` varchar(10) COLLATE utf8_bin NOT NULL,
  `country_id` int(11) NOT NULL DEFAULT '0',
  `zone_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`address_id`),
  KEY `customer_id` (`customer_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}geo_zone`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}geo_zone` (
  `geo_zone_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `exclude` int(10) unsigned NOT NULL DEFAULT '0',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`geo_zone_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}translation_text`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}translation_text` (
  `translation_id` int(10) unsigned NOT NULL,
  `object_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`translation_id`,`object_id`,`language_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}product_related`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}product_related` (
  `product_id` int(11) NOT NULL,
  `related_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`,`related_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}janrain`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}janrain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `provider` varchar(255) NOT NULL,
  `identifier` varchar(255) NOT NULL,
  `register_date` datetime NOT NULL,
  `lastvisit_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}user_group`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}user_group` (
  `user_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_bin NOT NULL,
  `permission` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`user_group_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}option_value`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}option_value` (
  `option_value_id` int(11) NOT NULL AUTO_INCREMENT,
  `option_id` int(11) NOT NULL,
  `image` varchar(255) COLLATE utf8_bin NOT NULL,
  `sort_order` int(3) NOT NULL,
  PRIMARY KEY (`option_value_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}product_views`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}product_views` (
  `product_view_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `session_id` varchar(45) DEFAULT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`product_view_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}manufacturer_description`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}manufacturer_description` (
  `manufacturer_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `shipping_return` text,
  `teaser` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`manufacturer_id`,`language_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}length_class`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}length_class` (
  `length_class_id` int(11) NOT NULL AUTO_INCREMENT,
  `value` decimal(15,8) NOT NULL,
  PRIMARY KEY (`length_class_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}weight_class_description`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}weight_class_description` (
  `weight_class_id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) NOT NULL,
  `title` varchar(32) COLLATE utf8_bin NOT NULL,
  `unit` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`weight_class_id`,`language_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}product_reward`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}product_reward` (
  `product_reward_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL DEFAULT '0',
  `customer_group_id` int(11) NOT NULL DEFAULT '0',
  `points` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_reward_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}collection_category`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}collection_category` (
  `collection_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`collection_id`,`category_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}order`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_no` int(11) NOT NULL DEFAULT '0',
  `invoice_prefix` varchar(26) COLLATE utf8_bin NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '0',
  `store_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `store_url` varchar(255) COLLATE utf8_bin NOT NULL,
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `customer_group_id` int(11) NOT NULL DEFAULT '0',
  `firstname` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `lastname` varchar(32) COLLATE utf8_bin NOT NULL,
  `email` varchar(96) COLLATE utf8_bin NOT NULL,
  `telephone` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `fax` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `shipping_firstname` varchar(32) COLLATE utf8_bin NOT NULL,
  `shipping_lastname` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `shipping_company` varchar(32) COLLATE utf8_bin NOT NULL,
  `shipping_address_1` varchar(128) COLLATE utf8_bin NOT NULL,
  `shipping_address_2` varchar(128) COLLATE utf8_bin NOT NULL,
  `shipping_city` varchar(128) COLLATE utf8_bin NOT NULL,
  `shipping_postcode` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  `shipping_country` varchar(128) COLLATE utf8_bin NOT NULL,
  `shipping_country_id` int(11) NOT NULL,
  `shipping_zone` varchar(128) COLLATE utf8_bin NOT NULL,
  `shipping_zone_id` int(11) NOT NULL,
  `shipping_address_format` text COLLATE utf8_bin NOT NULL,
  `shipping_method` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `shipping_code` varchar(128) COLLATE utf8_bin NOT NULL,
  `payment_firstname` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `payment_lastname` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `payment_company` varchar(32) COLLATE utf8_bin NOT NULL,
  `payment_address_1` varchar(128) COLLATE utf8_bin NOT NULL,
  `payment_address_2` varchar(128) COLLATE utf8_bin NOT NULL,
  `payment_city` varchar(128) COLLATE utf8_bin NOT NULL,
  `payment_postcode` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  `payment_country` varchar(128) COLLATE utf8_bin NOT NULL,
  `payment_country_id` int(11) NOT NULL,
  `payment_zone` varchar(128) COLLATE utf8_bin NOT NULL,
  `payment_zone_id` int(11) NOT NULL,
  `payment_address_format` text COLLATE utf8_bin NOT NULL,
  `payment_method` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `comment` text COLLATE utf8_bin NOT NULL,
  `total` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `order_status_id` int(11) NOT NULL DEFAULT '0',
  `affiliate_id` int(11) NOT NULL,
  `commission` decimal(15,4) NOT NULL,
  `language_id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `currency_code` varchar(3) COLLATE utf8_bin NOT NULL,
  `currency_value` decimal(15,8) NOT NULL,
  `ip` varchar(15) COLLATE utf8_bin NOT NULL,
  `forwarded_ip` varchar(15) COLLATE utf8_bin NOT NULL,
  `user_agent` varchar(255) COLLATE utf8_bin NOT NULL,
  `accept_language` varchar(255) COLLATE utf8_bin NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `fpo` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`order_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}product_option_value_restriction`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}product_option_value_restriction` (
  `product_id` int(10) unsigned NOT NULL,
  `option_value_id` int(10) unsigned NOT NULL,
  `restrict_option_value_id` int(10) unsigned NOT NULL,
  `quantity` int(10) NOT NULL,
  PRIMARY KEY (`product_id`,`option_value_id`,`restrict_option_value_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}plugin_merged_files`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}plugin_merged_files` (
  `oc_plugin_merged_files_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  PRIMARY KEY (`oc_plugin_merged_files_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}product_to_layout`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}product_to_layout` (
  `product_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `layout_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`,`store_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}contact`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}contact` (
  `contact_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(1000) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `street_1` varchar(128) DEFAULT NULL,
  `street_2` varchar(128) DEFAULT NULL,
  `city` varchar(128) DEFAULT NULL,
  `postcode` varchar(10) DEFAULT NULL,
  `country_id` int(10) unsigned DEFAULT NULL,
  `zone_id` int(10) unsigned DEFAULT NULL,
  `contact_type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`contact_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}customer_reward`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}customer_reward` (
  `customer_reward_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `order_id` int(11) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_bin NOT NULL,
  `points` int(8) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`customer_reward_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}language`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}language` (
  `language_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `code` varchar(5) COLLATE utf8_bin NOT NULL,
  `locale` varchar(255) COLLATE utf8_bin NOT NULL,
  `image` varchar(64) COLLATE utf8_bin NOT NULL,
  `directory` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `filename` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `sort_order` int(3) NOT NULL DEFAULT '0',
  `status` tinyint(2) NOT NULL,
  `datetime_format` varchar(45) COLLATE utf8_bin NOT NULL DEFAULT 'Y-m-d H:i:s',
  `date_format_short` varchar(45) COLLATE utf8_bin NOT NULL DEFAULT '',
  `date_format_long` varchar(45) COLLATE utf8_bin NOT NULL DEFAULT '',
  `time_format` varchar(45) COLLATE utf8_bin NOT NULL DEFAULT '',
  `direction` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT 'ltr',
  `decimal_point` varchar(2) COLLATE utf8_bin NOT NULL DEFAULT '.',
  `thousand_point` varchar(2) COLLATE utf8_bin NOT NULL DEFAULT ',',
  PRIMARY KEY (`language_id`),
  KEY `name` (`name`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}customer_setting`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}customer_setting` (
  `customer_setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) unsigned NOT NULL,
  `key` varchar(45) NOT NULL,
  `value` text,
  `serialized` int(10) unsigned NOT NULL,
  PRIMARY KEY (`customer_setting_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}voucher_theme_description`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}voucher_theme_description` (
  `voucher_theme_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(32) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`voucher_theme_id`,`language_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}download_description`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}download_description` (
  `download_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`download_id`,`language_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}product_attribute`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}product_attribute` (
  `product_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `text` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`product_id`,`attribute_id`,`language_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}option`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}option` (
  `option_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(32) COLLATE utf8_bin NOT NULL,
  `group_type` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT 'single',
  `sort_order` int(3) NOT NULL,
  PRIMARY KEY (`option_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}option_value_description`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}option_value_description` (
  `option_value_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `name` varchar(128) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`option_value_id`,`language_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}voucher_history`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}voucher_history` (
  `voucher_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `voucher_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`voucher_history_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}download`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}download` (
  `download_id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `mask` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `remaining` int(11) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`download_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}plugin`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}plugin` (
  `plugin_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `status` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`plugin_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}currency`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}currency` (
  `currency_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `code` varchar(3) COLLATE utf8_bin NOT NULL DEFAULT '',
  `symbol_left` varchar(12) COLLATE utf8_bin NOT NULL,
  `symbol_right` varchar(12) COLLATE utf8_bin NOT NULL,
  `decimal_place` char(1) COLLATE utf8_bin NOT NULL,
  `value` float(15,8) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`currency_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}return_status`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}return_status` (
  `return_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`return_status_id`,`language_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}tax_class`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}tax_class` (
  `tax_class_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`tax_class_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}tax_rule`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}tax_rule` (
  `tax_rule_id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_class_id` int(11) NOT NULL,
  `tax_rate_id` int(11) NOT NULL,
  `based` varchar(10) COLLATE utf8_bin NOT NULL,
  `priority` int(5) NOT NULL DEFAULT '1',
  PRIMARY KEY (`tax_rule_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}order_voucher`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}order_voucher` (
  `order_voucher_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `voucher_id` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8_bin NOT NULL,
  `code` varchar(10) COLLATE utf8_bin NOT NULL,
  `from_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `from_email` varchar(96) COLLATE utf8_bin NOT NULL,
  `to_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `to_email` varchar(96) COLLATE utf8_bin NOT NULL,
  `voucher_theme_id` int(11) NOT NULL,
  `message` text COLLATE utf8_bin NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  PRIMARY KEY (`order_voucher_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}information`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}information` (
  `information_id` int(11) NOT NULL AUTO_INCREMENT,
  `sort_order` int(3) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`information_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}product`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}product` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(64) COLLATE utf8_bin NOT NULL,
  `sku` varchar(64) COLLATE utf8_bin NOT NULL,
  `upc` varchar(12) COLLATE utf8_bin NOT NULL,
  `location` varchar(128) COLLATE utf8_bin NOT NULL,
  `quantity` int(4) NOT NULL DEFAULT '0',
  `stock_status_id` int(11) NOT NULL,
  `image` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `manufacturer_id` int(11) NOT NULL,
  `shipping` tinyint(1) NOT NULL DEFAULT '1',
  `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `points` int(8) NOT NULL DEFAULT '0',
  `tax_class_id` int(11) NOT NULL,
  `date_available` datetime NOT NULL,
  `date_expires` datetime DEFAULT '0000-00-00 00:00:00',
  `weight` decimal(15,8) NOT NULL DEFAULT '0.00000000',
  `weight_class_id` int(11) NOT NULL DEFAULT '0',
  `length` decimal(15,8) NOT NULL DEFAULT '0.00000000',
  `width` decimal(15,8) NOT NULL DEFAULT '0.00000000',
  `height` decimal(15,8) NOT NULL DEFAULT '0.00000000',
  `length_class_id` int(11) NOT NULL DEFAULT '0',
  `subtract` tinyint(1) NOT NULL DEFAULT '1',
  `minimum` int(11) NOT NULL DEFAULT '1',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `viewed` int(5) NOT NULL DEFAULT '0',
  `cost` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `is_final` int(10) unsigned NOT NULL DEFAULT '0',
  `editable` int(10) unsigned NOT NULL DEFAULT '1',
  `__image_sort__image` float DEFAULT NULL,
  PRIMARY KEY (`product_id`,`date_available`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}affiliate`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}affiliate` (
  `affiliate_id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `lastname` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `email` varchar(96) COLLATE utf8_bin NOT NULL DEFAULT '',
  `telephone` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `fax` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `password` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
  `company` varchar(32) COLLATE utf8_bin NOT NULL,
  `website` varchar(255) COLLATE utf8_bin NOT NULL,
  `address_1` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `address_2` varchar(128) COLLATE utf8_bin NOT NULL,
  `city` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `postcode` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  `country_id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `code` varchar(64) COLLATE utf8_bin NOT NULL,
  `commission` decimal(4,2) NOT NULL DEFAULT '0.00',
  `tax` varchar(64) COLLATE utf8_bin NOT NULL,
  `payment` varchar(6) COLLATE utf8_bin NOT NULL,
  `cheque` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `paypal` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `bank_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `bank_branch_number` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `bank_swift_code` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `bank_account_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `bank_account_number` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `ip` varchar(15) COLLATE utf8_bin NOT NULL,
  `status` tinyint(1) NOT NULL,
  `approved` tinyint(1) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`affiliate_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}product_to_category`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}product_to_category` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`,`category_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}customer_group`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}customer_group` (
  `customer_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`customer_group_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}stock_status`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}stock_status` (
  `stock_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) NOT NULL,
  `name` varchar(32) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`stock_status_id`,`language_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}product_option_value`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}product_option_value` (
  `product_option_value_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_option_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `option_value_id` int(11) NOT NULL,
  `quantity` int(3) NOT NULL,
  `subtract` tinyint(1) NOT NULL,
  `cost` decimal(15,4) NOT NULL,
  `price` decimal(15,4) NOT NULL,
  `points` int(8) NOT NULL,
  `weight` decimal(15,8) NOT NULL,
  `option_restriction_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`product_option_value_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}page`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}page` (
  `page_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `layout_id` int(10) unsigned NOT NULL,
  `content` text,
  `meta_keywords` text,
  `meta_description` text,
  `status` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`page_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}secure_page`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}secure_page` (
  `secure_page_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `route` varchar(60) NOT NULL,
  PRIMARY KEY (`secure_page_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}product_discount`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}product_discount` (
  `product_discount_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `customer_group_id` int(11) NOT NULL,
  `quantity` int(4) NOT NULL DEFAULT '0',
  `priority` int(5) NOT NULL DEFAULT '1',
  `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `date_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`product_discount_id`),
  KEY `product_id` (`product_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}coupon`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}coupon` (
  `coupon_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_bin NOT NULL,
  `code` varchar(10) COLLATE utf8_bin NOT NULL,
  `type` char(1) COLLATE utf8_bin NOT NULL,
  `discount` decimal(15,4) NOT NULL,
  `logged` tinyint(1) NOT NULL,
  `shipping` tinyint(1) NOT NULL,
  `total` decimal(15,4) NOT NULL,
  `date_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uses_total` int(11) NOT NULL,
  `uses_customer` varchar(11) COLLATE utf8_bin NOT NULL,
  `status` tinyint(1) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `shipping_geozone` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`coupon_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}coupon_category`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}coupon_category` (
  `coupon_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`coupon_id`,`category_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}customer_transaction`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}customer_transaction` (
  `customer_transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`customer_transaction_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}manufacturer_to_store`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}manufacturer_to_store` (
  `manufacturer_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  PRIMARY KEY (`manufacturer_id`,`store_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}navigation_store`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}navigation_store` (
  `navigation_group_id` int(10) unsigned NOT NULL,
  `store_id` int(10) NOT NULL,
  PRIMARY KEY (`navigation_group_id`,`store_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}url_alias`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}url_alias` (
  `url_alias_id` int(11) NOT NULL AUTO_INCREMENT,
  `route` varchar(255) COLLATE utf8_bin NOT NULL,
  `query` varchar(255) COLLATE utf8_bin NOT NULL,
  `keyword` varchar(255) COLLATE utf8_bin NOT NULL,
  `status` int(2) unsigned NOT NULL DEFAULT '1',
  `store_id` int(10) NOT NULL,
  `redirect` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`url_alias_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}customer`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL DEFAULT '0',
  `firstname` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `lastname` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `email` varchar(96) COLLATE utf8_bin NOT NULL DEFAULT '',
  `telephone` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `fax` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `password` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
  `cart` text COLLATE utf8_bin,
  `wishlist` text COLLATE utf8_bin,
  `newsletter` tinyint(1) NOT NULL DEFAULT '0',
  `address_id` int(11) NOT NULL DEFAULT '0',
  `payment_code` varchar(45) COLLATE utf8_bin NOT NULL DEFAULT '',
  `customer_group_id` int(11) NOT NULL,
  `ip` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL,
  `approved` tinyint(1) NOT NULL,
  `token` varchar(255) COLLATE utf8_bin NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`customer_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}zone_to_geo_zone`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}zone_to_geo_zone` (
  `zone_to_geo_zone_id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL DEFAULT '0',
  `geo_zone_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`zone_to_geo_zone_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}translation`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}translation` (
  `translation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `table` varchar(255) NOT NULL,
  `field` varchar(255) NOT NULL,
  PRIMARY KEY (`translation_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}product_option`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}product_option` (
  `product_option_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `option_value` text COLLATE utf8_bin NOT NULL,
  `required` tinyint(1) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_option_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}option_description`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}option_description` (
  `option_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(128) COLLATE utf8_bin NOT NULL,
  `display_name` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`option_id`,`language_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}customer_ip`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}customer_ip` (
  `customer_ip_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `ip` varchar(15) COLLATE utf8_bin NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`customer_ip_id`),
  KEY `ip` (`ip`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}page_store`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}page_store` (
  `page_id` int(10) unsigned NOT NULL,
  `store_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`page_id`,`store_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}db_rule`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}db_rule` (
  `db_rule_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `table` varchar(45) NOT NULL,
  `column` varchar(90) DEFAULT NULL,
  `escape_type` varchar(45) DEFAULT NULL,
  `truncate` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`db_rule_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}product_template`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}product_template` (
  `product_id` int(10) unsigned NOT NULL,
  `template` varchar(255) NOT NULL DEFAULT '',
  `theme` varchar(45) NOT NULL DEFAULT 'default',
  `store_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`product_id`,`store_id`,`theme`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}tax_rate_to_customer_group`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}tax_rate_to_customer_group` (
  `tax_rate_id` int(11) NOT NULL,
  `customer_group_id` int(11) NOT NULL,
  PRIMARY KEY (`tax_rate_id`,`customer_group_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}navigation_group`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}navigation_group` (
  `navigation_group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `status` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`navigation_group_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}order_fraud`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}order_fraud` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `country_match` varchar(3) COLLATE utf8_bin NOT NULL,
  `country_code` varchar(2) COLLATE utf8_bin NOT NULL,
  `high_risk_country` varchar(3) COLLATE utf8_bin NOT NULL,
  `distance` int(11) NOT NULL,
  `ip_region` varchar(255) COLLATE utf8_bin NOT NULL,
  `ip_city` varchar(255) COLLATE utf8_bin NOT NULL,
  `ip_latitude` decimal(10,6) NOT NULL,
  `ip_longitude` decimal(10,6) NOT NULL,
  `ip_isp` varchar(255) COLLATE utf8_bin NOT NULL,
  `ip_org` varchar(255) COLLATE utf8_bin NOT NULL,
  `ip_asnum` int(11) NOT NULL,
  `ip_user_type` varchar(255) COLLATE utf8_bin NOT NULL,
  `ip_country_confidence` varchar(3) COLLATE utf8_bin NOT NULL,
  `ip_region_confidence` varchar(3) COLLATE utf8_bin NOT NULL,
  `ip_city_confidence` varchar(3) COLLATE utf8_bin NOT NULL,
  `ip_postal_confidence` varchar(3) COLLATE utf8_bin NOT NULL,
  `ip_postal_code` varchar(10) COLLATE utf8_bin NOT NULL,
  `ip_accuracy_radius` int(11) NOT NULL,
  `ip_net_speed_cell` varchar(255) COLLATE utf8_bin NOT NULL,
  `ip_metro_code` int(3) NOT NULL,
  `ip_area_code` int(3) NOT NULL,
  `ip_time_zone` varchar(255) COLLATE utf8_bin NOT NULL,
  `ip_region_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `ip_domain` varchar(255) COLLATE utf8_bin NOT NULL,
  `ip_country_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `ip_continent_code` varchar(2) COLLATE utf8_bin NOT NULL,
  `ip_corporate_proxy` varchar(3) COLLATE utf8_bin NOT NULL,
  `anonymous_proxy` varchar(3) COLLATE utf8_bin NOT NULL,
  `proxy_score` int(3) NOT NULL,
  `is_trans_proxy` varchar(3) COLLATE utf8_bin NOT NULL,
  `free_mail` varchar(3) COLLATE utf8_bin NOT NULL,
  `carder_email` varchar(3) COLLATE utf8_bin NOT NULL,
  `high_risk_username` varchar(3) COLLATE utf8_bin NOT NULL,
  `high_risk_password` varchar(3) COLLATE utf8_bin NOT NULL,
  `bin_match` varchar(10) COLLATE utf8_bin NOT NULL,
  `bin_country` varchar(2) COLLATE utf8_bin NOT NULL,
  `bin_name_match` varchar(3) COLLATE utf8_bin NOT NULL,
  `bin_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `bin_phone_match` varchar(3) COLLATE utf8_bin NOT NULL,
  `bin_phone` varchar(32) COLLATE utf8_bin NOT NULL,
  `customer_phone_in_billing_location` varchar(8) COLLATE utf8_bin NOT NULL,
  `ship_forward` varchar(3) COLLATE utf8_bin NOT NULL,
  `city_postal_match` varchar(3) COLLATE utf8_bin NOT NULL,
  `ship_city_postal_match` varchar(3) COLLATE utf8_bin NOT NULL,
  `score` decimal(10,5) NOT NULL,
  `explanation` text COLLATE utf8_bin NOT NULL,
  `risk_score` decimal(10,5) NOT NULL,
  `queries_remaining` int(11) NOT NULL,
  `maxmind_id` varchar(8) COLLATE utf8_bin NOT NULL,
  `error` text COLLATE utf8_bin NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`order_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}category_to_store`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}category_to_store` (
  `category_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  PRIMARY KEY (`category_id`,`store_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}order_status`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}order_status` (
  `order_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) NOT NULL,
  `name` varchar(32) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`order_status_id`,`language_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}extension`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}extension` (
  `extension_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(32) COLLATE utf8_bin NOT NULL,
  `code` varchar(32) COLLATE utf8_bin NOT NULL,
  `status` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`extension_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}layout`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}layout` (
  `layout_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`layout_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}navigation`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}navigation` (
  `navigation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `navigation_group_id` int(10) unsigned NOT NULL,
  `name` varchar(45) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `title` varchar(45) NOT NULL,
  `href` text NOT NULL,
  `query` text,
  `is_route` int(10) unsigned NOT NULL DEFAULT '0',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` int(10) unsigned NOT NULL DEFAULT '0',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`navigation_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}session`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}session` (
  `session_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(60) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `data` text,
  `ip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`session_id`),
  UNIQUE KEY `token_UNIQUE` (`token`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}order_option`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}order_option` (
  `order_option_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `order_product_id` int(11) NOT NULL,
  `product_option_id` int(11) NOT NULL,
  `product_option_value_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `value` text COLLATE utf8_bin NOT NULL,
  `type` varchar(32) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`order_option_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}zone`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}zone` (
  `zone_id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) NOT NULL,
  `code` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `name` varchar(128) COLLATE utf8_bin NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`zone_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}return_action`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}return_action` (
  `return_action_id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`return_action_id`,`language_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}manufacturer_article`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}manufacturer_article` (
  `article_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `manufacturer_id` int(10) unsigned NOT NULL,
  `title` varchar(45) NOT NULL,
  `description` text,
  `link` varchar(255) NOT NULL,
  PRIMARY KEY (`article_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}voucher`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}voucher` (
  `voucher_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `code` varchar(10) COLLATE utf8_bin NOT NULL,
  `from_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `from_email` varchar(96) COLLATE utf8_bin NOT NULL,
  `to_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `to_email` varchar(96) COLLATE utf8_bin NOT NULL,
  `voucher_theme_id` int(11) NOT NULL,
  `message` text COLLATE utf8_bin NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`voucher_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}information_to_layout`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}information_to_layout` (
  `information_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `layout_id` int(11) NOT NULL,
  PRIMARY KEY (`information_id`,`store_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}information_to_store`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}information_to_store` (
  `information_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  PRIMARY KEY (`information_id`,`store_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}attribute`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}attribute` (
  `attribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `attribute_group_id` int(11) NOT NULL,
  `sort_order` int(3) NOT NULL,
  PRIMARY KEY (`attribute_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}coupon_history`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}coupon_history` (
  `coupon_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`coupon_history_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}banner_image_description`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}banner_image_description` (
  `banner_image_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `banner_id` int(11) NOT NULL,
  `title` varchar(64) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`banner_image_id`,`language_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}order_history`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}order_history` (
  `order_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `order_status_id` int(5) NOT NULL,
  `notify` tinyint(1) NOT NULL DEFAULT '0',
  `comment` text COLLATE utf8_bin NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`order_history_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}layout_header`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}layout_header` (
  `layout_id` int(10) unsigned NOT NULL,
  `page_header_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`layout_id`,`page_header_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}order_total`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}order_total` (
  `order_total_id` int(10) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `code` varchar(32) COLLATE utf8_bin NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `text` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `value` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `sort_order` int(3) NOT NULL,
  PRIMARY KEY (`order_total_id`),
  KEY `idx_orders_total_orders_id` (`order_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}weight_class`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}weight_class` (
  `weight_class_id` int(11) NOT NULL AUTO_INCREMENT,
  `value` decimal(15,8) NOT NULL DEFAULT '0.00000000',
  PRIMARY KEY (`weight_class_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}plugin_registry`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}plugin_registry` (
  `plugin_registry_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) COLLATE latin1_general_ci NOT NULL,
  `date_added` datetime NOT NULL,
  `live_file` text COLLATE latin1_general_ci NOT NULL,
  `plugin_file` text COLLATE latin1_general_ci NOT NULL,
  `live_file_modified` int(10) unsigned NOT NULL,
  `plugin_file_modified` int(10) unsigned NOT NULL,
  PRIMARY KEY (`plugin_registry_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}product_tag`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}product_tag` (
  `product_tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `tag` varchar(32) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`product_tag_id`),
  KEY `product_id` (`product_id`),
  KEY `language_id` (`language_id`),
  KEY `tag` (`tag`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}block`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}block` (
  `block_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `settings` text,
  `profiles` text,
  `status` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`block_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}return_reason`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}return_reason` (
  `return_reason_id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`return_reason_id`,`language_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}product_to_download`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}product_to_download` (
  `product_id` int(11) NOT NULL,
  `download_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`,`download_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}setting`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}setting` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL DEFAULT '0',
  `group` varchar(32) COLLATE utf8_bin NOT NULL,
  `key` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `value` text COLLATE utf8_bin NOT NULL,
  `serialized` tinyint(1) NOT NULL,
  `auto_load` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`setting_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}length_class_description`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}length_class_description` (
  `length_class_id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) NOT NULL,
  `title` varchar(32) COLLATE utf8_bin NOT NULL,
  `unit` varchar(4) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`length_class_id`,`language_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}product_image`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}product_image` (
  `product_image_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `sort_order` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_image_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}order_product`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}order_product` (
  `order_product_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `model` varchar(24) COLLATE utf8_bin NOT NULL,
  `quantity` int(4) NOT NULL,
  `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `cost` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `total` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `tax` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `reward` int(8) NOT NULL,
  `is_final` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`order_product_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}review`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}review` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `author` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `text` text COLLATE utf8_bin NOT NULL,
  `rating` int(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`review_id`),
  KEY `product_id` (`product_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}category_to_layout`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}category_to_layout` (
  `category_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `layout_id` int(11) NOT NULL,
  PRIMARY KEY (`category_id`,`store_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}return`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}return` (
  `return_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `firstname` varchar(32) COLLATE utf8_bin NOT NULL,
  `lastname` varchar(32) COLLATE utf8_bin NOT NULL,
  `email` varchar(96) COLLATE utf8_bin NOT NULL,
  `telephone` varchar(32) COLLATE utf8_bin NOT NULL,
  `product` varchar(255) COLLATE utf8_bin NOT NULL,
  `model` varchar(64) COLLATE utf8_bin NOT NULL,
  `quantity` int(4) NOT NULL,
  `opened` tinyint(1) NOT NULL,
  `return_reason_id` int(11) NOT NULL,
  `return_action_id` int(11) NOT NULL,
  `return_status_id` int(11) NOT NULL,
  `comment` text COLLATE utf8_bin,
  `date_ordered` date NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`return_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}type_to_contact`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}type_to_contact` (
  `type_to_contact_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(45) NOT NULL,
  `type_id` int(10) unsigned NOT NULL,
  `contact_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`type_to_contact_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}return_history`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}return_history` (
  `return_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `return_id` int(11) NOT NULL,
  `return_status_id` int(11) NOT NULL,
  `notify` tinyint(1) NOT NULL,
  `comment` text COLLATE utf8_bin NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`return_history_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}store`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}store` (
  `store_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_bin NOT NULL,
  `url` varchar(255) COLLATE utf8_bin NOT NULL,
  `ssl` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`store_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}template`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}template` (
  `template_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `tables` text NOT NULL,
  `forms` text NOT NULL,
  `blocks` text NOT NULL,
  `options` text NOT NULL,
  `template_file` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`template_id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}tax_rate`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}tax_rate` (
  `tax_rate_id` int(11) NOT NULL AUTO_INCREMENT,
  `geo_zone_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(32) COLLATE utf8_bin NOT NULL,
  `rate` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `type` char(1) COLLATE utf8_bin NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`tax_rate_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}plugin_file_modification`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}plugin_file_modification` (
  `plugin_file_modification_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `original_file` varchar(255) NOT NULL,
  `mod_path` varchar(255) NOT NULL,
  PRIMARY KEY (`plugin_file_modification_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}coupon_customer`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}coupon_customer` (
  `coupon_id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`coupon_id`,`customer_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}newsletter`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}newsletter` (
  `newsletter_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `send_date` datetime NOT NULL,
  `data` text NOT NULL,
  `status` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`newsletter_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}product_to_store`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}product_to_store` (
  `product_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_id`,`store_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}product_special`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}product_special` (
  `product_special_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `customer_group_id` int(11) NOT NULL,
  `priority` int(5) NOT NULL DEFAULT '1',
  `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `date_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `flashsale_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_special_id`),
  KEY `product_id` (`product_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}coupon_product`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}coupon_product` (
  `coupon_product_id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`coupon_product_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}banner_image`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}banner_image` (
  `banner_image_id` int(11) NOT NULL AUTO_INCREMENT,
  `banner_id` int(11) NOT NULL,
  `link` varchar(255) COLLATE utf8_bin NOT NULL,
  `image` varchar(255) COLLATE utf8_bin NOT NULL,
  `sort_order` int(3) DEFAULT NULL,
  PRIMARY KEY (`banner_image_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}user`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_group_id` int(11) NOT NULL,
  `username` varchar(20) COLLATE utf8_bin NOT NULL DEFAULT '',
  `password` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `firstname` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `lastname` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `email` varchar(96) COLLATE utf8_bin NOT NULL DEFAULT '',
  `code` varchar(32) COLLATE utf8_bin NOT NULL,
  `ip` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}manufacturer`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}manufacturer` (
  `manufacturer_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `image` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `sort_order` int(3) NOT NULL,
  `featured_product_id` int(11) DEFAULT NULL,
  `keyword` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `section_attr` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `vendor_id` varchar(45) COLLATE utf8_bin NOT NULL DEFAULT '''''',
  `status` int(11) NOT NULL DEFAULT '0',
  `date_active` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_expires` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `editable` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`manufacturer_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}attribute_group`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}attribute_group` (
  `attribute_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `sort_order` int(3) NOT NULL,
  PRIMARY KEY (`attribute_group_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}category`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `description` text COLLATE utf8_bin,
  `meta_description` text COLLATE utf8_bin,
  `meta_keywords` text COLLATE utf8_bin,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `top` tinyint(1) NOT NULL,
  `column` int(3) NOT NULL,
  `sort_order` int(3) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `__image_sort__image` float DEFAULT NULL,
  PRIMARY KEY (`category_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}country`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}country` (
  `country_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_bin NOT NULL,
  `iso_code_2` varchar(2) COLLATE utf8_bin NOT NULL DEFAULT '',
  `iso_code_3` varchar(3) COLLATE utf8_bin NOT NULL DEFAULT '',
  `address_format` text COLLATE utf8_bin NOT NULL,
  `postcode_required` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`country_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}affiliate_transaction`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}affiliate_transaction` (
  `affiliate_transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `affiliate_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`affiliate_transaction_id`)
)
SQL;

$_[] = <<<SQL
DROP TABLE IF EXISTS `{$db_prefix}voucher_theme`;
SQL;

$_[] = <<<SQL
CREATE TABLE `{$db_prefix}voucher_theme` (
  `voucher_theme_id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`voucher_theme_id`)
)
SQL;

