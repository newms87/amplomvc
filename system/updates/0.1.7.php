<?php

if ($this->db->hasColumn('view', 'listing_id')) {
	$views = $this->queryRows("SELECT * FROM " . DB_PREFIX . "view");

	$listings = $this->queryRows("SELECT * FROM " . DB_PREFIX . "view_listing");

	foreach ($views as $view) {
		$view_listing_id = false;
		if ($view['listing_id']) {

			foreach ($listings as $listing) {
				if ($view['listing_id'] === $listing['slug']) {
					$view_listing_id = $listing['view_listing_id'];
				}
			}

			if (!$view_listing_id) {
				trigger_error("VIEW LISTING ID NOT FOUND");
				break;
			}

			$this->update('view', array('listing_id' => $view_listing_id), $view['view_id']);
		}
	}

	$this->db->changeColumn('view', 'listing_id', 'view_listing_id', "INT(10) UNSIGNED NOT NULL AFTER `group`");
}

$this->db->addColumn('view', 'sort_order', "INT NOT NULL DEFAULT 0 AFTER `show`");
$this->db->dropColumn('view', 'path');
$this->db->addColumn('view', 'settings', "TEXT NULL AFTER `show`");
$this->db->addColumn('view', 'view_type', "VARCHAR(45) NOT NULL AFTER `query`");

$this->db->createTable('view_meta', <<<SQL
  `view_meta_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `view_id` INT UNSIGNED NOT NULL,
  `key` VARCHAR(45) NOT NULL,
  `value` TEXT NOT NULL,
  `serialized` TINYINT UNSIGNED NOT NULL,
  `date` DATETIME NOT NULL,
  PRIMARY KEY (`view_meta_id`)
SQL
);
