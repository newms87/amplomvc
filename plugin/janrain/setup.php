<?php
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