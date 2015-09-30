<?php
$this->db->dropTable('user_meta');
$this->db->dropColumn('user_role', 'user_id');
