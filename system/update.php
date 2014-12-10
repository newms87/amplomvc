<?php
class System_Update extends Model
{
	public function getVersions()
	{
		$version_list = get_files(DIR_SYSTEM . 'updates/', array('php'), FILELIST_STRING);

		natsort($version_list);

		$versions = array();

		foreach ($version_list as $version) {
			$v            = str_replace('.php', '', basename($version));
			$versions[$v] = $version;
		}

		return $versions;
	}

	/*
	 * Update the database and run any necessary configurations based
	 * on the current file version of the system
	 */
	public function updateSystem($update_version = null)
	{
		if (!$update_version) {
			$update_version = AMPLO_VERSION;
		}

		$versions = $this->getVersions();

		foreach ($versions as $version => $file) {
			if (version_compare($update_version, $version, '>=')) {
				require_once($file);
			}
		}

		$this->config->save('system', 'AMPLO_VERSION', AMPLO_VERSION, 0);

		return true;
	}
}
