<?php
$tables = $this->db->getTables();

foreach ($tables as $table) {
	if (strpos($table, 'oc_')) {
		$this->query("ALTER TABLE `$table` RENAME TO " . DB_PREFIX . str_replace("oc_", '', $table));
	}
}

if (!$this->db->hasColumn('information', 'title')) {
	$this->db->query("ALTER TABLE `" . DB_PREFIX . "information` ADD COLUMN `title` VARCHAR(128) NOT NULL  AFTER `sort_order`");

	if ($this->db->hasTable('information_description')) {
		$this->db->query("UPDATE " . DB_PREFIX . "information i SET title = (SELECT title FROM " . DB_PREFIX . "information_description id WHERE id.information_id = i.information_id) WHERE information_id  > 0");
	}
}

if (!$this->db->hasColumn('information', 'description')) {
	$this->db->query("ALTER TABLE `" . DB_PREFIX . "information` ADD COLUMN `description` TEXT NOT NULL  AFTER `title`");

	if ($this->db->hasTable('information_description')) {
		$this->db->query("UPDATE " . DB_PREFIX . "information i SET description = (SELECT description FROM " . DB_PREFIX . "information_description id WHERE id.information_id = i.information_id) WHERE information_id > 0");
	}
}

$this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "information_description");

$this->db->dropColumn('plugin_registry', 'plugin_file_modified');
$this->db->dropColumn('plugin_registry', 'live_file_modified');


$this->db->changeColumn('return', 'date_ordered', '', 'DATETIME NOT NULL');
$this->db->dropColumn('return', 'product');
$this->db->dropColumn('return', 'model');

$this->db->addColumn('return', 'rma', "VARCHAR(45) NOT NULL AFTER `return_id`, ADD UNIQUE INDEX `RMA_UNIQUE` (`rma` ASC)");

$this->db->dropTable('return_action');
$this->db->dropTable('return_reason');
$this->db->dropTable('return_status');

if ($this->db->addColumn('product', 'information', 'TEXT NOT NULL AFTER `teaser`')) {
	$this->db->query("UPDATE " . DB_PREFIX . "product SET information = description");
	$this->db->query("UPDATE " . DB_PREFIX . "product SET description = teaser");
	$this->db->query("UPDATE " . DB_PREFIX . "product SET teaser = ''");
}

$this->db->dropColumn('order', 'payment_address_format');
$this->db->dropColumn('order', 'shipping_address_format');

$this->db->addColumn('extension', 'sort_order', "INT UNSIGNED NOT NULL DEFAULT 0  AFTER `code`");

//Settings
$this->db->addColumn('setting', 'translate', "TINYINT(1) UNSIGNED NOT NULL AFTER `serialized`");

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

//Remove Tables
$this->db->dropTable('tag_translation');
$this->db->dropTable('category_description');
$this->db->dropTable('attribute_description');
$this->db->dropTable('attribute_group_description');

//Options
$this->db->addColumn('option', 'name', "VARCHAR(45) NOT NULL AFTER `option_id`");
$this->db->addColumn('option', 'display_name', "VARCHAR(128) NOT NULL AFTER `name`");

if ($this->db->hasTable('option_description')) {
	$this->db->query("UPDATE " . DB_PREFIX . "option o SET name = (SELECT name FROM " . DB_PREFIX . "option_description od WHERE od.option_id=o.option_id AND od.language_id = " . $this->language->id() . ")");
	$this->db->query("UPDATE " . DB_PREFIX . "option o SET display_name = (SELECT display_name FROM " . DB_PREFIX . "option_description od WHERE od.option_id=o.option_id AND od.language_id = " . $this->language->id() . ")");

	$this->db->dropTable('option_description');
}

$this->db->addColumn('option_value', 'name', "VARCHAR(128) NOT NULL AFTER `option_id`");

