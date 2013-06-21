<?php
class PrettyLanguage extends Library
{
	/**
	 * *.tpl
	 * 
	 * Template output spacing conventions
	 * search: <\?=\s*([^\?]*);\s*\?>
	 * replace: <\?= $1; ?>
	 * search: <\?=\s*([^\?]*\?[^\?]*);\s*\?>
	 * repalce: <\?= $1; ?>
	 * 
	 * 
	 * *.php
	 * 
	 * Fixes class spacing convention
	 * search: class[ \t]+([^\s\{)*\s*\{
	 * replace: class $1 \R{
	 * 
	 * Fixes function spacing conventions
	 * search: ([ \t]*)([a-zA-Z0-9_]*[ \t]*)function\s*([a-zA-Z0-9_]*)\s*\(([^)]*)\)\s*\{
	 * replace: $1$2function $3($4)\R$1\{
	 * 
	 * Fixes if / elseif / foreach / while spacing convention
	 * search: (foreach|if|while)\s*\(([^\{\r\n]*)\)[ \t]*\{
	 * replace: $1 ($2) {
	 * 
	 * Fixes else and elseif spacing convention
	 * search: else(\s*)\{
	 * replace: else {
	 * search: \}[ \t]*else
	 * replace: } else
	 */
	function __construct()
	{
		$ignore_list = array(DIR_CACHE);
		$ext = array('php');
		
		
		$files = $this->get_all_files_r(SITE_DIR . 'catalog/controller/block/', $ext);
		
		//$this->smodel_call_update($files);
		
		//$this->class_name_convention($files);
		
		
		//$files = array(SITE_DIR . 'catalog/controller/checkout/checkout.php');
		
	// $this->update_message_format($files);
		
		//$this->remove_language_gets($files);
		
		//$this->update_url_links($files);
		
		//$this->update_breadcrumb_format($files);
		
		//$this->format_langauge_files($files);
		
		//$this->clean_php_line_endings($files);
		
		//$this->update_template_format($files);

	}
	
	public function smodel_call_update($files){
		foreach ($files as $file) {
			$lines = explode("\n", file_get_contents($file));
			
			$orig_lines = $lines;
			
			$count = 0;
			
			foreach ($lines as $num => $line) {
				$matches = null;
				if (preg_match("/\\\$this->(model_[a-z_]*)/", $line, $matches)) {
					$parts = explode('_', $matches[1]);
					$rename = ucfirst($parts[0]) . '_' . ucfirst($parts[1]) . '_' . ucfirst($parts[2]);
					
					if(isset($parts[3])){
						$rename .= ucfirst($parts[3]);
					}
					
					$lines[$num] = str_replace($matches[1], $rename, $line);
					
					$count++;
				}
			}
			
			if($count > 0){
				echo "modified $count lines in $file<br>";
			}
			file_put_contents($file, implode("\n", $lines));
		}
	}
	
	public function class_name_convention($files){
		foreach ($files as $file) {
			$lines = explode("\n", file_get_contents($file));
			
			$orig_lines = $lines;
			
			$dir_components = explode('/',str_replace(SITE_DIR, '', dirname($file)));
			
			if($dir_components[0] == 'plugin'){
				array_shift($dir_components);
				while (!in_array(current($dir_components), array("", "catalog", "admin", "system"))) {
					array_shift($dir_components);
				}
			}
			
			array_walk($dir_components, function(&$e, $index){
				 $e = ucfirst($e);
				 $e = preg_replace_callback("/_([a-z])/", function($matches){return strtoupper($matches[1]);}, $e);
			});
			
			$file_component = ucfirst(str_replace('.php','', basename($file)));
			$file_component = preg_replace_callback("/_([a-z])/", function($matches){return strtoupper($matches[1]);}, $file_component);
			
			$classname = implode('_', $dir_components) . '_' . $file_component;
			
			foreach ($lines as $num => $line) {
				$count = 0;
				
				$replace = "class $classname";
				$lines[$num] = preg_replace("/class [A-Z0-9_\\\\\/]*/i", "class {$classname}", $line, 1, $count);
				
				if($count){
					echo "&lt;&lt;&lt;$line<br />&gt;&gt;&gt;$lines[$num]<br>";
					break;
				 }
			}
			
			file_put_contents($file, implode("\n", $lines));
		}
	}
	
