<?php
class Tool extends Library
{
	public function __construct()
	{
		parent::__construct();

		define("FILELIST_STRING", 1);
		define("FILELIST_SPLFILEINFO", 2);
	}

	public function getSlug($name, $allow = '')
	{
		$patterns = array(
			"/[\s\\\\\/]/" => '_',
			"/[^a-z0-9_$allow]/" => '',
		);

		return preg_replace(array_keys($patterns), array_values($patterns), strtolower(trim($name)));
	}

	public function _2CamelCase($str, $lower = false)
	{
		$parts = explode('_', $str);

		//capitalize each component of the class name
		array_walk($parts, function (&$e) { $e = ucfirst($e); });

		$str = implode('', $parts);

		return $lower ? lcfirst($str) : $str;
	}

	public function camelCase2_($str)
	{
		$matches = null;
		preg_match_all("/([A-Z][a-z0-9]*)/", ucfirst($str), $matches);

		return strtolower(implode("_", $matches[1]));
	}

	public function name_format($format, $data)
	{
		$formatted_data = array();

		foreach ($data as $name => $d) {
			$formatted_data[preg_replace("/%name%/", $name, $format)] = $d;
		}

		return $formatted_data;
	}

	public function insertables($insertables, $text, $start = '%', $end = '%')
	{
		$patterns     = array();
		$replacements = array();

		foreach ($insertables as $key => $value) {
			$patterns[]     = "/$start" . $key . "$end/";
			$replacements[] = $value;
		}

		return preg_replace($patterns, $replacements, $text);
	}

	public function sort_by_array($array, $order, $sort_key)
	{
		$new_array = array();
		foreach ($order as $o) {
			foreach ($array as $a) {
				if ($a[$sort_key] == $o) {
					$new_array[] = $a;
				}
			}
		}
		return $new_array;
	}

	public function cleanTitle($text)
	{
		return strip_tags(preg_replace("/<br\\s*\/?>/", ' ', $text));
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
				if ((strlen($short) + strlen($word) + 1) > $num) {
					$short .= $append;
					break;
				}
				$short .= empty($short) ? $word : ' ' . $word;
			}
		} else {
			if (strlen($string) > $num) {
				$short = substr($string, 0, $num) . $append;
			} else {
				$short = $string;
			}
		}

		return $short;
	}

	public function bytes2str($size, $decimals = 2, $unit = null)
	{
		$unit_sizes = array(
			'TB' => 1024 * 1024 * 1024 * 1024,
			'GB' => 1024 * 1024 * 1024,
			'MB' => 1024 * 1024,
			'KB' => 1024,
			'B'  => 1,
		);

		if ($unit && isset($unit_sizes[$unit])) {
			$divisor = $unit_sizes[$unit];
		} else {
			foreach ($unit_sizes as $key => $unit_size) {
				if ($size > $unit_size) {
					$divisor = $unit_size;
					$unit    = $key;
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
			foreach ($child->attributes() as $attr => $value) {
				if ($attr == 'href') {
					$the_link = $value;
				}
			}
			$return["$parent"][] = $this->parse_xml_to_array($child) ? $this->parse_xml_to_array($child) : ($the_link ? "$the_link" : "$child");
		}
		return $return;
	}

	/**
	 * Parses PHPDoc comments for Directives in the form Directive: String information
	 *
	 * @param string $file - The File to get the comment Directives from.
	 *
	 * @return array - An associative array with key as the Comment Directive, and value of the String following the ':'
	 */
	public function getFileCommentDirectives($file, $trim = true)
	{
		$directives = array();

		if (is_file($file)) {
			$tokens = token_get_all(file_get_contents($file));

			foreach ($tokens as $token) {
				if ($token[0] === T_DOC_COMMENT) {
					if (preg_match_all("/(.*?)([a-z0-9_]*?):(.*?)\\*/is", $token[1], $matches)) {
						$directives = array_change_key_case(array_combine($matches[2], $matches[3]));
					}
				}
			}
		}

		if ($trim) {
			array_walk($directives, function(&$a){$a = trim($a);});
		}

		return $directives;
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
	public function get_files_r($dir, $exts = null, $return_type = FILELIST_SPLFILEINFO)
	{
		if (is_null($exts)) {
			$exts = array(
				'php',
				'tpl',
				'css',
				'js'
			);
		}

		if (!is_dir($dir)) {
			return array();
		}

		$dir_iterator = new RecursiveDirectoryIterator($dir);
		$iterator     = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::CHILD_FIRST);

		$files = array();

		foreach ($iterator as $file) {
			if ($file->isFile() && (!$exts || in_array($file->getExtension(), $exts))) {
				switch ($return_type) {
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
