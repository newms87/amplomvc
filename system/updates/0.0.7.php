<?php
//Remove Tables
$this->db->dropTable('tag_translation');
$this->db->dropTable('category_description');
$this->db->dropTable('attribute_description');
$this->db->dropTable('attribute_group_description');

//Options
$this->db->addColumn('option', 'name', "VARCHAR(45) NOT NULL AFTER `option_id`");
$this->db->addColumn('option', 'display_name', "VARCHAR(128) NOT NULL AFTER `name`");

if ($this->db->hasTable('option_description')) {
	$this->db->query("UPDATE " . DB_PREFIX . "option o SET name = (SELECT name FROM " . DB_PREFIX . "option_description od WHERE od.option_id=o.option_id AND od.language_id = " . $this->language->id() . ")");
	$this->db->query("UPDATE " . DB_PREFIX . "option o SET display_name = (SELECT display_name FROM " . DB_PREFIX . "option_description od WHERE od.option_id=o.option_id AND od.language_id = " . $this->language->id() . ")");

	$this->db->dropTable('option_description');
}

$this->db->addColumn('option_value', 'name', "VARCHAR(128) NOT NULL AFTER `option_id`");

if ($this->db->hasTable('option_value_description')) {
	$this->db->query("UPDATE " . DB_PREFIX . "option_value ov SET name = (SELECT name FROM " . DB_PREFIX . "option_value_description ovd WHERE ovd.option_value_id=ov.option_value_id AND ovd.language_id = " . $this->language->id() . ")");

	$this->db->dropTable('option_value_description');
}

//Product Option
$this->db->addColumn('product_option', 'name', "VARCHAR(45) NOT NULL  AFTER `option_id`");
$this->db->addColumn('product_option', 'display_name', "VARCHAR(128) NOT NULL  AFTER `name`");
$this->db->addColumn('product_option', 'type', "VARCHAR(45) NOT NULL  AFTER `option_value`");
$this->db->addColumn('product_option', 'group_type', "VARCHAR(45) NOT NULL  AFTER `type`");

//Product Option Value
$this->db->addColumn('product_option_value', 'name', "VARCHAR(45) NOT NULL  AFTER `option_value_id`");
$this->db->addColumn('product_option_value', 'image', "VARCHAR(255) NOT NULL  AFTER `name`");
$this->db->addColumn('product_option_value', 'sort_order', "INT NOT NULL DEFAULT 0 AFTER `option_restriction_id`");

//Product Option Restrictions
$this->db->changeColumn('product_option_value_restriction', 'option_value_id', 'product_option_value_id', "INT(10) UNSIGNED NOT NULL");
$this->db->changeColumn('product_option_value_restriction', 'restrict_option_value_id', 'restrict_product_option_value_id', "INT(10) UNSIGNED NOT NULL");
