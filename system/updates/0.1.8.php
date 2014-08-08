<?php
$this->db->dropColumn('product', 'product_class_id');
$this->db->dropTable('product_class');
