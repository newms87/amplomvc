<?php
class ModelDevDev extends Model 
{
	
	public function getBackupFiles()
	{
		$file_list = $this->tool->get_files_r(DIR_DATABASE_BACKUP, array('txt', 'sql'), FILELIST_STRING);
		
		$files = array();
		$sort_order = array();
		
		foreach ($file_list as $file) {
			$files[] = array(
				'name' => basename($file),
				'date' => filemtime($file),
				'path' => str_replace('\\', '/', $file),
				'size' => (int)filesize($file),
			);
			
			$sort_order[] = filemtime($file);
		}
		
		array_multisort($sort_order, SORT_DESC, $files);
		
		return $files;
	}
}