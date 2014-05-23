<?php
class App_Model_Dev_Dev extends Model
{

	public function getBackupFiles()
	{
		$exts = array(
			'txt',
			'sql'
		);

		$file_list = $this->tool->get_files_r(DIR_DATABASE_BACKUP, $exts, FILELIST_STRING);

		$files = array();

		foreach ($file_list as $file) {
			$files[] = array(
				'name' => basename($file),
				'date' => filemtime($file),
				'path' => str_replace('\\', '/', $file),
				'size' => (int)filesize($file),
			);
		}

		usort($files, function ($a, $b) { return $a['date'] > $b['date']; });

		return $files;
	}
}
