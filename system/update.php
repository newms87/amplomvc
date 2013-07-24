<?php
class System_Update {
	private $registry;
	
	public function __construct($registry)
	{
		$this->registry = $registry;
	}
	
	public function __get($key)
	{
		return $this->registry->get($key);
	}
	
	public function getVersions()
	{
		$version_list = $this->tool->get_files_r(DIR_SYSTEM . 'updates/', array('php'), FILELIST_STRING);
		
		$versions = array();
		
		foreach ($version_list as $version) {
			$v = str_replace('.php','',basename($version));
			$versions[$v] = $version;
		}
		
		return $versions;
	}
	
	/*
	 * Update the database and run any necessary configurations based
	 * on the current file version of the system
	 */
	public function update($update_version = null)
	{
		if (!$update_version) {
			$update_version = VERSION;
		}
		
		$versions = $this->getVersions();
		
		foreach ($versions as $version => $file) {
			if ($update_version >= $version) {
				require_once($file);
			}
		}
		
		$this->config->save('system', 'ac_version', VERSION, 0);
	}
}