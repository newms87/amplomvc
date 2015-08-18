<?php
$this->db->addColumn('customer', 'role_id', "INT(10) UNSIGNED NOT NULL");
$this->db->dropColumn('customer', 'customer_group_id');
$this->db->dropColumn('customer', 'ip');
