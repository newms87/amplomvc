<?php
$this->db->addColumn('page', 'display_title', "TINYINT UNSIGNED NOT NULL DEFAULT 0  AFTER `status`");
$this->db->changeColumn('page', 'name', 'title', "VARCHAR(45) NOT NULL");