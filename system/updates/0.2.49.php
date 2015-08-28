<?php
$this->db->addColumn('log', 'user_id', "INT(10) UNSIGNED NOT NULL AFTER `log_id`");
$this->db->query("ALTER TABLE `ps`.`am_log` ADD INDEX `USERID` (`user_id` ASC)");
$this->db->query("ALTER TABLE `ps`.`am_log` ADD INDEX `NAME_USER` (`name` ASC, `user_id` ASC)");
