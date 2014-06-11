<?php
$this->db->changeColumn('coupon', 'code', 'code', "varchar(32) NOT NULL");
$this->db->addColumn('coupon', 'auto_apply', "TINYINT UNSIGNED NOT NULL AFTER `code`");
