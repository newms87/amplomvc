<?
$this->db->dropColumn('order', 'payment_address_format');
$this->db->dropColumn('order', 'shipping_address_format');

$this->db->addColumn('extension', 'sort_order', "INT UNSIGNED NOT NULL DEFAULT 0  AFTER `code`");