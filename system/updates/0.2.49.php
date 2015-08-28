<?php
$this->db->addColumn('log', 'user_id', "INT(10) UNSIGNED NOT NULL AFTER `log_id`");
