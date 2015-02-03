<?php
function call($path, $params = null, $is_ajax = null)
{
	$args = func_get_args();
	array_shift($args);

	$action = new Action($path, $args);

	if ($action->execute($is_ajax)) {
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

function head()
{
	global $registry;
	require_once _mod($registry->get('theme')->getFile('head'));
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

function get_links($group)
{
	global $registry;
	return $registry->get('document')->getLinks($group);
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

	if (IS_ADMIN ? option('admin_show_breadcrumbs', true) : option('show_breadcrumbs', true)) {
		return $registry->get('breadcrumb')->render();
	}
}

function get_last_page($offset = -2)
{
	global $registry;
	return $registry->get('request')->getPrevPageRequest($offset);
}

function check_condition($condition)
{
	global $registry;
	return $registry->get('condition')->is($condition);
}

function message($type, $message = null)
{
	global $registry;
	$registry->get('message')->add($type, $message);
}

function render_message($type = null, $close = true)
{
	global $registry;
	return $registry->get('message')->render($type, $close);
}

function send_mail($params)
{
	global $registry;
	$mail = $registry->get('mail');
	$mail->init($params);
	return $mail->send();
}

function img($image, $width = null, $height = null, $title = null, $alt = null, $default = null, $cast_protocol = false)
{
	$src = image($image, $width, $height, $default, $cast_protocol);

	$size  = _getimagesize($src);

	$src   = $src ? "src=\"$src\"" : '';
	$title = $title !== false ? "title=\"$title\"" : '';
	$alt   = $alt !== false ? "alt=\"$alt\"" : '';

	return "$src $title $alt $size";
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

function image_srcset($srcsets, $nx = 3)
{
	if (empty($srcsets)) {
		return '';
	}

	if (!is_array($srcsets)) {
		$srcsets = array(
			1 => $srcsets,
		);
	}

	reset($srcsets);

	$path = pathinfo(current($srcsets));

	if (empty($path['filename'])) {
		return '';
	}

	while ($nx > 0) {
		if (empty($srcsets[$nx])) {
			$path['filename'] = preg_replace("/[-@]1x$/", '', $path['filename']);

			$src_name = image($path['dirname'] . '/' . $path['filename'] . '@' . $nx . 'x.' . $path['extension']);

			if (!$src_name) {
				$src_name = image($path['dirname'] . '/' . $path['filename'] . '-' . $nx . 'x.' . $path['extension']);
			}

			if ($src_name) {
				$srcsets[$nx] = $src_name;
			}
		} else {
			$srcsets[$nx] = image($srcsets[$nx]);
		}

		if ($nx > 1) {
			$srcsets[$nx] .= ' ' . $nx . 'x';
		}

		$nx--;
	}

	$src = empty($srcsets[1]) ? current($srcsets) : $srcsets[1];
	unset($srcsets[1]);

	$size = _getimagesize($src);

	if (!empty($srcsets)) {
		ksort($srcsets);
		return "src=\"$src\" srcset=\"" . implode(',', $srcsets) . "\" $size";
	}

	return "src=\"$src\" $size";
}

function build_srcset($image, $nx = 3, $width = null, $height = null, $default = null, $cast_protocol = false)
{
	$srcsets = array();

	if (!is_file($image)) {
		$image = DIR_IMAGE . $image;

		if (!is_file($image)) {
			return array();
		}
	}

	if (!$width && !$height) {
		$size = getimagesize($image);

		if (!$size) {
			return array();
		}

		$width  = $size[0] / $nx;
		$height = $size[1] / $nx;
	}

	while ($nx > 0) {
		$src = image($image, $width * $nx, $height * $nx, $default, $cast_protocol);

		if ($src) {
			$srcsets[$nx] = $src;
		}

		$nx--;
	}

	ksort($srcsets);

	return $srcsets;
}

function _getimagesize($image)
{
	$image_file = $image;

	if (!is_file($image_file)) {
		$image_file = str_replace(URL_SITE, DIR_SITE, $image);

		if (!is_file($image_file)) {
			$image_file = str_replace(URL_IMAGE, DIR_IMAGE, $image);

			if (!is_file($image_file)) {
				return '';
			}
		}
	}

	$size = getimagesize($image_file);

	return isset($size[3]) ? $size[3] : '';
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

function theme_sprite($image)
{
	static $sprites;

	if (!isset($sprites[$image])) {
		$path = pathinfo($image);

		$src = theme_image($image);

		$sprite_srcs = array();

		$sizes = array(
			2 => '2x',
			3 => '3x',
			4 => '4x',
		);

		$path['filename'] = preg_replace("/[-@]1x$/", '', $path['filename']);

		foreach ($sizes as $size => $name) {
			$s = theme_image($path['filename'] . '@' . $name . '.' . $path['extension']);

			if (!$s) {
				theme_image($path['filename'] . '-' . $name . '.' . $path['extension']);
			}

			if ($s) {
				$sprite_srcs[$size] = $s . ' ' . $size . 'x';
			}
		}

		$size = _getimagesize($src);

		$sprites[$image] = $sprite_srcs ? "src=\"$src\" srcset=\"" . implode(',', $sprite_srcs) . "\" $size" : "src=\"$src\" $size";
	}

	return $sprites[$image];
}

function site_url($path = '', $query = null, $ssl = null, $site_id = null)
{
	global $registry;
	return $registry->get('url')->link($path, $query, $ssl, $site_id);
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

function redirect($path = '', $query = null, $ssl = null, $status = null)
{
	global $registry;
	$registry->get('url')->redirect($path, $query, $ssl, $status);
}

function post_redirect($path = '', $query = null, $ssl = null, $status = null)
{
	$_SESSION['__post_data__'] = $_POST;
	redirect($path, $query, $ssl, $status);
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
	}, explode('_', str_replace('-', '_', $name)));

	return implode(' ', $title);
}

function cast_protocol($url, $cast = null)
{
	if ($cast === null) {
		$cast = IS_SSL ? 'https' : 'http';
	}

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

function option($option, $default = null)
{
	global $_options;

	//Load config if not loaded
	if (!$_options) {
		new Config;
	}

	return isset($_options[$option]) ? $_options[$option] : $default;
}

function set_option($option, $value)
{
	global $_options;
	$_options[$option] = $value;
}

function save_option($option, $value)
{
	global $registry;
	return $registry->get('config')->save('config', $option, $value);
}

function page_info($key = null, $default = null)
{
	global $registry;
	static $document, $info;

	if (!$document) {
		$document = $registry->get('document');
	}

	if (!$info) {
		$info = &$document->infoRef();
	}

	if (!$key) {
		return $info;
	}

	if ($key === 'styles') {
		return $document->getStyles();
	}

	if ($key === 'scripts') {
		return $document->getScripts();
	}

	return isset($info[$key]) ? $info[$key] : $default;
}

function set_page_info($key, $value)
{
	global $registry;
	$registry->get('document')->setInfo($key, $value);
}

function page_meta($key = null, $default = null)
{
	global $registry;
	return $registry->get('document')->meta($key, $default);
}

function set_page_meta($key, $value)
{
	global $registry;
	$registry->get('document')->setMeta($key, $value);
}

function language_info($key = null, $default = null)
{
	global $registry;
	return $registry->get('language')->info($key, $default);
}

function set_language_info($key, $value)
{
	global $registry;
	return $registry->get('language')->setInfo($key, $value);
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

function customer_meta($key, $default = null)
{
	global $registry;
	return $registry->get('customer')->meta($key, $default);
}

function set_customer_meta($key, $value)
{
	global $registry;

	if ($value === null) {
		return $registry->get('customer')->removeMeta($key);
	} else {
		return $registry->get('customer')->setMeta($key, $value);
	}
}

function user_can($level, $path)
{
	global $registry;
	return $registry->get('user')->can($level, $path);
}

/**
 * Check if current user's role is in the 1 or more provided roles
 *
 * @param string $role - The Role to check (can enter 1 or more role parameters)
 * @param string $role2 - Additional roles to check.
 *
 * @return bool - true if the user's role matches any of the provided roles.
 */
function user_is($role)
{
	global $registry;
	return in_array($registry->get('user')->info('role'), func_get_args());
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

function build($type, $params = null)
{
	if (!is_array($type)) {
		$params['type'] = $type;
	} else {
		$params = $type;
	}

	$params += array(
		'type'     => 'select',
		'name'     => '',
		'data'     => null,
		'select'   => array(),
		'value'    => null,
		'label'    => null,
		'readonly' => false,
	);

	if (!is_array($params['data'])) {
		trigger_error(_l("The 'data' parameter must be an array for %s", __METHOD__));
		return;
	}

	$type      = $params['type'];
	$data      = $params['data'];
	$name      = $params['name'];
	$select    = $params['select'];
	$value_key = $params['value'];
	$label_key = $params['label'];
	$readonly  = $params['readonly'];

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
			if (($value_key && !isset($value[$value_key])) || ($label_key && !isset($value[$label_key]))) {
				trigger_error(_l("The associative indexes for 'key' and 'value' were not found in the data array."));
				return;
			}

			if ($value_key) {
				$key = $value[$value_key];
			}

			$value = isset($value[$label_key]) ? $value[$label_key] : '';
		}

		//Determine if the value is a selected value.
		//If the user specified the type of vars, use that type.,
		//otherwise try to guess the type.
		$selected = false;

		foreach ($select as $s) {
			if (is_array($s)) {
				$s = $s[$value_key];
			}

			$v = is_integer($s) ? (int)$key : $key;

			if (((is_integer($v) && $s !== '' && !is_bool($s) && $s !== null) ? (int)$s : $s) === $v) {
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
	$token;
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

function output_message()
{
	global $registry;
	output_json($registry->get('message')->fetch());
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

