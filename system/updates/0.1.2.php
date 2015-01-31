<?php
if ($this->db->hasTable('user_group')) {
	$this->db->query("ALTER TABLE {$this->t['user_group']} RENAME TO  {$this->t['user_role']}");
}
$this->db->changeColumn('user_role', 'user_group_id', 'user_role_id', "INT(11) NOT NULL AUTO_INCREMENT");
$this->db->changeColumn('user_role', 'permission', 'permissions', "TEXT NOT NULL");

$this->db->changeColumn('user', 'user_group_id', 'user_role_id', "INT(11) NOT NULL");

$this->db->changeColumn('user', 'username', 'username', "VARCHAR(128) NOT NULL DEFAULT ''");
$this->db->changeColumn('user', 'first_name', 'first_name', "VARCHAR(45) NOT NULL DEFAULT ''");
$this->db->changeColumn('user', 'last_name', 'last_name', "VARCHAR(45) NOT NULL DEFAULT ''");

$this->db->dropTable('db_rule');