if ($this->db->hasTable('option_value_description')) {
	$this->db->query("UPDATE " . DB_PREFIX . "option_value ov SET name = (SELECT name FROM " . DB_PREFIX . "option_value_description ovd WHERE ovd.option_value_id=ov.option_value_id AND ovd.language_id = " . $this->language->id() . ")");

	$this->db->dropTable('option_value_description');
}

//Product Option
$this->db->addColumn('product_option', 'name', "VARCHAR(45) NOT NULL  AFTER `option_id`");
$this->db->addColumn('product_option', 'display_name', "VARCHAR(128) NOT NULL  AFTER `name`");
$this->db->addColumn('product_option', 'type', "VARCHAR(45) NOT NULL  AFTER `option_value`");
$this->db->addColumn('product_option', 'group_type', "VARCHAR(45) NOT NULL  AFTER `type`");

//Product Option Value
$this->db->addColumn('product_option_value', 'name', "VARCHAR(45) NOT NULL  AFTER `option_value_id`");
$this->db->addColumn('product_option_value', 'image', "VARCHAR(255) NOT NULL  AFTER `name`");
$this->db->addColumn('product_option_value', 'sort_order', "INT NOT NULL DEFAULT 0 AFTER `option_restriction_id`");

//Product Option Restrictions
$this->db->changeColumn('product_option_value_restriction', 'option_value_id', 'product_option_value_id', "INT(10) UNSIGNED NOT NULL");
$this->db->changeColumn('product_option_value_restriction', 'restrict_option_value_id', 'restrict_product_option_value_id', "INT(10) UNSIGNED NOT NULL");

$this->db->changeColumn('option_value', 'name', 'value', "VARCHAR(128) NOT NULL");
$this->db->changeColumn('product_option_value', 'name', 'value', "VARCHAR(128) NOT NULL");
//Url Alias
$this->db->changeColumn('url_alias', 'keyword', 'alias', "VARCHAR(255) NOT NULL  AFTER `url_alias_id`");
$this->db->changeColumn('url_alias', 'route', 'path', "VARCHAR(255) NOT NULL");
$this->db->query("UPDATE " . DB_PREFIX . "url_alias SET store_id = 0 WHERE store_id = '-1'");

//Update .htaccess Files
$htaccess = DIR_SITE . '.htaccess';
file_put_contents($htaccess, str_replace('_route_', '_path_', file_get_contents($htaccess)));

//Product Class
$this->db->addColumn('product', 'product_class_id', "INT UNSIGNED NOT NULL AFTER `product_id`");

$this->db->createTable('product_class', <<<SQL
  `product_class_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `admin_template` text NOT NULL,
  `front_template` text NOT NULL,
  `defaults` text,
  PRIMARY KEY (`product_class_id`)
SQL
);

$link = array(
	'display_name' => "Product Classes",
	'name'         => 'catalog_products_product_classes',
	'href'         => 'catalog/product_class',
	'sort_order'   => 10,
	'parent'       => 'catalog_products',
);

$this->extend->addNavigationLink('admin', $link);

//Route to Path change
$this->db->changeColumn('view_count', 'route', 'path', "VARCHAR(255) NOT NULL");

//Product Options
$this->db->addColumn('product_option_value', 'default', "TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `sort_order`");
$this->db->addColumn('product_attribute', 'image', "VARCHAR(255) NOT NULL  AFTER `text`");
$this->db->addColumn('product_attribute', 'sort_order', "INT UNSIGNED NOT NULL  AFTER `image`");
$this->db->dropColumn('product_attribute', 'language_id');

//This is for integration with Already active sites (useless for future versions as this is included in the Create Table statement in 0.0.9)
$this->db->addColumn('product_class', 'defaults', "TEXT NULL  AFTER `front_template`");

$this->db->addColumn('attribute', 'image', "VARCHAR(255) NOT NULL  AFTER `name`");

//Plugins
$this->db->dropTable('plugin_file_modification');

$this->db->addColumn('page', 'display_title', "TINYINT UNSIGNED NOT NULL DEFAULT 0  AFTER `status`");
$this->db->changeColumn('page', 'name', 'title', "VARCHAR(45) NOT NULL");

$this->db->createTable('user_meta', <<<SQL
	`user_meta_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(10) unsigned NOT NULL,
	`key` varchar(64) NOT NULL,
	`value` text NOT NULL,
	`serialized` tinyint(3) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`user_meta_id`)
