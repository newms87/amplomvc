<?php
$merge_file = DIR_MERGED_FILES . 'registry.txt';
if (!file_exists($merge_file)) {
	if (!is_dir(DIR_MERGED_FILES)) {
		mkdir(DIR_MERGED_FILES, DEFAULT_PLUGIN_DIR_MODE, true);
	}
	touch($merge_file);
	chmod($merge_file, DEFAULT_PLUGIN_FILE_MODE);
}

$merge_registry = array();

$entries = file_get_contents($merge_file);

if ($entries) {
	$entries = explode("\n", $entries);

	foreach ($entries as $entry) {
		if(!($entry = trim($entry)))continue;
		
		list($filename, $name, $mod_path) = explode(',',$entry);
		$merge_registry[SITE_DIR . $filename][$name] = $mod_path;
	}
}

function _require($file, $once = true, $_ = array()) {
	global $merge_registry;
	if (isset($merge_registry[$file])) {
		$count = 0;
		
		$file = str_replace(SITE_DIR, DIR_MERGED_FILES, $file, $count);
		
		if($count == 0){
			$file = DIR_MERGED_FILES . $file;
		}
	}
	
	if (!is_file($file)) {
		echo get_caller(0, 3);
	}
	
	if ($once) {
		require_once($file);
	} else {
		require($file);
	}
	
	return $_;
}