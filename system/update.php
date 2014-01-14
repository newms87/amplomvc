<?php
class System_Update extends Model
{
	public function getVersions()
	{
		$version_list = $this->tool->get_files_r(DIR_SYSTEM . 'updates/', array('php'), FILELIST_STRING);

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
			$update_version = AC_VERSION;
		}

		$versions = $this->getVersions();

		foreach ($versions as $version => $file) {
			if (version_compare($update_version, $version, '>=')) {
				require_once($file);
			}
		}

		$this->config->save('system', 'ac_version', AC_VERSION, 0);
	}
}
