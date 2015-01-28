<?php
$this->db->dropColumn('product', 'product_class_id');
$this->db->dropTable('product_class');

$this->db->addColumn('dashboard', 'title', "VARCHAR(45) NOT NULL AFTER `name`");

$dashboards = $this->Model_Dashboard->getRecords();

foreach ($dashboards as $dash) {
	if (!$dash['title']) {
		$this->db->query("UPDATE {$this->t['dashboard']} SET title = '" . $this->escape($dash['name']) . "', name = '" . $this->escape(slug($dash['name'])) . "' WHERE dashboard_id = " . (int)$dash['dashboard_id']);
	}
}

$this->db->addColumn('user_role', 'level', "INT UNSIGNED NOT NULL");
