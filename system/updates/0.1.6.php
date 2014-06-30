<?php
$this->db->addColumn('option_value', 'info', "TEXT NOT NULL AFTER `display_value`");
$this->db->addColumn('product_option_value', 'info', "TEXT NOT NULL AFTER `display_value`");
