<?php
$this->db->createTable('invoice', <<<SQL
  `invoice_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `number` VARCHAR(45) NOT NULL,
  `customer_id` INT UNSIGNED NOT NULL,
  `payment_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `amount` INT UNSIGNED NOT NULL DEFAULT 0,
  `date_created` DATETIME NOT NULL,
  `date_paid` DATETIME NULL,
  `date_updated` DATETIME NULL,
  `date_due` DATETIME NULL,
  `status` INT UNSIGNED NOT NULL,
  `data` TEXT NULL,
  PRIMARY KEY (`invoice_id`)
SQL
);

$this->db->addColumn('invoice', 'amount', "INT UNSIGNED NOT NULL DEFAULT 0 AFTER `payment_id`");

$this->db->createTable('meta', <<<SQL
  `meta_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(60) NOT NULL,
  `record_id` INT UNSIGNED NOT NULL,
  `key` VARCHAR(45) NOT NULL,
  `value` TEXT NOT NULL,
  `serialized` TINYINT UNSIGNED NOT NULL,
  `date` DATETIME NOT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `RECORD` (`record_id`),
  KEY `TYPE` (`type`)
SQL
);

if (!$this->db->hasColumn('page', 'options')) {
	$this->db->addColumn('page', 'options', "TEXT NULL AFTER `status`");

	if ($this->db->hasColumn('page', 'display_title')) {
		$pages = $this->db->queryRows("SELECT page_id, display_title FROM {$this->db->t['page']}");

		foreach ($pages as $p) {
        $options = array('display_title' => $p['display_title']);

        $this->db->query("UPDATE {$this->db->t['page']} SET `options` = '" . serialize($options) . "' WHERE page_id = " . $p['page_id']);
		}

     $this->db->dropColumn('page', 'display_title');
	}

  $this->db->query("ALTER TABLE `{$this->t['page']}` ADD UNIQUE INDEX `THEMENAME` (`theme` ASC, `name` ASC)");
}

