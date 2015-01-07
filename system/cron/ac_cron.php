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

	public function cleanAddressTable()
	{
		$address_ids = $this->queryColumn("SELECT address_id FROM " . self::$tables['address']);

		foreach ($address_ids as $address_id) {
			if ($this->queryVar("SELECT COUNT(*) FROM " . self::$tables['customer_address'] . " WHERE address_id = " . (int)$address_id)) {
				continue;
			}

			if ($this->queryVar("SELECT COUNT(*) FROM " . self::$tables['transaction'] . " WHERE address_id = " . (int)$address_id)) {
				continue;
			}

			if ($this->queryVar("SELECT COUNT(*) FROM " . self::$tables['shipping'] . " WHERE address_id = " . (int)$address_id)) {
				continue;
			}

			//Remove unassociated address
			$this->delete('address', $address_id);
		}
	}

	public function updateCurrencies()
	{
		if (option('config_currency_auto')) {
			$this->Model_Localisation_Currency->updateCurrencies();
		}
	}
}
