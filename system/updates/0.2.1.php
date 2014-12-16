<?php
$this->db->addColumn('store', 'prefix', "VARCHAR(15) NOT NULL");
$this->db->changeColumn('navigation', 'href', 'path', "TEXT NOT NULL");

$this->db->dropColumn('block_area', 'store_id');
$this->db->dropColumn('customer', 'store_id');
$this->db->dropColumn('layout_route', 'store_id');
$this->db->dropColumn('setting', 'store_id');
$this->db->dropColumn('url_alias', 'store_id');

$this->db->dropTable('download_description');
$this->db->dropTable('extension');
$this->db->dropTable('navigation_store');
$this->db->dropTable('page_store');