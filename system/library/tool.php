<?php
class Tool extends Library
{
	public function __construct($registry)
	{
		parent::__construct($registry);
		
		define("FILELIST_STRING", 1);
		define("FILELIST_SPLFILEINFO",2);
	}
	
	public function error_set()
	{
		if (isset($this->session->data['warning']) && $this->session->data['warning']) {
			return true;
		}
		
		return false;
	}
	
	public function get_slug($name)
	{
		$slug = preg_replace("/\s/",'_', strtolower(trim($name)));
		$slug = preg_replace("/[^a-z0-9_]/", '', $slug);
		
		return $slug;
	}
	
	public function format_classname($component)
	{
		$parts = explode('_', $component);
				
		//capitalize each component of the class name
		array_walk($parts, function(&$e, $i){ $e = ucfirst($e); });
		
		return implode('', $parts);
	}
	
	public function name_format($format, $data)
	{
		$formatted_data = array();
		
		foreach ($data as $name => $d) {
			$formatted_data[preg_replace("/%name%/",$name, $format)] = $d;
		}
		
		return $formatted_data;
	}
	
	public function error_info()
	{
		return "<span style='font-weight:bold; color:#E72727'>" . get_caller(2). " </span>";
	}
	
	public function insertables($insertables, $text, $start = '%', $end = '%')
	{
		$patterns = array();
		$replacements = array();
		
		foreach ($insertables as $key => $value) {
			$patterns[] = "/$start" . $key . "$end/";
			$replacements[] = $value;
		}
		
		return preg_replace($patterns, $replacements, $text);
	}
	
	public function format_invoice($d)
	{
		$date_format = array();
		return preg_match("/%.*%/",$d,$date_format)?preg_replace("/%.*%/",date(preg_replace("/%/",'',$date_format[0])), $d):$d;
	}
	
	function sort_by_array($array,$order, $sort_key)
	{
		$new_array = array();
		foreach($order as $o)
			foreach($array as $a)
				if($a[$sort_key] == $o)
					$new_array[] = $a;
		return $new_array;
	}
	
	
	/**
	* limits the number of characters in a string to the nearest word or character
	*/
	public function limit_characters($string, $num, $append = '...', $keep_word = true)
	{
		if ($keep_word) {
			$words = explode(' ', $string);
			$short = '';
			foreach ($words as $word) {
				if ((strlen($short) + strlen($word)+1) > $num) {
					$short .= $append;
					break;
				}
				$short .= empty($short)?$word:' '.$word;
			}
		}
		else {
			if (strlen($string) > $num) {
				$short = substr($string, 0, $num) . $append;
			}
			else {
				$short = $string;
			}
		}
		
		return $short;
	}
	
	public function add_template_row(&$data)
	{
		if (empty($data)) {
			$data['__row__'] = array();
		} else {
			$data['__row__'] = reset($data);
		}
		
		if (is_array($data['__row__'])) {
			array_walk_recursive($data['__row__'], function(&$value, $key) { $value = $key; });
		} else {
			$data['__row__'] = '';
		}
	}
		
	public function bytes2str($size, $decimals = 2, $unit = null)
	{
		$unit_sizes = array(
			'TB' => 1024*1024*1024*1024,
			'GB' => 1024*1024*1024,
			'MB' => 1024*1024,
			'KB' => 1024,
			'B' => 1,
		);
		
		if ($unit && isset($unit_sizes[$unit])) {
			$divisor = $unit_sizes[$unit];
		}
		else {
			foreach ($unit_sizes as $key => $unit_size) {
				if ($size > $unit_size) {
					$divisor = $unit_size;
					$unit = $key;
					break;
				}
			}
		}
		
		if ($unit == 'B') {
			$decimals = 0;
		}
				
		return sprintf("%." . $decimals . "f $unit", ($size / $divisor));
	}
	
	public function parse_xml_to_array($xml)
	{
		$return = array();
		foreach ($xml->children() as $parent => $child) {
			$the_link = false;
			foreach($child->attributes() as $attr=>$value)
				if($attr == 'href')
					$the_link = $value;
				$return["$parent"][] = $this->parse_xml_to_array($child)?$this->parse_xml_to_array($child):($the_link?"$the_link":"$child");
		}
		return $return;
	}
	
	
	/**
	* Retrieves files in a specified directory recursively
	*
	* @param $dir - the directory to recursively search for files
	* @param $exts - the file extensions to search for. Use false to include all file extensions.
	* @param $return_type - can by FILELIST_STRING (for a string) or FILELIST_SPLFILEINFO (for an SPLFileInfo Object)
	*
	* @return array - Each value in the array will be determined by the $return_type param.
	*/
	function get_files_r($dir, $exts = array('php','tpl','css','js','to'), $return_type = FILELIST_SPLFILEINFO)
	{
		if(!is_dir($dir)) return array();
		
		$dir_iterator = new RecursiveDirectoryIterator($dir);
		$iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::CHILD_FIRST);
		
		$files = array();
		
		foreach ($iterator as $file) {
			if ($file->isFile() && (!$exts || in_array($file->getExtension(), $exts))) {
				switch($return_type){
					case FILELIST_STRING:
						$files[] = $file->getPathName();
						break;
					case FILELIST_SPLFILEINFO:
						$files[] = $file;
						break;
					default:
						trigger_error(__FUNCTION__ . ": invalid return type requested! Options are FILELIST_SPLFILEINFO or FILELIST_STRING. SplFileInfo type was returned.");
						$files[] = $file;
						break;
				}
			}
		}
		
		return $files;
	}

}