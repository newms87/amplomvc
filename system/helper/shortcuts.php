<?php
//Request Headers
$headers = apache_request_headers();
function _header($key, $default = null)
{
	global $headers;
	return isset($headers[$key]) ? $headers[$key] : $default;
}

define("REQUEST_ACCEPT", _header('Accept'));

function request_accepts($type)
{
	return strpos(REQUEST_ACCEPT, $type) !== false;
}

/**************************************
 * System Language Translation Engine *
 **************************************/

global $language_group;
$language_group = "Load";

/**
 * Translate a string to the current requested language
 *
 * @param $message
 * @return mixed|string
 */
function _l($message)
{
	//TODO: Set translations based on language group
	global $language_group;

	$values = func_get_args();

	array_shift($values);

	//TODO: See bitbucket issue https://bitbucket.org/newms87/dopencart/issue/20/language-translation-engine
	if (empty($values)) {
		return _($message);
	}

	return vsprintf(_($message), $values);
}

/**
 * Change Language Group just for this message, then revert back if $message is given.
 * If $message is null, then the language group is changed permanently.
 *
 * @param $group - The language group to change to.
 * @param $message - The Message
 * @param $var1 , $var2, etc.. The variables to pass to vsprintf() with the message.
 *
 * @return null | String with the translated message
 */

function _lg($group, $message = null)
{
	global $language_group;

	//Permanently change Group.
	if ($message === null) {
		$language_group = $group;
		return;
	}

	//Temporarily Change Group
	$temp           = $language_group;
	$language_group = $group;

	$params = func_get_args();
	array_shift($params);

	$return = call_user_func_array('_l', $params);

	$language_group = $temp;

	return $return;
}

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

function get_breadcrumb($offset = 0)
{
	global $registry;
	return $registry->get('breadcrumb')->get($offset);
}

function breadcrumbs()
{
	global $registry;
	return $registry->get('breadcrumb')->render();
}

function cache($key, $value = null, $as_file = false)
{
	global $registry;

	if (is_null($value)) {
		return $registry->get('cache')->get($key, $as_file);
	} else {
		return $registry->get('cache')->set($key, $value, $as_file);
	}
}

function message($type, $message = null)
{
	global $registry;
	$registry->get('message')->add($type, $message);
}

function render_message($type = null, $close = null)
{
	global $registry;
	return $registry->get('message')->render($type, $close);
}

function image($image, $width = null, $height = null, $default = null, $cast_protocol = false)
{
	global $registry;
	if ($width || $height) {
		$image = $registry->get('image')->resize($image, $width, $height);
	} else {
		$image = $registry->get('image')->get($image);
	}

	if (!$image && $default) {
		if ($default === true) {
			$image = theme_image('no_image.png', $width, $height);
		} else {
			$image = $registry->get('image')->resize($default, $width, $height);
		}
	}

	if ($image && $cast_protocol) {
		return cast_protocol($image, is_string($cast_protocol) ? $cast_protocol : 'http');
	}

	return $image;
}

function image_srcset($srcsets, $nx = 3, $width = null, $height = null, $default = null, $cast_protocol = false)
{
	if (!is_array($srcsets)) {
		$srcsets = array(
			$nx => $srcsets,
		);
	}

	$max_x   = max(array_keys($srcsets));
	$image = $srcsets[$max_x];

	if (!is_file($image)) {
		$image = DIR_IMAGE . $image;

		if (!is_file($image)) {
			return 'src=""';
		}
	}

	if (!$width && !$height) {
		$size = getimagesize($image);

		if (!$size) {
			return 'src=""';
		}

		$width  = $size[0] / $nx;
		$height = $size[1] / $nx;
	}

	while ($nx > 1) {
		$srcsets[$nx] = empty($srcsets[$nx]) ? image($image, $width * $nx, $height * $nx, $default, $cast_protocol) : image($srcsets[$nx]);
		$srcsets[$nx] .= ' ' . $nx . 'x';
		$nx--;
	}

	$src = empty($srcsets[1]) ? image($image, $width, $height, $default, $cast_protocol) : image($srcsets[1]);
	unset($srcsets[1]);

	if ($srcsets) {
		ksort($srcsets);
		return "src=\"$src\" srcset=\"" . implode(',', $srcsets) . "\"";
	}

	return "src=\"$src\"";
}