SQL
);

//Customer Meta
if ($this->db->hasTable('customer_setting')) {
	$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer_setting` CHANGE COLUMN `customer_setting_id` `customer_meta_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, RENAME TO  `" . DB_PREFIX . "customer_meta`");
}

$this->db->addColumn('block', 'profile_settings', "TEXT NULL  AFTER `settings`");


//Extensions
$this->db->addColumn('extension', 'title', "VARCHAR(255) NOT NULL  AFTER `code`");
$this->db->addColumn('extension', 'settings', "TEXT NULL  AFTER `title`");
$this->db->changeColumn('extension', 'sort_order', 'sort_order', "INT(10) NOT NULL DEFAULT '0'");

//Options
$this->db->addColumn('option_value', 'display_value', "VARCHAR(256) NOT NULL  AFTER `value`");
$this->db->addColumn('product_option_value', 'display_value', "VARCHAR(256) NOT NULL  AFTER `value`");

//Navigation
$this->db->dropColumn('navigation', 'is_route');
$this->db->addColumn('navigation', 'condition', "VARCHAR(45) NOT NULL  AFTER `query`");
$this->db->changeColumn('navigation', 'parent_id', 'parent_id', "INT(10) UNSIGNED NOT NULL DEFAULT '0'  AFTER `navigation_group_id`");


//Product Table Fix
//$this->db->query("ALTER TABLE `realmeal`.`ac_product` DROP PRIMARY KEY , ADD PRIMARY KEY (`product_id`)");
$this->db->changeColumn('product', 'product_id', 'product_id', "INT(11) UNSIGNED NOT NULL AUTO_INCREMENT");

//Product Class
$this->db->addColumn('product_class', 'status', "INT(10) UNSIGNED NOT NULL DEFAULT '1'");

$this->db->changeColumn('customer', 'password', 'password', "VARCHAR(255) NOT NULL DEFAULT ''");
$this->db->changeColumn('user', 'password', 'password', "VARCHAR(255) NOT NULL DEFAULT ''");

//Add PASSWORD_COST definition into config file
$ac_config = DIR_SITE . 'ac_config.php';
$contents  = file_get_contents($ac_config);

if (strpos($contents, 'PASSWORD_COST') === false) {
	$contents .= "\r\n\r\n//Password Hashing\r\ndefine(\"PASSWORD_COST\", 10);";

	file_put_contents($ac_config, $contents);
}

$this->db->addColumn('product_class', 'App_Controller_Admin', 'TEXT NOT NULL');
$this->db->addColumn('product_class', 'front_controller', 'TEXT NOT NULL');

//Order options
$this->db->dropColumn("order_option", 'name');
$this->db->dropColumn("order_option", 'value');
$this->db->dropColumn("order_option", 'type');
$this->db->addColumn('order_option', 'product_id', "INT(11) UNSIGNED NOT NULL  AFTER `order_product_id`");
$this->db->changeColumn('order_option', 'order_option_id', null, "INT(11) UNSIGNED NOT NULL AUTO_INCREMENT");
$this->db->changeColumn('order_option', 'order_id', null, "INT(11) UNSIGNED NOT NULL");
$this->db->changeColumn('order_option', 'order_product_id', null, "INT(11) UNSIGNED NOT NULL");
$this->db->changeColumn('order_option', 'product_option_id', null, "INT(11) UNSIGNED NOT NULL");
$this->db->changeColumn('order_option', 'product_option_value_id', null, "INT(11) UNSIGNED NOT NULL");

$this->db->addColumn('language', 'datetime_format_full', "VARCHAR(45) NOT NULL  AFTER `datetime_format_long`");
$this->db->addColumn('language', 'date_format_medium', "VARCHAR(45) NOT NULL  AFTER `date_format_short`");

