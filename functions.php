<?php
//custom var dump
global $html_dump_count;
$html_dump_count = 0;
function html_dump($var, $label= "HTML Dump", $level=0, $max = -1, $print = true){
	global $html_dump_count;
   
   $id = 'html_dump-' . $html_dump_count;
	
	if(!$print){
		ob_start();
	}
?>
<a id='<?=$id;?>' class='html_dump' onclick="open_html_dump('<?=$id;?>')">
   <span class='html_dump_label'><?=$label;?></span>
     
<style>
.html_dump{background:white;overflow:auto;top:0;left:0;}
.html_dump_label{cursor:pointer; color:blue; text-decoration:underline;}
.dump_output{margin:15px;}
.key_value_pair{position:relative; height:20px;overflow:visible;}
.type_label{background: #EF99A8;}
.key{word-wrap:break-word; max-width:200px;background: #82E182;padding:3px 5px}
.value{background: #92ADE3;max-width:800px; word-wrap:break-word}
</style>

   <div class='dump_output' id='<?=$id;?>-output' style='display:none'>
      <? $dump = html_dump_r($var,$level,$max);?>
   </div>
</a><br/>
<?
   if($html_dump_count == 0){
?>
<script type='text/javascript'>//<!--
function open_html_dump(id){
   var w = window.open(null, 'newwindow', 'resizable=1,scrollbars=1, width=800, height=800');
   document.getElementById(id + '-output').setAttribute('style','display:block');
   w.document.body.innerHTML = document.getElementById(id).innerHTML;
   document.getElementById(id + '-output').setAttribute('style','display:none');
}
//--></script>
<?
   }
   $html_dump_count++;
	
	if(!$print){
		$dump = ob_get_clean();
		
		return $dump;
	}
}

function html_dump_r($var, $level, $max){
	if(is_array($var) || is_object($var)){
		$left_offset = $level * 20 . "px";
		$type = is_array($var)?"Array":"Object";
		$type .= " (".count($var).")";
		echo "<table><tr><td class='type_label' colspan='2'>$type</td></tr>";
		foreach($var as $key=>$v){
			echo "<tr class='key_value_pair'>";
			echo "<td valign='top' class='key'>[$key]</td>";
			
			if((is_array($v) || is_object($v)) && !($max >= 0 && $level >= ($max-1))){
				echo "<td class='value'>";
				html_dump_r($v, $level+1, $max);
				echo "</td>";
			}
			else{
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
              
				echo "<td class='value'>$val</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
	}
	else{
		htmlspecialchars(var_dump($var));
	}
}

function html_backtrace($depth=3, $var_depth = -1, $print = true){
	return html_dump(debug_stack($depth, 1),'call stack', 0, $var_depth, $print);
}

function debug_stack($depth = 10, $offset = 0){
	return array_slice(debug_backtrace(false), 1 + $offset, $depth);
}
 
function get_caller($offset = 0){
	$calls = debug_backtrace(false);
	$caller = $calls[$offset + 1];

	if(isset($caller['file'])){
		return "Called from $caller[file] on line $caller[line]";
	}
	else{
		return "Called from $caller[class]::$caller[function]";
	}
}

function make_test_dir($dir, $mode = '0755'){
	if(!is_dir($dir)){
      mkdir($dir, $mode,true);
      chmod($dir, $mode);
   }
	
	if(!is_dir($dir)){
		trigger_error("Do not have write permissions to create directory " . $dir . ". Please change the permissions to allow writing to this directory.");
		return false;
	}
	else{
		$t_file = $dir . uniqid('test') . '.txt';
		touch($t_file);
		if(!is_file($t_file)){
			trigger_error("The write permissions on $dir are not set. Please change the permissions to allow writing to this directory");
			return false;
		}
		unlink($t_file);
	}
	
	return true;
}

//prints to the console in javascript
function console($msg){
	echo "<script>console.log('$msg');</script>";
}