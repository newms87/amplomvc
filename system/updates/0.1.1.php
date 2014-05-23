<?php
$this->db->addColumn('page', 'cache', "tinyint unsigned not null DEFAULT '1'");
$this->db->addColumn('page_store', 'layout_id', "int(11) unsigned not null DEFAULT '1'");

$pages = $this->queryRows("SELECT * FROM " . DB_PREFIX . "page");

foreach ($pages as $page) {
	if (!$page['name']) {
		$page['name'] = $this->tool->getSlug($page['title']);
		$this->update('page', array('name' => $page['name']), $page['page_id']);
	}

	if (!empty($page['content'])) {
		$dir = DIR_SITE . 'app/view/theme/' . option('config_theme', 'fluid') . '/template/page/' . $page['name'] . '/';

		_is_writable($dir);

		file_put_contents($dir . 'content.tpl', html_entity_decode($page['content']));
		file_put_contents($dir . 'style.less', html_entity_decode($page['css']));
	}

	$page_store = array(
		'page_id'   => $page['page_id'],
		'store_id'  => 1,
		'layout_id' => $page['layout_id'],
	);

	$this->insert('page_store', $page_store);
}

$this->db->dropColumn('page', 'content');
$this->db->dropColumn('page', 'css');
$this->db->dropColumn('page', 'layout_id');

//Informations to Pages
$informations = $this->queryRows("SELECT * FROM " . DB_PREFIX . "information");

foreach ($informations as $info) {
	$layout_id = $this->queryVar("SELECT layout_id FROM " . DB_PREFIX . "information_to_layout WHERE information_id = " . $info['information_id']);

	$page = array(
		'name'          => $this->tool->getSlug($info['title']),
		'title'         => $info['title'],
		'status'        => $info['status'],
		'display_title' => 1,
		'cache'         => 1,
	);

	if (!$this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "page WHERE `name` = '" . $page['name'] . "'")) {
		$this->insert('page', $page);
	}

	$dir = DIR_SITE . 'app/view/theme/' . option('config_theme', 'fluid') . '/template/page/' . $page['name'] . '/';

	_is_writable($dir);

	file_put_contents($dir . 'content.tpl', html_entity_decode($info['description']));
	file_put_contents($dir . 'style.less', '');
}

$this->db->dropTable('information');
$this->db->dropTable('information_to_layout');
$this->db->dropTable('information_to_store');