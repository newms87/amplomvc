<?
if ($this->db->addColumn('product', 'information', 'TEXT NOT NULL AFTER `teaser`')) {
	$this->db->query("UPDATE " . DB_PREFIX . "product SET information = description");
	$this->db->query("UPDATE " . DB_PREFIX . "product SET description = teaser");
	$this->db->query("UPDATE " . DB_PREFIX . "product SET teaser = ''");
}