//Shipping
$this->db->createTable('shipping', <<<SQL
  `shipping_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_code` varchar(127) NOT NULL,
  `shipping_key` varchar(255) NOT NULL,
  `tracking` varchar(45) NOT NULL,
  `address_id` int(10) unsigned NOT NULL,
  `status` varchar(45) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`shipping_id`)
SQL
);

$this->db->createTable('shipping_history', <<<SQL
  `shipping_history_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `shipping_id` INT UNSIGNED NOT NULL,
  `type` VARCHAR(45) NOT NULL,
  `comment` VARCHAR(45) NOT NULL,
  `status` VARCHAR(45) NOT NULL,
  `date_added` DATETIME NOT NULL,
  PRIMARY KEY (`shipping_history_id`)
SQL
);


$this->db->dropTable('customer_transaction');
$this->db->createTable('transaction', <<<SQL
  `transaction_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(45) NOT NULL,
  `description` text NOT NULL,
  `payment_code` varchar(127) NOT NULL,
  `payment_key` varchar(255) NOT NULL,
  `address_id` int(11) NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `retries` int(10) unsigned NOT NULL,
  `status` varchar(45) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`transaction_id`)
SQL
);

$this->db->createTable('transaction_history', <<<SQL
  `transaction_history_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `transaction_id` INT UNSIGNED NOT NULL,
  `type` VARCHAR(45) NOT NULL,
  `comment` TEXT NOT NULL,
  `status` VARCHAR(45) NOT NULL,
  `date_added` DATETIME NOT NULL,
  PRIMARY KEY (`transaction_history_id`)
SQL
);

//Address
$this->db->addColumn('address', 'locked', "TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER `zone_id`");
$this->db->addColumn('address', 'status', "TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER `locked`");

$this->db->createTable('customer_address', <<<SQL
  `customer_id` INT UNSIGNED NOT NULL,
  `address_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`customer_id`, `address_id`)
SQL
);


$addresses = $this->db->queryRows("SELECT * FROM " . DB_PREFIX . "address");

foreach ($addresses as $address) {
	if (!empty($address['customer_id'])) {
		if (!$this->db->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "customer_address WHERE customer_id = $address[customer_id] AND address_id = $address[address_id]")) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_address SET customer_id = $address[customer_id], address_id = $address[address_id]");
		}
	}
}

$this->db->dropColumn('address', 'customer_id');
$this->db->dropColumn('customer', 'address_id');


//Convert Order Payment / Shipping to transaction / shipping IDs
$this->db->addColumn('order', 'transaction_id', "INT UNSIGNED NOT NULL AFTER `payment_method_id`");
$this->db->addColumn('order', 'shipping_id', "INT UNSIGNED NOT NULL AFTER `transaction_id`");

$orders = $this->queryRows("SELECT * FROM " . DB_PREFIX . "order");

