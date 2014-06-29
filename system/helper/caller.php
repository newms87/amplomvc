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

function is_post()
{
	return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function is_get()
{
	return $_SERVER['REQUEST_METHOD'] === 'GET';
}

function is_ajax()
{
	return !empty($_GET['ajax']);
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
		'name'   => '',
		'data'   => null,
		'select' => array(),
		'key'    => null,
		'value'  => null,
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

	if (!isset($params['#class'])) {
		$params['#class'] = "builder-$type";
	} else {
		$params['#class'] .= " builder-$type";
	}

	$attrs = attrs($params);

	if ($type === 'text') {
		return "<input type=\"text\" $attrs name=\"$name\" value=\"$select\" />";
	}

	//This is for select option groups
	$opt_group_active = false;

	if (!is_array($select)) {
		$select = array($select);
	}

	$options          = '';
	$selected_options = ''; //for clickable list

	foreach ($data as $value => $display) {

		if (is_array($display)) {
			if (($build_key && !isset($display[$build_key])) || ($build_value && !isset($display[$build_value]))) {
				trigger_error(_l("The associative indexes for 'key' and 'value' were not found in the data array."));
				return;
			}

			if ($build_key) {
				$value = $display[$build_key];
			}

			$display = $display[$build_value];
		}


		//Determine if the value is a selected value.
		//If the user specified the type of vars, use that type.,
		//otherwise try to guess the type.
		$selected = false;

		foreach ($select as $s) {
			if (is_array($s)) {
				$s = $s[$build_key];
			}

			$v = is_integer($s) ? (int)$value : $value;

			if (((is_integer($v) && $s !== '' && !is_bool($s) && !is_null($s)) ? (int)$s : $s) === $v) {
				$selected = true;
				break;
			}
		}

		$uniqid = uniqid($name . '-' . $value);

		switch ($type) {
			case 'select':
				$s = $selected ? "selected='true'" : '';
				if (strpos($value, '#optgroup') === 0) {
					if ($opt_group_active) {
						$options .= "</optgroup>";
					}
					$options .= "<optgroup label=\"$display\">";
					$opt_group_active = true;
				} else {
					$options .= "<option value=\"$value\" $s>$display</option>";
				}
				break;

			case 'ac-radio':
			case 'radio':
				$s = $selected ? 'checked="checked"' : '';
				$options .= "<label for=\"radio-$uniqid\" class=\"$type\"><input type=\"radio\" id=\"radio-$uniqid\" name=\"$name\" value=\"$value\" $s /><div class=\"text\">$display</div></label>";
				break;

			case 'checkbox':
				$s = $selected ? 'checked="checked"' : '';
				$options .= "<div class=\"checkbox-button\"><input type=\"checkbox\" id=\"checkbox-$uniqid\" class=\"ac-checkbox\" name=\"{$name}[]\" value=\"$value\" $s /><label for=\"checkbox-$uniqid\">$display</label></div>";
				break;

			case 'multiselect':
				$s = $selected ? 'checked="checked"' : '';
				$options .= "<li><input id=\"checkbox-$uniqid\" type=\"checkbox\" name=\"$name" . "[]\" value=\"$value\" $s /><label for=\"checkbox-$uniqid\">$display</label></li>";
				break;

			case 'clickable_list':
				if ($selected) {
					$selected_options .= "<div onclick=\"clickable_list_remove($(this))\"><span>$display</span><input type=\"hidden\" value=\"$value\" name=\"$value\" /><img src=\"view/theme/default/image/remove.png\" /></div>";
				} else {
					$options .= "<div onclick=\"clickable_list_add($(this), '$value')\"><span>$display</span><img src=\"view/theme/default/image/add.png\" /></div>";
				}
			default:
				break;
		}
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
			return "<ul class=\"scrollbox\" $attrs>$options</ul>" .
			"<div class=\"scrollbox_buttons\">" .
			"<a class=\"check_all\" onclick=\"$(this).parent().prev().find('input[type=checkbox]').prop('checked', true)\">[ Check All ]</a>" .
			"<a class=\"uncheck_all\" onclick=\"$(this).parent().prev().find('input[type=checkbox]').prop('checked', false)\">[ Uncheck All ]</a>" .
			"</div>";

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
