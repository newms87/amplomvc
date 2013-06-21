<?php
if (!$this->db->has_column('information', 'title')) {
	$this->db->query("ALTER TABLE `" . DB_PREFIX . "information` ADD COLUMN `title` VARCHAR(128) NOT NULL  AFTER `sort_order`");
	
	if ($this->db->has_table('information_description')) {
		$this->db->query("UPDATE " . DB_PREFIX . "information i SET title = (SELECT title FROM " . DB_PREFIX . "information_description id WHERE id.information_id = i.information_id) WHERE information_id  > 0");
	}
}

if (!$this->db->has_column('information', 'description')) {
	$this->db->query("ALTER TABLE `" . DB_PREFIX . "information` ADD COLUMN `description` TEXT NOT NULL  AFTER `title`");
	
	if ($this->db->has_table('information_description')) {
		$this->db->query("UPDATE " . DB_PREFIX . "information i SET description = (SELECT description FROM " . DB_PREFIX . "information_description id WHERE id.information_id = i.information_id) WHERE information_id > 0");
	}
}

$this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "information_description");