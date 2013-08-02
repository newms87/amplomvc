<?php
$this->db->changeColumn('option_value', 'name', 'value', "VARCHAR(128) NOT NULL");
$this->db->changeColumn('product_option_value', 'name', 'value', "VARCHAR(128) NOT NULL");