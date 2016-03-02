<?php
$this->db->addColumn('history', 'status', "varchar(45) DEFAULT '' AFTER `action`");
