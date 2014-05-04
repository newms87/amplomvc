<?php
function _call($path, $params = null)
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

function _block($block, $instance_name = null, $settings = null)
{
	global $registry;
	return $registry->get('block')->render($block, $instance_name, $settings);
}

function _area($area)
{
	global $registry;
	return $registry->get('area')->render($area);
}

function _area_has_blocks($area)
{
	global $registry;
	return $registry->get('area')->hasBlocks($area);
}

function _links($group)
{
	global $registry;
	return $registry->get('document')->renderLinks($group);
}

function _has_links($group)
{
	global $registry;
	return $registry->get('document')->hasLinks($group);
}

function _breadcrumbs()
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
	return $registry->get('url')->link($store_id, $path, $query);
}

function theme_url($path = '', $query = null)
{
	if (is_file(DIR_THEME . $path)) {
		return site_url(URL_THEME . $path, $query);
	} elseif (is_file(DIR_THEME_PARENT . $path)) {
		return site_url(URL_THEME_PARENT . $path, $query);
	}

	return false;
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

function redirect($path = '', $query = null, $status = null) {
	global $registry;
	$registry->get('url')->redirect($path, $query, $status);
}

function option($option, $default = null)
{
	global $registry;
	$value = $registry->get('config')->get($option);

	return is_null($value) ? $default : $value;
}
