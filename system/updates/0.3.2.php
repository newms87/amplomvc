<?php
$this->db->addColumn('page', 'excerpt', "TEXT AFTER `title`");
$this->db->addColumn('page', 'comments', "tinyint(3) AFTER `cache`");
$this->db->addColumn('page', 'date_created', "DATETIME AFTER `status`");
$this->db->addColumn('page', 'date_published', "DATETIME AFTER `date_created`");
$this->db->addColumn('page', 'type', "VARCHAR(45) NOT NULL AFTER `page_id`");
$this->db->addColumn('page', 'author_id', "INT(10) UNSIGNED NOT NULL AFTER `page_id`");
$this->db->changeColumn('page', 'layout_id', 'layout_id', "INT(10) UNSIGNED NOT NULL AFTER `type`");
$this->db->changeColumn('page', 'template', 'template', "VARCHAR(128) NOT NULL AFTER `layout_id`");
$this->db->changeColumn('page', 'status', 'status', "INT(10) UNSIGNED NOT NULL AFTER `comments`");
$this->db->changeColumn('page', 'title', 'title', "VARCHAR(256) NOT NULL");
$this->db->dropColumn('page', 'theme');

$pages = $this->Model_Page->getRecords();

foreach ($pages as $page) {
	$update = array();

	if ($o = @unserialize($page['options'])) {
		$update['options'] = json_encode($o);
	}

	if (empty($page['type'])) {
		$update['type'] = 'page';

		if ($page['status'] == 1) {
			$update['status']         = 2;
			$update['date_published'] = $page['date_updated'];
		}

		$update['date_created'] = $page['date_updated'];
	}

	if ($update) {
		$this->Model_Page->save($page['page_id'], $update);
	}
}

$this->db->addColumn('page_history', 'type', "VARCHAR(45) NOT NULL AFTER `user_id`");
$this->db->addColumn('page_history', 'layout_id', "VARCHAR(45) NOT NULL AFTER `type`");
$this->db->addColumn('page_history', 'excerpt', "VARCHAR(45) NOT NULL AFTER `template`");
$this->db->addColumn('page_history', 'options', "TEXT AFTER `meta_description`");
$this->db->addColumn('page_history', 'cache', "TINYINT(3) UNSIGNED AFTER `options`");
$this->db->dropColumn('page_history', 'display_title');
$this->db->dropColumn('page_history', 'theme');

$this->db->createTable('page_category', <<<SQL
  `page_id` INT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`page_id`, `category_id`)
SQL
);

$this->db->addColumn('category', 'sort_order', "INT(10) NOT NULL DEFAULT 0 AFTER `status`");

$this->db->createIndex('meta', 'TYPEKEY', array(
	'type' => 'ASC',
	'key'  => 'ASC',
));
$this->db->createIndex('meta', 'TYPEID', array(
	'type'      => 'ASC',
	'record_id' => 'ASC',
));
