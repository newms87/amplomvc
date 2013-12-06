<?php
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

//Language
$this->db->addColumn('language', 'datetime_format_full', "VARCHAR(45) NOT NULL  AFTER `datetime_format_long`");
$this->db->addColumn('language', 'date_format_medium', "VARCHAR(45) NOT NULL  AFTER `date_format_short`");

//Shipping
$this->db->createTable('shipping', <<<SQL
  `shipping_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_method` varchar(45) NOT NULL,
  `tracking` varchar(45) NOT NULL,
  `firstname` varchar(45) DEFAULT NULL,
  `lastname` varchar(45) DEFAULT NULL,
  `company` varchar(45) DEFAULT NULL,
  `address_1` varchar(128) NOT NULL,
  `address_2` varchar(128) DEFAULT NULL,
  `city` varchar(128) DEFAULT NULL,
  `postcode` varchar(20) NOT NULL,
  `zone_id` int(10) unsigned NOT NULL,
  `country_id` int(10) unsigned NOT NULL,
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
  `payment_method` varchar(127) NOT NULL,
  `payment_key` varchar(255) NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `description` text NOT NULL,
  `address_id` int(11) unsigned NOT NULL,
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


//TODO : convert order Payments / Shipping information into transaction / shipping tables
