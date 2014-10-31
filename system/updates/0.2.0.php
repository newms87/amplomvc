<?php
$this->db->createTable('history', <<<SQL
  `history_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `table` VARCHAR(63) NOT NULL,
  `message` VARCHAR(127) NULL,
  `data` TEXT NULL,
  `date` DATETIME NOT NULL,
  PRIMARY KEY (`history_id`)
SQL
);
