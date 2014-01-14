<?php
$this->db->dropTable("affiliate");
$this->db->dropTable("affiliate_transaction");

$this->db->changeColumn("block", "name", "path", "VARCHAR(45) NOT NULL");
