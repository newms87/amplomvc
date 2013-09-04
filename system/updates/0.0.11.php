<?php
$this->db->addColumn('product_attribute', 'image', "VARCHAR(255) NOT NULL  AFTER `text`");
$this->db->addColumn('product_attribute', 'sort_order', "INT UNSIGNED NOT NULL  AFTER `image`");
$this->db->dropColumn('product_attribute', 'language_id');

//This is for integration with Already active sites (useless for future versions as this is included in the Create Table statement in 0.0.9)
$this->db->addColumn('product_class', 'defaults', "TEXT NULL  AFTER `front_template`");

$this->db->addColumn('attribute', 'image', "VARCHAR(255) NOT NULL  AFTER `name`");

//Plugins
$this->db->dropTable('plugin_file_modification');