<?php
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

$blocks = $this->db->queryRows("SELECT * FROM " . DB_PREFIX . "block");

foreach ($blocks as $block) {
	if ($block['profile_settings']) {
		$instances = unserialize($block['profile_settings']);

		foreach ($instances as $key => $instance) {
			$name       = $instance['name'];
			$block_name = $this->tool->getSlug($name);
			$show_title = $instance['show_block_title'];

			unset($instance['name']);
			unset($instance['show_block_title']);

			$data = array(
				'path'       => $block['path'],
				'name'       => $block_name,
				'title'      => $name,
				'show_title' => $show_title,
				'settings'   => serialize($instance),
				'status'     => 1,
			);

			if (!$this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "block_instance WHERE `path`='" . $block['path'] . "' AND `name`='" . $block_name . "'")) {
				$this->insert('block_instance', $data);
			}
		}
	}
}
