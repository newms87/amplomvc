<?php
$this->db->changeColumn('customer', 'password', 'password', "VARCHAR(255) NOT NULL DEFAULT ''");
$this->db->changeColumn('user', 'password', 'password', "VARCHAR(255) NOT NULL DEFAULT ''");

//Add PASSWORD_COST definition into config file
$ac_config = SITE_DIR . 'ac_config.php';
$contents = file_get_contents($ac_config);

if (strpos($contents, 'PASSWORD_COST') === false) {
	$contents .= "\r\n\r\n//Password Hashing\r\ndefine(\"PASSWORD_COST\", 10);";

	file_put_contents($ac_config, $contents);
}
