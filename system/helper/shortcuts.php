<?php
/**
 * @author  Daniel Newman
 * @date    3/20/2013
 * @package Amplo MVC
 * @link    http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

function call($path, $params = null, $is_ajax = null)
{
	$action = new Action($path, $params);

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
	require_once _mod($registry->get('theme')->getFile('template/head'));
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

$_breadcrumbs = array();

function breadcrumb($label, $href, $position = null, $class = '')
{
	global $_breadcrumbs;

	$crumb = array(
		'label' => $label,
		'href'  => $href,
		'class' => trim($class . ' breadcrumb'),
	);

	if ($position !== null && !empty($_breadcrumbs)) {
		array_splice($_breadcrumbs, $position, 0, array($crumb));
	} else {
		$_breadcrumbs[] = $crumb;
	}
}

function breadcrumbs()
{
	global $_breadcrumbs;

	if (IS_ADMIN ? option('admin_show_breadcrumbs', true) : option('show_breadcrumbs', true)) {
		foreach ($_breadcrumbs as $key => &$crumb) {
			$crumb['html'] = "<a href=\"$crumb[href]\" class=\"$crumb[class]\"><span>" . _strip_tags($crumb['label']) . "</span></a>";
		}

		return implode('', array_column($_breadcrumbs, 'html'));
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

function message($type, $message = null, $key = null)
{
	global $registry;
	$registry->get('message')->add($type, $message, $key);
}

function render_message($type = null, $close = true)
{
	global $registry;

	return $registry->get('message')->render($type, $close);
}

function send_mail($params)
{
	global $registry;

	return $registry->get('mail')->init($params)->send();
}

function img($image, $options = array())
{
	global $registry;

	$options += array(
		'width'         => null,
		'height'        => null,
		'max_size'      => null,
		'version'       => true,
		'size_attr'     => true,
		'default'       => null,
		'cast_protocol' => false,
	);

	$src = image($image, $options['width'], $options['height'], $options['max_size'], $options['default'], $options['cast_protocol']);

	if ($src && ($options['version'] || $options['size_attr'])) {
		$file = $registry->get('image')->get($src, true);

		if ($file && is_file($file)) {
			if ($options['version']) {
				$src .= '?v=' . filemtime($file);
			}

			if ($options['size_attr'] && $size = getimagesize($file)) {
				list($options['width'], $options['height']) = $size;
			}
		}
	}

	if ($options['size_attr']) {
		!$options['width'] || $options['#width'] = $options['width'];
		!$options['height'] || $options['#height'] = $options['height'];
	}

	$options['#src'] = $src ?: $image;

	return "<img " . attrs($options) . "/>";
}

function image($file, $width = null, $height = null, $max_size = false, $default = null, $cast_protocol = false)
{
	global $registry;

	if ($width || $height) {
		$image = new Image($file);
		$url   = $image->resize($width, $height, $max_size);
	} else {
		$url = $registry->get('image')->get($file);
	}

	if (!$url && $default) {
		if ($default === true) {
			$url = image(theme_url('image/no_image.png'), $width, $height);
		} else {
			$image = new Image($default);
			$url   = $image->resize($width, $height, $max_size);
		}
	}

	if ($url && $cast_protocol) {
		return cast_protocol($url, is_string($cast_protocol) ? $cast_protocol : 'http');
	}

	return $url;
}

function image_srcset($srcsets, $nx = 3, $alt = null, $title = null)
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

		if ($nx > 1 && !empty($srcsets[$nx])) {
			$srcsets[$nx] .= ' ' . $nx . 'x';
		}

		$nx--;
	}

	$src = empty($srcsets[1]) ? preg_replace("/ \\dx/", '', current($srcsets)) : $srcsets[1];
	unset($srcsets[1]);

	$size = _getimagesize($src);

	if (!empty($srcsets)) {
		ksort($srcsets);
	}

	return "src=\"$src\" $size " . (!empty($srcsets) ? "srcset=\"" . implode(',', $srcsets) . "\" " : '') . "alt=\"$alt\" title=\"$title\"";
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
	global $registry;

	$image_file = $registry->get('image')->get($image, true);

	if ($image_file) {
		$size = getimagesize($image_file);

		return isset($size[3]) ? $size[3] : '';
	}

	return '';
}

function _create_image($file, $mime = null)
{
	if (!$file) {
		return null;
	}

	if (!$mime) {
		$mime = image_type_to_mime_type(exif_imagetype($file));
	}

	switch ($mime) {
		case 'gif':
		case 'image/gif':
			$function = 'imagecreatefromgif';
			break;

		case 'png':
		case 'image/png':
			$function = 'imagecreatefrompng';
			break;

		case 'jpg':
		case 'jpeg':
		case 'image/jpg':
		case 'image/jpeg':
			$function = 'imagecreatefromjpeg';
			break;

		default:
			return null;
	}

	//increase the maximum memory limit from the settings
	ini_set('memory_limit', option('config_image_max_mem', '2G'));

	$image = @$function($file);

	//If image creation fails (ie: stream wrapper buggy), try downloading the file first
	if (!$image) {
		$temp = DIR_IMAGE . 'cache/temp/' . uniqid();

		if (_is_writable(dirname($temp))) {
			file_put_contents($temp, file_get_contents($file));

			$image = @$function($temp);

			unlink($temp);
		}
	}

	return $image;
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

function is_url($url)
{
	return (filter_var($url, FILTER_VALIDATE_URL) || strpos($url, '//') === 0);
}

function site_url($path = '', $query = null, $ssl = null, $site_id = null)
{
	global $registry;

	return $registry->get('url')->link($path, $query, $ssl, $site_id);
}

function theme_url($path = '', $query = null, $theme = null)
{
	global $registry;

	$url = $registry->get('theme')->getUrl($path, $theme);

	if (!$url) {
		$url = URL_THEME . $path;
	}

	if ($query) {
		$url .= (strpos($url, '?') ? '&' : '?') . $query;
	}

	return $url;
}

function theme_dir($path = '', $theme = null)
{
	global $registry;

	return $registry->get('theme')->getFile($path, $theme);
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

function slug($name, $sep = '_', $allow = 'a-z0-9._-')
{
	return preg_replace("/[$sep]+/", $sep, preg_replace("/[^$allow]/", $sep, strtolower(trim($name))));
}

function cast_title($name)
{
	$title = array_map(function ($a) {
		return ucfirst($a);
	}, explode('_', str_replace('-', '_', $name)));

	return implode(' ', $title);
}

function has_scheme($url)
{
	return parse_url($url, PHP_URL_SCHEME) || strpos($url, '//') === 0;
}

function cast_protocol($url, $cast = null)
{
	if ($cast === null) {
		$cast = IS_SSL ? 'https' : 'http';
	}

	$scheme = parse_url($url, PHP_URL_SCHEME);

	if ($scheme) {
		return $cast . '://' . preg_replace("#^" . $scheme . '://#', '', $url);
	} elseif (strpos($url, '//') === 0) {
		return $cast . ':' . $url;
	} else {
		return $cast . '://' . $url;
	}
}

function option($option, $default = null)
{
	global $_options;

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

function js_var($key, $value = null)
{
	global $_js_vars;

	if ($value === null) {
		return isset($_js_vars[$key]) ? $_js_vars[$key] : null;
	} else {
		$_js_vars[$key] = $value;
	}
}

function page_info($key = null, $default = null)
{
	global $registry;

	return $registry->get('document')->info($key, $default);
}

function set_page_info($key, $value)
{
	global $registry;

	return $registry->get('document')->setInfo($key, $value);
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

function customer_info($key = null, $default = null)
{
	global $registry;

	return $registry->get('customer')->info($key, $default);
}

function set_customer_info($key, $value)
{
	global $registry;

	return $registry->get('customer')->setInfo($key, $value);
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

function user_can($level, $action)
{
	global $registry;

	return $registry->get('user')->can($level, $action);
}

/**
 * Check if current user's role is in the 1 or more provided roles
 *
 * @param string $role  - The Role / Type to check (can enter 1 or more role parameters)
 * @param string $role2 - Additional roles / types to check.
 *
 * @return bool - true if the user's role or type matches any $role arguments.
 */