foreach ($orders as $order) {
	if ($order['transaction_id'] || $order['shipping_id']) {
		continue;
	}

	$payment_address = array(
		'firstname'  => $order['payment_firstname'],
		'lastname'   => $order['payment_lastname'],
		'company'    => $order['payment_company'],
		'address_1'  => $order['payment_address_1'],
		'address_2'  => $order['payment_address_2'],
		'city'       => $order['payment_city'],
		'postcode'   => $order['payment_postcode'],
		'country_id' => $order['payment_country_id'],
		'zone_id'    => $order['payment_zone_id'],
	);

	$payment_address_id = $this->insert('address', $payment_address);

	$transaction = array(
		'payment_method' => $order['payment_method_id'],
		'payment_key'    => '',
		'address_id'     => $payment_address_id,
		'amount'         => $order['total'],
	);

	$transaction_id = $this->transaction->add('order', $transaction);

	if ($order['confirmed']) {
		$this->transaction->updateStatus($transaction_id, Transaction::COMPLETE);
	}

	if ($order['shipping_method_id']) {
		$shipping_address = array(
			'firstname'  => $order['shipping_firstname'],
			'lastname'   => $order['shipping_lastname'],
			'company'    => $order['shipping_company'],
			'address_1'  => $order['shipping_address_1'],
			'address_2'  => $order['shipping_address_2'],
			'city'       => $order['shipping_city'],
			'postcode'   => $order['shipping_postcode'],
			'country_id' => $order['shipping_country_id'],
			'zone_id'    => $order['shipping_zone_id'],
		);

		$shipping_address_id = $this->insert('address', $shipping_address);

		$shipping = array(
			'shipping_method' => $order['shipping_method_id'],
			'address_id'      => $shipping_address_id,
			'tracking'        => '',
		);

		$shipping_id = $this->shipping->add('order', $shipping);

		if ($order['confirmed']) {
			$this->shipping->confirm($shipping_id);
		}
	} else {
		$shipping_id = '';
	}

	$order_update = array(
		'transaction_id' => $transaction_id,
		'shipping_id'    => $shipping_id,
	);

	$this->update('order', $order_update, $order['order_id']);
}

$this->db->dropColumn('order', 'payment_method_id');
$this->db->dropColumn('order', 'payment_zone_id');
$this->db->dropColumn('order', 'payment_country_id');
$this->db->dropColumn('order', 'payment_postcode');
$this->db->dropColumn('order', 'payment_city');
$this->db->dropColumn('order', 'payment_address_2');
$this->db->dropColumn('order', 'payment_address_1');
$this->db->dropColumn('order', 'payment_company');
$this->db->dropColumn('order', 'payment_lastname');
$this->db->dropColumn('order', 'payment_firstname');
$this->db->dropColumn('order', 'shipping_method_id');
$this->db->dropColumn('order', 'shipping_zone_id');
$this->db->dropColumn('order', 'shipping_country_id');
$this->db->dropColumn('order', 'shipping_postcode');
$this->db->dropColumn('order', 'shipping_city');
$this->db->dropColumn('order', 'shipping_address_2');
$this->db->dropColumn('order', 'shipping_address_1');
$this->db->dropColumn('order', 'shipping_company');
$this->db->dropColumn('order', 'shipping_lastname');
$this->db->dropColumn('order', 'shipping_firstname');

$this->db->dropTable("affiliate");
$this->db->dropTable("affiliate_transaction");

$this->db->changeColumn("block", "name", "path", "VARCHAR(45) NOT NULL");

$this->db->dropColumn('user', 'ip');

//Blocks
$this->db->createTable('block_instance', <<<SQL
	  `block_instance_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	  `path` VARCHAR(128) NOT NULL,
	  `name` VARCHAR(45) NOT NULL,
	  `title` VARCHAR(255) NOT NULL,
	  `show_title` TINYINT UNSIGNED NOT NULL,
	  `settings` TEXT NULL,
	  `status` TINYINT UNSIGNED NOT NULL,
	  PRIMARY KEY (`block_instance_id`)
SQL
);

$this->db->createTable('block_area', <<<SQL
  `block_area_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `path` VARCHAR(128) NOT NULL,
  `instance_name` VARCHAR(45) NOT NULL,
  `area` VARCHAR(45) NOT NULL,
  `store_id` INT UNSIGNED NOT NULL,
  `layout_id` INT UNSIGNED NOT NULL,
  `sort_order` INT(10) NOT NULL,
  PRIMARY KEY (`block_area_id`)
SQL
);


$this->db->changeColumn('block', 'path', 'path', "VARCHAR(128) NOT NULL");
$this->db->dropColumn('block', 'profile_settings');
$this->db->dropColumn('block', 'profiles');


//Page
$this->db->addColumn('page', 'template', "VARCHAR(128) NOT NULL AFTER `layout_id`");
$this->db->addColumn('page', 'name', "VARCHAR(128) NOT NULL AFTER `page_id`");