	public function update_template_format($files)
	{
		foreach ($files as $file) {
			$lines = explode("\n", file_get_contents($file));
			
			$orig_lines = $lines;
			
			$brackets = array();
			$to_remove = array();
			$to_add = array();
			
			echo $file . '<br>';
			
			$dir_temp = -1;
			
			foreach ($lines as $num=>$line) {
				for($i=0;$i<strlen($line);$i++){
					if($line[$i] == '{')
						array_push($brackets, $num);
					elseif($line[$i] == '}')
						array_pop($brackets);
				}
				
				if (strpos($line, "DIR_THEME") !== false) {
					$dir_temp = 0;
				}
				
				switch($dir_temp){
					case 0:
						if(!preg_match('/if\s*\(file_exists\(DIR_THEME\s*\.\s*\$this->config->get\([^\)]*\)\s*\.\s*\'[^\']*\'\)\)\s*\{/', $line)){
							echo "PROBLEM at 0";exit;
						}
						$lines[$num] .= "**REMOVE ME**";
						break;
					case 1:
						if (!preg_match('/\$this->template_file\s*=\s*\$this->config->get\(\'config_theme\'\)\s*\.\s*\'[^\']*\';/', $line)) {
							echo "PROBLEM at 1"; exit;
						}
						$lines[$num] .= "**REMOVE ME**";
						break;
					case 3:
						array_pop($brackets);
						break;
					case 2:
						if(!preg_match('/\}\s*else\s*\{/', $line)){
							echo "PROBLEM at 2"; exit;
						}
						$lines[$num] .= "**REMOVE ME**";
						break;
					case 4:
						if (trim($line) != '}') {
							echo "PROBLEM at 4"; exit;
						}
						$lines[$num] .= "**REMOVE ME**";
						break;
					case 5:
						if (!trim($line)) {
							$lines[$num] .= "**REMOVE ME**";
						}
						$dir_temp = -1;
					default:
						break;
				}

				if ($dir_temp >=0 && $dir_temp != 3) {
					$dir_temp++;continue;
				}
				
				if (preg_match('/\$this->template_file/', $line)) {
					if (substr_count($line,'=') > 1) {
						echo "WEIRD TEMPLATE LINE!";
						exit;
					}
					$template = trim(preg_replace(array('/\.tpl/','/;/','/[^=]*=/'), '', $line));
					
					if ($dir_temp == 3) {
						if (!preg_match('/default\/template\//',$template)) {
							echo "PROBLEM PREG at 3"; exit;
						}
						$template = str_replace("default/template/",'', $template);
						$dir_temp++;
					}
					
					$lines[$num] .= "**REMOVE ME**";
					
					$to_add[end($brackets)] = '$this->template->load(' . $template . ');' . "\r\n";
				}
				elseif ($dir_temp == 3) {
					$this->pl($num, $line);
					echo "PROBLEM at 3";exit;
				}
			}
			
			$new_lines = array();
			$new_orig_lines = array();
			foreach ($lines as $num=>$l) {
				if (!preg_match('/\*\*REMOVE ME\*\*/', $l)) {
					$new_lines[] = $l;
				}
				else {
					$new_lines[] = null;
				}
				$new_orig_lines[] = $l;
				if (in_array($num, array_keys($to_add))) {
					$new_lines[] = $to_add[$num];
					$new_orig_lines[] = '';
				}
			}
			
			//$this->print_lines($new_orig_lines, $new_lines, true);
			file_put_contents($file, implode("\n", $new_lines));
		}
	}
	
	
	public function clean_php_line_endings($files)
	{
		foreach ($files as $file) {
			$lines = explode("\n", file_get_contents($file));
			
			$orig_lines = $lines;
			
			for($i=count($lines)-1; $i > max(0,count($lines)-10); $i--){
				if (preg_match("/[^\s]/", $lines[$i]) > 0) {
					//echo "checking " . htmlspecialchars($lines[$i]) . '<br>';
					if (preg_match("/\?>\s*$/", $lines[$i])) {
						unset($lines[$i]);
					}
					else {
						//echo 'skipping ' . htmlspecialchars($lines[$i]);
					}
					break;
				}
			}
			//$this->print_lines($orig_lines, $lines, true);
			file_put_contents($file, implode("\n", $lines));
		}
	}
	
