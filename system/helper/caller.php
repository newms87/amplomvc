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

function image($image, $width = null, $height = null)
{
	global $registry;
	return $registry->get('image')->resize($image, $width, $height);
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
	if (is_file(DIR_THEME . $path)) {
		return site_url(URL_THEME . $path, $query);
	} elseif (is_file(DIR_THEME_PARENT . $path)) {
		return site_url(URL_THEME_PARENT . $path, $query);
	}

	return site_url(URL_THEME . $path, $query);
}

function theme_dir($path = '')
{
	if (is_file(DIR_THEME . $path)) {
		return DIR_THEME . $path;
	} elseif (is_file(DIR_THEME_PARENT . $path)) {
		return DIR_THEME_PARENT . $path;
	}

	return false;
}

function redirect($path = '', $query = null, $status = null)
{
	global $registry;
	$registry->get('url')->redirect($path, $query, $status);
}

function option($option, $default = null)
{
	global $registry;
	$value = $registry->get('config')->get($option);

	return is_null($value) ? $default : $value;
}

function format($type, $data, $param = null)
{
	global $registry;

	$args = func_get_args();
	array_shift($args);

	if (!is_string($type) && is_callable($type)) {
		return call_user_func_array($type, $args);
	}

	return call_user_func_array(array($registry->get($type), 'format'), $args);
}

function format_all($type, &$array, $index = null, $key = 'formatted')
{
	array_walk($array, function (&$a, $i) use ($type, $index, $key) {
		$a[$key] = $index ? format($type, $a[$index], $i) : format($type, $a, $i);
	});

	return $array;
}

function build($type, $params)
{
	global $registry;
	if (isset($params['key'])) {
		$registry->get('builder')->setConfig($params['key'], $params['value']);
	}

	return $registry->get('builder')->build($type, $params['data'], $params['name'], $params['select']);
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
