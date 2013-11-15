<?php
//Options
$this->db->dropColumn("product_option_value", 'option_restriction_id');
$this->db->dropColumn("product_option_value", 'name');
$this->db->dropColumn('option_value', 'name');