	public function format_language_files($files)
	{
		echo 'Formatting Language Files';
		
		$files = array(SITE_DIR . 'catalog/language/english/product/test.php');
		foreach ($files as $file) {
			$lines = explode("\n", file_get_contents($file));
			$new_lines = array();
			$max = 0;
			foreach ($lines as $line) {
				if(preg_match('/\s*\$[^=]*=/', $line) == 0) continue;
				
				echo 'parsing line ' .$line  . '<br>';
				list($entry, $text) = explode('=', $line, 2);
				html_dump($entry);
				html_dump($text);
				$entry = str_replace(' ', '', $entry);
				$text = trim($text);
				$max = max($max, strlen($entry));
				$new_lines[] = array('e'=>$entry, 't' => $text);
			}
			
			foreach ($new_lines as &$line) {
				$line = str_pad($line['e'], $max) . ' = ' . $line['t'];
			}
			file_put_contents($file, implode("\n", $new_lines));
		}
	}
	
	public function update_breadcrumb_format($files)
	{
		foreach ($files as $file) {
			$lines = explode("\n", file_get_contents($file));
			
			$flag_delete = false;
			$orig_lines = $lines;
			
			$tricky = false;
			
			$block_started = false;
			
			$brace_count = 0;
			$r_href = $r_text = null;
			
			foreach ($lines as $num=>&$line) {
				if (preg_match("/[^\s]/", $line) == 0) {
					if($block_started)
						unset($lines[$num]);
					continue;
				}

				if (!$block_started) {
					if (strpos($line, '=')) {
						list($var, $assign) = explode('=', $line, 2);
						
						if(preg_match('/\$this->data\[[\'"]breadcrumbs[\'"]\]/', $var) == 0) continue;
						
						if (preg_match('/\$this->data\[[\'"]breadcrumbs[\'"]\]\[\]/', $var) > 0) {
							echo "Tricky File $file at line $num<br><br>";
							$tricky = true;
							break;
						}
						
						if(preg_match('/\$this->tool->breadcrumbs/', $assign) > 0) continue;
						
						if (trim($assign) == 'array();') {
							unset($lines[$num]);
							$block_started = true;
						}
						else {
							echo "Unusual Assign Start of block: $file at line $num<br><br>";
							$tricky = true;
							break;
						}
					}

					continue;
				}
				else {
					if (strpos($line, '=')) {

						list($var, $assign) = explode('=', $line, 2);
						if ($brace_count == 0) {
							if (preg_match('/\$this->data\[[\'"]breadcrumbs[\'"]\]\[\]/', $var) == 0) {
								$line = "\r\n" . $line;
								$block_started = false;
								continue;
							}
						
							if (trim($assign) == 'array(') {
								$brace_count++;
								unset($lines[$num]);
							}
							else {
								echo "Unusual Assign in block: $file at line $num<br><br>";
								$tricky = true;
								break;
							}
						}
						else {
							$var = trim($var, " \t\n\r\0\x0B'");
							$assign = trim($assign, " \t\n\r\0\x0B,>");
							if ($var == 'text') {
								if ($r_text) {
									echo "OVERWRITING TEXT? $file line $num<br><br>";
									break;
								}
								$r_text = $assign;
							}
							elseif ($var == 'href') {
								if ($r_href) {
									echo "OVERWRITING HREF? $file line $num<br><br>";
									break;
								}
								$r_href = $assign;
							}
							elseif ($var != 'separator') {
								echo "Unusual attribute $file at line $num<br><br>";
								$tricky = true;
								break;
							}
							
							if ($r_text && $r_href) {
								$pre = null;
								preg_match("/^\s*/",$line,$pre);
								$lines[$num] = $pre[0] . '$this->breadcrumb->add(' . $r_text . ', ' . $r_href . ');';
								$r_text = null;
								$r_href = null;
							}
							else {
								unset($lines[$num]);
							}
						}
					}
					else {
						if (trim($line) == ');') {
							$brace_count--;
							unset($lines[$num]);
						}
						else {
							$line = "\r\n" . $line;
							$block_started = false;
							continue;
						}
					}
				}
			}
			
			if (!$tricky) {
				file_put_contents($file, implode("\n", $lines));
			}
			else {
				$this->print_lines($orig_lines, $lines, true);
			}
		}
	}
	
