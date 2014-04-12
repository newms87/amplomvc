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

$this->db->createTable('block_profile', <<<SQL
  `block_profile_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(128) NOT NULL,
  `block_instance_id` int(10) unsigned NOT NULL,
  `name` varchar(45) NOT NULL,
  `position` varchar(45) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`block_profile_id`)
SQL
);

$this->db->createTable('block_profile_layout', <<<SQL
  `block_profile_id` INT UNSIGNED NOT NULL,
  `store_id` INT UNSIGNED NOT NULL,
  `layout_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`block_profile_id`, `store_id`, `layout_id`)
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

	if (!empty($block['profiles'])) {
		$profiles = unserialize($block['profiles']);

		foreach ($profiles as $profile) {
			$profile['path'] = $block['path'];
			$profile['block_instance_id'] = $profile['profile_setting_id'];

			if ($this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "block_profile WHERE block_instance_id = " . (int)$profile['block_instance_id'] . " AND name = '" . $this->escape($profile['name']) . "'")) {
				continue;
			}

			$block_profile_id = $this->insert('block_profile', $profile);

			$profile_layout = array(
				'block_profile_id' => $block_profile_id,
			);

			foreach ($profile['store_ids'] as $store_id) {
				$profile_layout['store_id'] = $store_id;

				foreach ($profile['layout_ids'] as $layout_id) {
					$profile_layout['layout_id'] = $layout_id;


					$this->insert('block_profile_layout', $profile_layout);
				}
			}
		}
	}
}


$this->db->dropColumn('block', 'profile_settings');
$this->db->dropColumn('block', 'profiles');
