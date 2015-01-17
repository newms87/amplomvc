<?php

$this->db->dropColumn('customer', 'cart');
$this->db->dropColumn('customer', 'wishlist');
$this->db->dropColumn('customer', 'payment_code');
