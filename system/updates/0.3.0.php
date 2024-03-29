<?php
$this->db->addColumn('log', 'user_id', "INT(10) UNSIGNED NOT NULL AFTER `log_id`");
$this->db->addColumn('log', 'domain', "VARCHAR(127) NOT NULL AFTER `name`");
$this->db->createIndex('log', 'USERID', array('user_id' => 'ASC'));
$this->db->createIndex('log', 'NAME_USER', array(
	'name'    => 'ASC',
	'user_id' => 'ASC'
));
