<?php
$pages = $this->queryRows("SELECT * FROM " . DB_PREFIX . "page");

foreach ($pages as $page) {
	if (!$page['name']) {
		$page['name'] = $this->tool->getSlug($page['title']);
		$this->update('page', array('name' => $page['name']), $page['page_id']);
	}

	if (!empty($page['content'])) {
		$dir = DIR_SITE . 'catalog/view/theme/' . option('config_theme', 'fluid') . '/template/page/' . $page['name'] . '/';

		_is_writable($dir);

		file_put_contents($dir . 'content.tpl', html_entity_decode($page['content']));
		file_put_contents($dir . 'style.less', html_entity_decode($page['css']));
	}
}

$this->db->dropColumn('page', 'content');
$this->db->dropColumn('page', 'css');
$this->db->addColumn('page', 'cache', "tinyint unsigned not null DEFAULT '1'");
