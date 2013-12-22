<?php
class System_Cron_AcCron extends System_Cron_Job
{
	public function maintenance()
	{
		$this->backup();
		$this->cleanCache();
	}

	public function backup()
	{
		$backup_file = DIR_DATABASE_BACKUP . 'scheduled_backup-' . $this->date->format(null, 'y-m-d') . '.sql';

		$this->db->dump($backup_file);
	}

	public function cleanCache()
	{
		$files = glob(DIR_CACHE . '*.cache');

		$expired = _time() - CACHE_FILE_EXPIRATION;

		if ($files) {
			foreach ($files as $file) {
				if (_filemtime($file) < $expired) {
					//Suppress warnings as this will fail under race conditions
					@unlink($file);
				}
			}
		}
	}

	public function retryFailedTransactions()
	{
		$this->transaction->retryFailedTransactions();
	}
}
