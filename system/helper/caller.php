<?php
function call($path, $params = null)
{
	$args = func_get_args();
	array_shift($args);

	$action = new Action($path, $args);

	if ($action->execute()) {
		return $action->getOutput();
	} else {
		trigger_error('Could not load controller path ' . $path . '.');
	}
}

function block($block, $instance_name = null, $settings = null)
{
	global $registry;
	return $registry->get('block')->render($block, $instance_name, $settings);
}

function area($area)
{
	global $registry;
	return $registry->get('area')->render($area);
}

function show_area($area)
{
	global $registry;
	return $registry->get('area')->hasBlocks($area);
}

function links($group)
{
	global $registry;
	return $registry->get('document')->renderLinks($group);
}

function has_links($group)
{
	global $registry;
	return $registry->get('document')->hasLinks($group);
}

function breadcrumb($name, $url, $separator = '', $position = null)
{
	global $registry;
	$registry->get('breadcrumb')->add($name, $url, $separator, $position);
}

function breadcrumbs()
{
	global $registry;
	return $registry->get('breadcrumb')->render();
}

function cache($key, $value = null)
{
	global $registry;

	if (is_null($value)) {
		return $registry->get('cache')->get($key);
	} else {
		return $registry->get('cache')->set($key, $value);
	}
}

function message($type, $message)
{
	global $registry;
	$registry->get('message')->add($type, $message);
}

function image($image, $width = null, $height = null, $default = null)
{
	global $registry;
	$image = $registry->get('image')->resize($image, $width, $height);

	if (!$image && $default) {
		if ($default === true) {
			return theme_image('no_image.png', $width, $height);
		}

		return $registry->get('image')->resize($default, $width, $height);
	}

	return $image;
}

function theme_image($image, $width = null, $height = null)
{
	return image(theme_dir('image/' . $image), $width, $height);
}

function site_url($path = '', $query = null)
{
	global $registry;
	return $registry->get('url')->link($path, $query);
}

function store_url($store_id, $path = '', $query = null)
{
	global $registry;
	return $registry->get('url')->store($store_id, $path, $query);
}

function theme_url($path = '', $query = null)
{
	global $registry;

	$url = $registry->get('theme')->getUrl($path);

	if (!$url) {
		$url = URL_THEME . $path;
	}

	if ($query) {
		$url .= (strpos($url, '?') ? '&' : '?') . $query;
	}

	return $url;
}

function theme_dir($path = '')
{
	global $registry;
	return $registry->get('theme')->getFile($path);
}

function redirect($path = '', $query = null, $status = null)
{
	global $registry;
	$registry->get('url')->redirect($path, $query, $status);
}

function slug($name, $sep = '_', $allow = '')
{
	$patterns = array(
		"/[\\s\\\\\\/]/"      => $sep,
		"/[^a-z0-9_-$allow]/" => '',
	);

	return preg_replace(array_keys($patterns), array_values($patterns), strtolower(trim($name)));
}

define("IS_POST", $_SERVER['REQUEST_METHOD'] === 'POST');
define("IS_GET", $_SERVER['REQUEST_METHOD'] === 'GET');
define("IS_AJAX", !empty($_GET['ajax']));

function _get($key, $default = null)
{
	return isset($_GET[$key]) ? $_GET[$key] : $default;
}

function _post($key, $default = null)
{
	return isset($_POST[$key]) ? $_POST[$key] : $default;
}

function _request($key, $default = null)
{
	return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
}

function _session($key, $default = null)
{
	return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
}

function option($option, $default = null)
{
	global $registry;
	$value = $registry->get('config')->get($option);

	return is_null($value) ? $default : $value;
}

function set_option($option, $value)
{
	global $registry;
	$registry->get('config')->save('config', $option, $value);
}

function user_can($level, $path)
{
	global $registry;
	return $registry->get('user')->can($level, $path);
}

function validate($method, $value)
{
	global $registry;

	$args = func_get_args();
	array_shift($args);

	return call_user_func_array(array(
		$registry->get('validation'),
		$method
	), $args);
}

function format($type, $data, $param = null)
{
	global $registry;

	$args = func_get_args();
	array_shift($args);

	if (!is_string($type) && is_callable($type)) {
		return call_user_func_array($type, $args);
	}

	return call_user_func_array(array(
		$registry->get($type),
		'format'
	), $args);
}

