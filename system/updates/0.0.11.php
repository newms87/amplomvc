<?php
$this->db->addColumn('product_attribute', 'image', "VARCHAR(255) NOT NULL  AFTER `text`");
$this->db->addColumn('product_attribute', 'sort_order', "INT UNSIGNED NOT NULL  AFTER `image`");
$this->db->dropColumn('product_attribute', 'language_id');

$this->db->addColumn('attribute', 'image', "VARCHAR(255) NOT NULL  AFTER `name`");

//Plugins
$this->db->dropTable('plugin_file_modification');