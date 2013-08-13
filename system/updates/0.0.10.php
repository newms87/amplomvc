<?php
//Product Options
$this->db->addColumn('product_option_value', 'default', "TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `sort_order`"); 