function format_all($type, &$array, $index = null, $key = 'formatted')
{
	array_walk($array, function (&$a, $i) use ($type, $index, $key) {
		$a[$key] = $index ? format($type, $a[$index], $i) : format($type, $a, $i);
	});

	return $array;
}

function charlimit($string, $limit, $append = '...', $keep_word = true)
{
	if ($keep_word) {
		$words = explode(' ', $string);
		$short = '';
		foreach ($words as $word) {
			if ((strlen($short) + strlen($word) + 1) > $limit) {
				$short .= $append;
				break;
			}
			$short .= empty($short) ? $word : ' ' . $word;
		}
	} else {
		if (strlen($string) > $limit) {
			$short = substr($string, 0, $limit) . $append;
		} else {
			$short = $string;
		}
	}

	return $short;
}

function attrs($data)
{
	$html = '';

	foreach ($data as $key => $value) {
		if (strpos($key, '#') === 0) {
			$html .= ($html ? ' ' : '') . substr($key, 1) . "=\"$value\"";
		}
	}

	return $html;
}

function build($type, $params)
{
	$params += array(
		'name'     => '',
		'data'     => null,
		'select'   => array(),
		'key'      => null,
		'value'    => null,
		'readonly' => false,
	);

	if (!is_array($params['data'])) {
		trigger_error(_l("The 'data' parameter must be an array for %s", __METHOD__));
		return;
	}

	$data        = $params['data'];
	$name        = $params['name'];
	$select      = $params['select'];
	$build_key   = $params['key'];
	$build_value = $params['value'];
	$readonly    = $params['readonly'];

	if (!isset($params['#class'])) {
		$params['#class'] = "builder-$type";
	} else {
		$params['#class'] .= " builder-$type";
	}

	if ($readonly) {
		$params['#class'] .= ' read-only';
	}

	//Type Specific groupings
	$text_types = array(
		'text',
		'int',
		'float',
		'decimal',
		'date',
		'time',
		'datetime',
	);

	$date_types = array(
		'date',
		'time',
		'datetime',
	);

	//Add Date / Time picker
	if (in_array($type, $date_types)) {
		if ($readonly) {
			$select = format('date', $select, $type . '_format_short');
		} else {
			$params['#class'] .= ' ' . $type . 'picker';
		}
	}

	$attrs = attrs($params);

	if (in_array($type, $text_types)) {
		if ($readonly) {
			return "<span $attrs>$select</span>";
		} else {
			return "<input type=\"text\" $attrs name=\"$name\" value=\"$select\" />";
		}
	}

	//This is for select option groups
	$opt_group_active = false;

	if (!is_array($select)) {
		$select = array($select);
	}

	$options          = '';
	$selected_options = ''; //for clickable list

	foreach ($data as $key => $value) {
		if (is_array($value)) {
			if (($build_key && !isset($value[$build_key])) || ($build_value && !isset($value[$build_value]))) {
				trigger_error(_l("The associative indexes for 'key' and 'value' were not found in the data array."));
				return;
			}

			if ($build_key) {
				$key = $value[$build_key];
			}

			$value = isset($value[$build_value]) ? $value[$build_value] : '';
		}

		//Determine if the value is a selected value.
		//If the user specified the type of vars, use that type.,
		//otherwise try to guess the type.
		$selected = false;

		foreach ($select as $s) {
			if (is_array($s)) {
				$s = $s[$build_key];
			}

			$v = is_integer($s) ? (int)$key : $key;

			if (((is_integer($v) && $s !== '' && !is_bool($s) && !is_null($s)) ? (int)$s : $s) === $v) {
				$selected = true;
				break;
			}
		}

		if ($readonly) {
			if ($selected) {
				$options .= "<div class=\"value\">$value</div>";
			}
			continue;
		}

		$uniqid = uniqid($name . '-' . $key);

		switch ($type) {
			case 'select':
				$s = $selected ? "selected='true'" : '';
				if (strpos($key, '#optgroup') === 0) {
					if ($opt_group_active) {
						$options .= "</optgroup>";
					}
					$options .= "<optgroup label=\"$value\">";
					$opt_group_active = true;
				} else {
					$options .= "<option value=\"$key\" $s>$value</option>";
				}
				break;

			case 'ac-radio':
			case 'radio':
				$s = $selected ? 'checked="checked"' : '';
				$options .= "<label for=\"radio-$uniqid\" class=\"$type\"><input type=\"radio\" id=\"radio-$uniqid\" name=\"$name\" value=\"$key\" $s /><div class=\"text\">$value</div></label>";
				break;

			case 'checkbox':
				$s = $selected ? 'checked="checked"' : '';
				$options .= "<div class=\"checkbox-button\"><input type=\"checkbox\" id=\"checkbox-$uniqid\" class=\"ac-checkbox\" name=\"{$name}[]\" value=\"$key\" $s /><label for=\"checkbox-$uniqid\">$value</label></div>";
				break;

			case 'multiselect':
				$s = $selected ? 'checked="checked"' : '';
				$options .= "<li><input id=\"checkbox-$uniqid\" type=\"checkbox\" name=\"$name" . "[]\" value=\"$key\" $s /><label for=\"checkbox-$uniqid\">$value</label></li>";
				break;

			case 'clickable_list':
				if ($selected) {
					$selected_options .= "<div onclick=\"clickable_list_remove($(this))\"><span>$value</span><input type=\"hidden\" value=\"$key\" name=\"$key\" /><img src=\"view/theme/default/image/remove.png\" /></div>";
				} else {
					$options .= "<div onclick=\"clickable_list_add($(this), '$key')\"><span>$value</span><img src=\"view/theme/default/image/add.png\" /></div>";
				}
			default:
				break;
		}
	}

	if ($readonly) {
		return "<div $attrs>$options</div>";
	}

	switch ($type) {
		case 'select':
			if ($opt_group_active) {
				$options .= "</optgroup>";
			}
			return "<select name=\"$name\" $attrs>$options</select>";

		case 'radio':
		case 'ac-radio':
		case 'checkbox':
			return "<div $attrs>$options</div>";

		case 'multiselect':
			return <<<HTML
<div class="scrollbox-div">
	<ul class="scrollbox" $attrs>$options</ul>
</div>
<div class="scrollbox-buttons">
	<a class="check_all" onclick="$(this).parent().prev().find('input[type=checkbox]').prop('checked', true)">[ Check All ]</a>
	<a class="uncheck_all" onclick="$(this).parent().prev().find('input[type=checkbox]').prop('checked', false)">[ Uncheck All ]</a>
</div>
HTML
;

		case 'clickable_list':
			$added_list = "<div class=\"scrollbox clickable_added\">$selected_options</div>";
			$list       = "<div class=\"scrollbox clickable\">$options</div>";
			return "<div class=\"clickable_list\">$added_list $list</div>";
	}
}

