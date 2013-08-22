<?php
/**
 * Janrain RPX Social Login
 *
 * Version: 0.7
 * Title: Social Login / Registration by JanRain (RPX now)
 * Description: Allows customers to login and register in a couple clicks! No form filling required.
                Uses the Janrain API to access the Social Authentication by many different Social networks like Google, Facebook, Twitter, etc.
 * Author: Daniel Newman
 * Date: 3/15/2013
 * Link: http://www.amplocart.com/plugins/janrain
 *
 */

class Janrain_Setup extends PluginSetup
{
	public function install()
	{
		$table = DB_PREFIX . 'janrain';

		$query = <<<SQL
CREATE TABLE IF NOT EXISTS $table (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` int(11) NOT NULL,
	`email` varchar(255) NOT NULL,
	`provider` varchar(255) NOT NULL,
	`identifier` varchar(255) NOT NULL,
	`register_date` datetime NOT NULL,
	`lastvisit_date` datetime NOT NULL,
	PRIMARY KEY (`id`)
)
SQL;

		$this->db->query($query);
	}

	public function uninstall($keep_data = true)
	{
		if (!$keep_data) {
			$this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "janrain");
		}
	}
}