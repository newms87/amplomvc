<?php
$this->db->addColumn('navigation', 'target', "VARCHAR(45) NOT NULL AFTER `condition`");

$this->update('language', Language::$defaults, array('code' => 'en'));
