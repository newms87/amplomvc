<?php

if ($this->db->has_column('plugin_registry', 'plugin_file_modified')) {
	$this->db->query("ALTER TABLE `" . DB_PREFIX . "plugin_registry` DROP COLUMN `plugin_file_modified`");
}

if ($this->db->has_column('plugin_registry', 'live_file_modified')) {
	$this->db->query("ALTER TABLE `" . DB_PREFIX . "plugin_registry` DROP COLUMN `live_file_modified`");
}