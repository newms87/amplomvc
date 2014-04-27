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

function site_url($path, $query = null)
{
	global $registry;
	return $registry->get('url')->link($path, $query);
}

function store_url($store_id, $path, $query = null)
{
	global $registry;
	return $registry->get('url')->link($store_id, $path, $query);
}

function redirect($path, $query = null, $status = null) {
	global $registry;
	$registry->get('url')->redirect($path, $query, $status);
}
