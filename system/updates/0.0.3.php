<?php

$this->db->changeColumn('return', 'date_ordered', '', 'DATETIME NOT NULL');
$this->db->dropColumn('return', 'product');
$this->db->dropColumn('return', 'model');

$this->db->addColumn('return', 'rma', "VARCHAR(45) NOT NULL AFTER `return_id`, ADD UNIQUE INDEX `RMA_UNIQUE` (`rma` ASC)");

$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "return_action`");
$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "return_reason`");
$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "return_status`");
