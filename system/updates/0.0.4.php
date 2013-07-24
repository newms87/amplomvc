<?
$this->db->addColumn('product', 'information', 'TEXT NOT NULL AFTER `teaser`');

$this->db->query("UPDATE " . DB_PREFIX . "product SET information = description, description = teaser, teaser = ''");