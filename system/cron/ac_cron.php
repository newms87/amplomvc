<?php
class System_Cron_AcCron extends CronJob
{
	public function backup()
	{
		$backup_file = DIR_DATABASE_BACKUP . 'scheduled_backup-' . $this->date->format(null, 'y-m-d') . '.sql';

		$this->db->dump($backup_file);
	}
}
