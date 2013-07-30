<?php
//custom var dump
global $html_dump_count;
$html_dump_count = 0;
function html_dump($var, $label= "HTML Dump", $level=0, $max = -1, $print = true) {
	global $html_dump_count;
	
	$id = 'html_dump-' . $html_dump_count;
	
	if (!$print) {
		ob_start();
	}
?>

<? if (!$html_dump_count) { ?>
<style>
#html_dump_list{
	position:relative;
	background:#EEE;
	border-radius: 5px;
	box-shadow: 2px 2px 5px rgba(0,0,0,.3);
	overflow:auto;
	display:block;
	padding: 5px 10px;
	margin-bottom: 10px;
}
.html_dump{
	float:left;
	display:block;
	margin-bottom:15px;
	margin-left:20px
}
</style>
<div id="html_dump_list"></div>
<? } ?>

<a id="<?=$id;?>" class ='html_dump' onclick="open_html_dump('<?=$id;?>')">
	<style>
	.html_dump_label{cursor:pointer; color:blue; text-decoration:underline;}
	.dump_output{margin:15px;}
	.key_value_pair{position:relative; height:20px;overflow:visible;}
	.type_label{background: #EF99A8;}
	.key{word-wrap:break-word; max-width:200px;background: #82E182;padding:3px 5px}
	.value{background: #92ADE3;max-width:800px; word-wrap:break-word}
	</style>
	<span class='html_dump_label'><?=$label;?></span>
	<div class ='dump_output' id='<?=$id;?>-output' style='display:none'>
		<? $dump = html_dump_r($var,$level,$max);?>
	</div>
</a>

<? if ($html_dump_count == 0) { ?>
<script type='text/javascript'>//<!--
function open_html_dump(id) {
	var w = window.open(null, 'newwindow', 'resizable=1,scrollbars=1, width=800, height=800');
	document.getElementById(id + '-output').setAttribute('style','display:block');
	w.document.body.innerHTML = document.getElementById(id).innerHTML;
	document.getElementById(id + '-output').setAttribute('style','display:none');
}
document.getElementsByTagName('body')[0].appendChild(document.getElementById("html_dump_list"));
//--></script>
<? } ?>

<script type='text/javascript'>//<!--
document.getElementById("html_dump_list").appendChild(document.getElementById("<?= $id; ?>"));
//--></script>

<?
	$html_dump_count++;
	
	if (!$print) {
		return ob_get_clean();
	}
}

function html_dump_r($var, $level, $max) {
	if (is_array($var) || is_object($var)) {
		$left_offset = $level * 20 . "px";
		$type = is_array($var)?"Array":"Object";
		$type .= " (".count($var).")";
		echo "<table><tr><td class ='type_label' colspan='2'>$type</td></tr>";
		foreach($var as $key=>$v) {
			echo "<tr class ='key_value_pair'>";
			echo "<td valign='top' class='key'>[$key]</td>";
			
			if ((is_array($v) || is_object($v)) && !($max >= 0 && $level >= ($max-1))) {
				echo "<td class ='value'>";
				html_dump_r($v, $level+1, $max);
				echo "</td>";
			}
			else {
				if(is_array($v))
					$val = "Array (" . count($v) . ")";
				elseif(is_object($v))
					$val = "Object (" . count($v) . ")";
				elseif(is_bool($v))
					$val = "Bool (" . ($v?"true":"false") . ')';
				elseif(is_string($v) && empty($v) && $v !== '0')
					$val = "String (empty)";
				elseif(is_null($v))
					$val = "NULL";
				else
					$val = $v;
				
				echo "<td class ='value'>$val</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
	}
	else {
		htmlspecialchars(var_dump($var));
	}
}

function html_backtrace($depth=3, $var_depth = -1, $print = true) {
	return html_dump(debug_stack($depth, 1),'call stack', 0, $var_depth, $print);
}

function debug_stack($depth = 10, $offset = 0) {
	return array_slice(debug_backtrace(false), 1 + $offset, $depth);
}

if (!function_exists('array_column')) {
	/**
	 * PHP < 5.5 backwards Compatibility
	 *
	 * Returns an array of elements from the column of an array
	 *
	 * @param array array - An associative array of arrays
	 * @param column string - The key column of the $array to get elements for
	 * 
	 * @return array - an array of values of the column requested
	 */
	function array_column($array, $column)
	{
		$values = array();
		
		foreach ($array as $row) {
			$values[] = $row[$column];
		}
		
		return $values;
	}
}

if (!function_exists('array_search_key')) {
	/**
	 * Searches for an element in a multidimensional array for an element key that matches search_key and
	 * value that matches needle. It will return the parent array of this key => value pair.
	 *
	 * @param search_key mixed - Either a string or int to search by the array key 
	 * @param needle mixed - The searched value. If needle is a string, the comparison is done in a case-sensitive manner. 
	 * @param haystack array - The array. 
	 * @param strict bool[optional] - If the third parameter strict is set to true then the array_search function will search for identical elements in the haystack.
	 * This means it will also check the types of the needle in the haystack, and objects must be the same instance. 
	 * 
	 * @return mixed the key for needle if it is found in the array, false otherwise. 
	 */
	
	function array_search_key($search_key, $needle, $haystack, $strict = false){
		foreach ($haystack as $key => $value) {
			if (is_array($value)) {
				$result = array_search_key($search_key, $needle, $value, $strict);
				
				if (!is_null($result)) {
					return $result;
				}
			}
	 		
	 		if ($key === $search_key && $value === $needle) {
	 			return $haystack;
			}
		}
	}
}

if (!function_exists('html2text')) {
	/**
	 * Converts HTML break tags (eg: <br />) to new lines, and removes all other HTML tags
	 *
	 * @param string $html - The HTML to replace all the
	 *
	 * @return String of plain text
	 */
	function html2text($html)
	{
		return strip_tags(preg_replace("/<br\s*\/?>/", "\r\n", $html));
	}
}

function get_caller($offset = 0, $limit = 1) {
	$calls = debug_backtrace(false);
	
	$html = "";
	
	$limit += $offset;
	
	while ( $offset < $limit && $offset < count($calls) ) {	
		$caller = $calls[$offset + 1];
		
		if (isset($caller['file'])) {
			$msg = "Called from <b style=\"color:red\">$caller[file]</b> on line <b style=\"color:red\">$caller[line]</b>";
		}
		else {
			$msg = "Called from <b style=\"color:red\">$caller[class]::$caller[function]</b>";
		}
		
		$html = "<div style=\"margin-top:5px\"><b>&#187;</b> $msg</div>" . $html;
		
		$offset++;
	}
	
	return "<div style=\"margin-top: 8px; margin-bottom: 8px; margin-left: 15px\">$html</div>";
}

if (!defined("AMPLOCART_DIR_MODE")) {
	define("AMPLOCART_DIR_MODE", 0755);
}

if (!defined("AMPLOCART_FILE_MODE")) {
	define("AMPLOCART_FILE_MODE", 0755);
}

//TODO: do we allow different modes?
function _is_writable($dir, $mode = 0755) {
	if (!is_writable($dir)) {
		if (!is_dir($dir)) {
			mkdir($dir, AMPLOCART_DIR_MODE,true);
			chmod($dir, AMPLOCART_DIR_MODE);
		}
		
		if (!is_dir($dir)) {
			trigger_error("Do not have write permissions to create directory " . $dir . ". Please change the permissions to allow writing to this directory.");
			return false;
		}
		else {
			$t_file = $dir . uniqid('test') . '.txt';
			touch($t_file);
			if (!is_file($t_file)) {
				trigger_error("The write permissions on $dir are not set. Please change the permissions to allow writing to this directory");
				return false;
			}
			unlink($t_file);
		}
	}
	
	return true;
}

//prints to the console in javascript
function console($msg) {
	echo "<script>console.log('$msg');</script>";
}