function image_save($image, $save_as = null, $width = null, $height = null, $default = null, $cast_protocol = false)
{
	$new_image = image($image, $width, $height, $default, false);

	if ($new_image) {
		if (!$save_as) {
			if (strpos($new_image, URL_IMAGE . 'cache/') === 0) {
				$save_as = str_replace(URL_IMAGE . 'cache/', 'saved/', $new_image);
			}
		} else {
			$save_as = str_replace(DIR_IMAGE, '', $save_as);
		}

		if ($save_as) {
			$new_image_file = str_replace(URL_IMAGE, DIR_IMAGE, $new_image);

			if (is_file($new_image_file)) {
				if (_is_writable(dirname(DIR_IMAGE . $save_as))) {
					rename($new_image_file, DIR_IMAGE . $save_as);
					$new_image = URL_IMAGE . $save_as;
				}
			}
		}
	}

	if ($cast_protocol) {
		return cast_protocol($image, is_string($cast_protocol) ? $cast_protocol : 'http');
	}

	return $new_image;
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

function post_redirect($path = '', $query = null, $status = null)
{
	$_SESSION['__post_data__'] = $_POST;
	redirect($path, $query, $status);
}

function slug($name, $sep = '_', $allow = 'a-z0-9_-')
{
	$patterns = array(
		"/[\\s\\\\\\/]/" => $sep,
		"/[^$allow]/"    => '',
	);

	return preg_replace(array_keys($patterns), array_values($patterns), strtolower(trim($name)));
}

function cast_title($name)
{
	$title = array_map(function ($a) {
		return ucfirst($a);
	}, explode('_', str_replace('-','_',$name)));

	return implode(' ', $title);
}

function cast_protocol($url, $cast = 'http')
{
	$scheme = parse_url($url, PHP_URL_SCHEME);

	if ($cast) {
		$cast .= ':';
	}

	if ($scheme) {
		return $cast . '//' . preg_replace("#^" . $scheme . '://#', '', $url);
	} elseif (strpos($url, '//') === 0) {
		return $cast . $url;
	} else {
		return $cast . '//' . $url;
	}
}

define("IS_ADMIN", strpos(rtrim($_SERVER['REQUEST_URI'], '/'), SITE_BASE . 'admin') === 0);

define("IS_SSL", !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

define("IS_AJAX", isset($_GET['ajax']) ? true : isset($headers['X-Requested-With']));
define("IS_POST", $_SERVER['REQUEST_METHOD'] === 'POST');
define("IS_GET", $_SERVER['REQUEST_METHOD'] === 'GET');

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

function is_logged()
{
	global $registry;
	if (IS_ADMIN) {
		return $registry->get('user')->isLogged();
	} else {
		return $registry->get('customer')->isLogged();
	}
}

function customer_info($key = null)
{
	global $registry;
	return $registry->get('customer')->info($key);
}

function user_can($level, $path)
{
	global $registry;
	return $registry->get('user')->can($level, $path);
}

function user_info($key = null)
{
	global $registry;
	return $registry->get('user')->info($key);
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
				$options .= "<label for=\"radio-$uniqid\" class=\"$type\"><input type=\"radio\" id=\"radio-$uniqid\" name=\"$name\" value=\"$key\" $s /><span class=\"label\">$value</span></label>";
				break;

			case 'checkbox':
				$s = $selected ? 'checked="checked"' : '';
				$options .= "<label for=\"checkbox-$uniqid\" class=\"checkbox\"><input type=\"checkbox\" id=\"checkbox-$uniqid\" name=\"{$name}[]\" value=\"$key\" $s /><span class=\"label\">$value</span></label>";
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
<div $attrs>
	<ul class="multiselect-list">$options</ul>
</div>
<div class="multiselect-buttons">
	<a class="check_all" onclick="$(this).parent().prev().find('input[type=checkbox]').prop('checked', true)">[ Check All ]</a>
	<a class="uncheck_all" onclick="$(this).parent().prev().find('input[type=checkbox]').prop('checked', false)">[ Uncheck All ]</a>
</div>
HTML
				;

		case 'clickable_list':
			$added_list = "<div class=\"multiselect-list clickable_added\">$selected_options</div>";
			$list       = "<div class=\"multiselect-list clickable\">$options</div>";
			return "<div class=\"clickable_list\">$added_list $list</div>";
	}
}

function build_js($js)
{
	static $js_loaded_files = array();

	$args = func_get_args();
	array_shift($args);

	ob_start();

	include(DIR_RESOURCES . 'builder_js.php');

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

function output_json($data)
{
	global $registry;
	$registry->get('response')->setOutput(json_encode($data), 'application/json');
}

function output_as_file($contents, $type = 'txt', $filename = '')
{
	global $registry;
	$registry->get('csv')->setContents($contents);
	$registry->get('csv')->downloadContents($filename, $type);
}

function output_file($file, $type = null, $filename = null)
{
	global $registry;
	$registry->get('csv')->downloadFile($file, $filename, $type);
}

function _is_object($o)
{
	return is_array($o) || is_object($o) || is_resource($o);
}