function user_is($role)
{
	global $registry;

	return $registry->get('user')->is(func_get_args());
}

function user_info($key = null)
{
	global $registry;

	return $registry->get('user')->info($key);
}

function user_meta($key, $default = null)
{
	global $registry;

	return $registry->get('user')->meta($key, $default);
}

function set_user_meta($key, $value = null)
{
	global $registry;

	return $registry->get('user')->setMeta($key, $value);
}

function get_user_info($user_id, $key = null, $default = null)
{
	global $registry;

	static $users;

	if (!$user_id) {
		return;
	}

	if (!isset($users[$user_id])) {
		$users[$user_id] = $registry->get('Model_User')->getUser($user_id);
	}

	if (!$users[$user_id]) {
		return;
	}

	if ($key) {
		return isset($users[$user_id][$key]) ? $users[$user_id][$key] : $default;
	}

	return $users[$user_id];
}

function validate($method, $value)
{
	global $registry;

	$args = func_get_args();
	array_shift($args);

	return call_user_func_array(array(
		$registry->get('validation'),
		$method,
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
		'format',
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

function value2label($select, $data, $label_index, $value_index)
{
	$label = '';
	$map   = array();

	foreach ($data as $key => $value) {
		if (is_array($value)) {
			if ($value_index) {
				if (!isset($value[$value_index])) {
					continue;
				}

				$v = $value[$value_index];
			} else {
				$v = $key;
			}

			if ($label_index) {
				if (!isset($value[$label_index])) {
					continue;
				}

				$l = $value[$label_index];
			} else {
				$l = $value;
			}
		} else {
			$v = $key;
			$l = $value;
		}

		$map[$v] = $l;
	}

	$value_set = false;

	foreach ((array)$select as $s) {
		if (isset($map[$s])) {
			$label .= $map[$s];
			$value_set = true;
		}
	}

	return $value_set ? $label : null;
}

function build($type, $params = null)
{
	static $count = 0;

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
		'textarea',
		'text',
		'pk-int',
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
		} elseif ($type === 'textarea') {
			return "<textarea $attrs name=\"$name\">$select</textarea>";
		} else {
			return "<input type=\"text\" $attrs name=\"$name\" value=\"$select\" />";
		}
	}

	if (!is_array($data)) {
		trigger_error(_l("The 'data' parameter must be an array to build type %s", $type));

		return;
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
				trigger_error(_l("The indexes for value (%s) and label (%s) not found in data array at index %s for build object %s.", $value_key, $label_key, $key, $name));

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

		$uniqid = $name . '-' . $key . '-' . $count++;

		switch ($type) {
			case 'select':
			case 'multiselect':
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

			case 'text-select':
				$s = $selected ? 'checked="checked"' : '';
				$options .= "<li><label for=\"text-select-$uniqid\" class=\"$type\"><input type=\"radio\" id=\"text-select-$uniqid\" name=\"$name\" value=\"$key\" $s /><div class=\"label\"><span class=\"text\">$value</span></div></label></li>";
				break;

			case 'radio':
				$s = $selected ? 'checked="checked"' : '';
				$options .= "<label for=\"radio-$uniqid\" class=\"$type\"><input type=\"radio\" id=\"radio-$uniqid\" name=\"$name\" value=\"$key\" $s /><div class=\"label\"><span class=\"text\">$value</span></div></label>";
				break;

			case 'checkbox':
				$s = $selected ? 'checked="checked"' : '';
				$options .= "<label for=\"checkbox-$uniqid\" class=\"checkbox\"><input type=\"checkbox\" id=\"checkbox-$uniqid\" name=\"{$name}[]\" value=\"$key\" $s /><div class=\"label\"><span class=\"text\">$value</span></div></label>";
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

		case 'multiselect':
			if ($opt_group_active) {
				$options .= "</optgroup>";
			}

			return "<select multiple name=\"{$name}[]\" $attrs>$options</select>";

		case 'text-select':
			return <<<HTML
<div $attrs>
	<input type="text" name="$name" value="" />
	<ul class="text-select-list">$options</ul>
</div>
HTML;

		case 'radio':
		case 'checkbox':
			return "<div $attrs>$options</div>";

		case 'multiselect-bkp':
			return <<<HTML
<div $attrs>
	<ul class="multiselect-list">$options</ul>
</div>
<div class="multiselect-buttons">
	<a class="check_all" onclick="$(this).parent().prev().find('input[type=checkbox]').prop('checked', true)">[ Check All ]</a>
	<a class="uncheck_all" onclick="$(this).parent().prev().find('input[type=checkbox]').prop('checked', false)">[ Uncheck All ]</a>
</div>
HTML;

		case 'clickable_list':
			$added_list = "<div class=\"multiselect-list clickable_added\">$selected_options</div>";
			$list       = "<div class=\"multiselect-list clickable\">$options</div>";

			return "<div class=\"clickable_list\">$added_list $list</div>";
	}
}

function build_links($links, $options = array(), $active_url = null, &$is_active = false)
{
	global $registry;

	$html = '';

	$options += array(
		'sort'       => null,
		'class'      => 'vertical',
		'amp_toggle' => false,
	);

	$active_url = path_format($active_url === null ? $registry->get('url')->here() : $active_url);

	if (is_string($links)) {
		$links = get_links($links);
	}

	if ($options['sort'] === null) {
		$first           = current($links);
		$options['sort'] = !empty($first['sort_order']) ? 'sort_order' : false;
	}

	if ($options['sort']) {
		sort_by($links, $options['sort']);
	}

	foreach ($links as $name => $link) {
		if (!empty($link['children']) && empty($link['path'])) {
			unset($link['path']);
		}

		if (IS_ADMIN && !empty($link['path']) && !strpos($link['path'], '://') && !user_can('r', $link['path'])) {
			continue;
		}

		if (!empty($link['name'])) {
			$name = $link['name'];
		}

		$link += array(
			'name'         => $name,
			'display_name' => $name,
			'class'        => $options['class'],
			'path'         => null,
			'show_on'      => (empty($link['href']) && empty($link['path'])) ? 'hover click' : 'hover expand',
			'a'            => array(),
		);

		$link['a'] += array(
			'#class' => ''
		);

		if (is_array($link['class'])) {
			$link['class'] = implode(' ', $link['class']);
		}

		$link['class'] .= ' link-' . $name;

		if (empty($link['href']) && isset($link['path'])) {
			$link['href'] = site_url($link['path'], isset($link['query']) ? $link['query'] : '');
		}

		if (!empty($link['href'])) {
			if (strpos($link['href'], '#') !== 0) {
				$link['a']['#class'] .= ' link';
			}

			$link['a']['#href'] = $link['href'];

			if (path_format($link['href']) === $active_url) {
				$is_active = true;
				$link['class'] .= ' active';
			}
		}

		if (!empty($link['children'])) {
			foreach (explode(' ', $link['show_on']) as $on) {
				$link['class'] .= ' on-' . $on;
			}

			$child_options = !empty($link['options']) ? $link['options'] : array();
			$child_active  = false;

			$children = build_links($link['children'], $child_options + $options, $active_url, $child_active);
			$link['a']['#class'] .= ' parent';

			if ($child_active) {
				$link['class'] .= ' active-child';
				$is_active = true;
			}
		} else {
			$children = '';
		}

		if (empty($link['a']['#href']) && !$children) {
			continue;
		}

		$link['#class']      = "link-menu menu-tab " . trim($link['class']);
		$link['a']['#class'] = trim($link['a']['#class']);

		$l = "<a " . attrs($link['a']) . ">$link[display_name]" . ($children ? "<i class=\"expand fa fa-chevron-down\"></i>" : '') . "</a>\n" . ($children ? "<div class=\"children\">$children</div>" : '');

		$html .= "<div " . attrs($link) . ">$l</div>";
	}

	return $html;
}

function output($output, $headers = null)
{
	global $registry;
	$registry->get('response')->setOutput($output, $headers);
}

function output_message()
{
	global $registry;
	output_json($registry->get('message')->fetch());
}

function output_json($data, $headers = array())
{
	global $registry;

	if (!isset($headers['Content-Type'])) {
		$headers['Content-Type'] = 'application/json';
	}

	$registry->get('response')->setOutput(json_encode($data), $headers);
}

function output_api($status, $message = null, $data = null, $code = 200, $http_code = null)
{
	if (!$http_code) {
		$http_code = $code;
	}

	if ($http_code !== 200) {
		header("HTTP/1.1 $http_code $message");
	}

	$response = array(
		'status'  => $status,
		'code'    => $code,
		'message' => $message,
		'data'    => $data,
	);

	if (AMPLO_API_LOG) {
		$data_json = json_encode($data);
		write_log('amplo_api', _l("%s (%s): %s<BR><BR>Request: %s<BR><BR>Response: %s", strtoupper($status), $code, $message, json_encode($_REQUEST), $data_json > AMPLO_API_LOG_MAX_SIZE ? strlen($data_json) . ' Bytes' : $data_json));
	}

	output_json($response);
}

function output_file($file, $type = null, $filename = null)
{
	if (is_file($file)) {
		$contents = file_get_contents($file);

		if (!$type) {
			$type = pathinfo($file, PATHINFO_EXTENSION);
		}

		if (!$filename) {
			$filename = basename($file);
		}

		output_as_file($contents, $type, $filename);
	}
}

function output_as_file($contents, $type = 'txt', $filename = '')
{
	global $registry;
	$registry->get('response')->setOutput($contents);
	$registry->get('response')->download($filename, $type);
}

function output_flush()
{
	global $registry;
	$registry->get('response')->output();
}

