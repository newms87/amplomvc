<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

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

		save_option('AMPLO_VERSION', AMPLO_VERSION);

		clear_cache();

		return true;
	}
}