	public function update_message_format($files)
	{
		foreach ($files as $file) {
			$lines = explode("\n", file_get_contents($file));
			
			$flag_delete = false;
			$orig_lines = $lines;
			
			foreach ($lines as $num=>&$line) {
				if (preg_match("/[^\s]/", $line) == 0) {
					continue;
				}
				
				$msg_types = array('warning', 'success', 'notify', 'attention');
				
				foreach ($msg_types as $mt) {
					$r = preg_replace("/\\\$this->session->data\[['\"]" . $mt . "[\"']\]\s*=/",'$this->message->add(\'' . $mt . '\',', $line);
					if ($r) {
						if ($r != $line) {
							$pos = strrpos($r, ';');
							if ($pos !== false) {
								$r = substr_replace($r, ');', $pos, 1);
							}
							else {
								echo 'NO ENDING DELIMETER';
								exit;
							}
							$line = $r;
						}
					}
					else {
						echo "ERROR PREG";
						exit;
					}
				}
			}
			$this->print_lines($orig_lines, $lines, true);
			file_put_contents($file, implode("\n", $lines));
		}
	}
	
	public function update_url_links($files)
	{
		foreach ($files as $file) {
			$lines = explode("\n", file_get_contents($file));
			foreach ($lines as &$line) {
				if (strpos($line, '$this->url->link') !== false) {
					$patterns = array(
						"/['\"]token=['\"]\s*\.\s*\\\$this->session->data\[['\"]token['\"]\][^a-zA-Z0-9]*,/",
						"/['\"]token=['\"]\s*\.\s*\\\$this->session->data\[['\"]token['\"]\]\s*\.\s*\\\$/",
						"/['\"]token=['\"]\s*\.\s*\\\$this->session->data\[['\"]token['\"]\]\s*\.\s*['\"]&/",
						"/['\"]token=['\"]\s*\.\s*\\\$token[^a-zA-Z0-9]*,/",
						"/['\"]token=['\"]\s*\.\s*\\\$token\s*\.\s*\\\$/",
						"/['\"]token=['\"]\s*\.\s*\\\$token\s*\.\s*['\"]&/",
						"/,\s*['\"]SSL['\"]\s*/i",
						"/,\s*['\"]['\"]\s*\)/",
					);
					$replacements = array(
						'\'\',',
						'$',
						'\'',
						'\'\',',
						'$',
						'\'',
						'',
						')'
					);
					
					$orig = $line;
					$line = preg_replace($patterns,$replacements,$line);
					if ($orig != $line) {
						echo "Changed $orig<br>$line<br><br>";
					}
				}
			}
			
			file_put_contents($file, implode("\n", $lines));
		}
	}
	
	public function get_all_files_r($dir, $ignore=array(), $ext=array('php'), $depth=0){
		if ($depth > 20) {
			echo "we have too many recursions!";
			exit;
		}
		
		if(!is_dir($dir) || in_array($dir . '/', $ignore))return array();
		
		$handle = @opendir($dir);
		
		$files = array();
		while (($file = readdir($handle)) !== false) {
			if($file == '.' || $file == '..')continue;
			
			$file_path = rtrim($dir,'/') . '/' . $file;
			
			if (is_dir($file_path)) {
				$files = array_merge($files, $this->get_all_files_r($file_path, $ignore,$ext, $depth+1));
			}
			else {
				if (!empty($ext)) {
					$match = null;
					preg_match("/[^\.]*$/", $file, $match);
					
					if (!in_array($match[0], $ext)) {
						continue;
					}
				}
				$files[] = $file_path;
			}
		}
		
		return $files;
	}
	
	public function print_lines($orig, $lines, $changes_only = true, $special_chars = false)
	{
		foreach ($orig as $num=>$l) {
			if($special_chars){
				$l = preg_replace(array('/ /','/\t/'),array('&nbsp;','&nbsp;&nbsp;&nbsp;'), $l);
			}
			
			if (!isset($lines[$num])) {
				$color = '#F98888';
			}
			elseif ($l != $lines[$num]) {
				echo "<div style='background: #92ADE3'>" . ($num+1) . ".  "  . ($special_chars ? htmlspecialchars($l) : $l) . "</div>";
				$l = $lines[$num];
				$color = '#C2E782';
			}
			else {
				if($changes_only)continue;
				$color = '#CBCBCB';
			}
			echo "<div style='background: $color'>" . ($num+1) . ".  "  . ($special_chars ? htmlspecialchars($l) : $l) . "</div>";
		}
	}
	
	public function pl($num, $line)
	{
		$line = preg_replace(array('/ /','/\t/'),array('&nbsp;','&nbsp;&nbsp;&nbsp;'), $line);
		echo "$num. " . htmlspecialchars($line) . '<br>';
	}
}