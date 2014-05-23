<?php
$this->db->query("ALTER TABLE " . DB_PREFIX . "user_group RENAME TO  " . DB_PREFIX . "user_role");
$this->db->changeColumn('user_role', 'user_group_id', 'user_role_id', "INT(11) NOT NULL AUTO_INCREMENT");
$this->db->changeColumn('user_role', 'permission', 'permissions', "TEXT NOT NULL");

$this->db->changeColumn('user', 'user_group_id', 'user_role_id', "INT(11) NOT NULL");