function build_js($js)
{
	static $js_loaded_files = array();

	$args = func_get_args();
	array_shift($args);

	ob_start();

	include(DIR_SYSTEM . 'helper/builder_js.php');

	return ob_get_clean();
}

function rrmdir($dir)
{
	foreach (glob($dir . '/*') as $file) {
		if (is_dir($file)) {
			rrmdir($file);
		} else {
			unlink($file);
		}
	}
	rmdir($dir);
}

function crypto_rand($min, $max)
{
	$range = $max - $min;
	if ($range < 0) {
		return $min;
	} // not so random...
	$log    = log($range, 2);
	$bytes  = (int)($log / 8) + 1; // length in bytes
	$bits   = (int)$log + 1; // length in bits
	$filter = (int)(1 << $bits) - 1; // set all lower bits to 1
	do {
		$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
		$rnd = $rnd & $filter; // discard irrelevant bits
	} while ($rnd >= $range);
	return $min + $rnd;
}

function tokengen($length)
{
	$token        = "";
	$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
	$codeAlphabet .= "0123456789";
	for ($i = 0; $i < $length; $i++) {
		$token .= $codeAlphabet[crypto_rand(0, strlen($codeAlphabet))];
	}
	return $token;
}

function output($output)
{
	global $registry;
	$registry->get('response')->setOutput($output);
}

function _is_object($o) {
	return is_array($o) || is_object($o) || is_resource($o);
}
