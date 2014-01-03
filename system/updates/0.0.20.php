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
  `payment_method` varchar(127) NOT NULL,
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
	}
	else {
		$shipping_id = '';
	}

	$order_update = array(
		'transaction_id' => $transaction_id,
		'shipping_id'    => $shipping_id,
	);

	$this->update('order', $order_update, $order['order_id']);
}

$this->db->dropColumn('order', 'payment_method_id');
$this->db->dropColumn('payment_zone_id');
$this->db->dropColumn('payment_country_id');
$this->db->dropColumn('payment_postcode');
$this->db->dropColumn('payment_city');
$this->db->dropColumn('payment_address_2');
$this->db->dropColumn('payment_address_1');
$this->db->dropColumn('payment_company');
$this->db->dropColumn('payment_lastname');
$this->db->dropColumn('payment_firstname');
$this->db->dropColumn('shipping_method_id');
$this->db->dropColumn('shipping_zone_id');
$this->db->dropColumn('shipping_country_id');
$this->db->dropColumn('shipping_postcode');
$this->db->dropColumn('shipping_city');
$this->db->dropColumn('shipping_address_2');
$this->db->dropColumn('shipping_address_1');
$this->db->dropColumn('shipping_company');
$this->db->dropColumn('shipping_lastname');
$this->db->dropColumn('shipping_firstname');
