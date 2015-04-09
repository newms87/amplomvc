<?php
$this->db->addColumn('navigation', 'target', "VARCHAR(45) NOT NULL AFTER `condition`");
