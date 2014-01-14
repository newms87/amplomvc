<?php
define('AC_MOD_REGISTRY', DIR_MOD_FILES . 'registry.ac');

if (!file_exists(AC_MOD_REGISTRY)) {
	_is_writable(dirname(AC_MOD_REGISTRY));

	touch(AC_MOD_REGISTRY);
	chmod(AC_MOD_REGISTRY, 0444); //Set Read Only access
}

global $mod_registry, $live_registry;
$registries    = unserialize(file_get_contents(AC_MOD_REGISTRY));
$mod_registry  = isset($registries['mod']) ? $registries['mod'] : array();
$live_registry = isset($registries['live']) ? $registries['live'] : array();

function _ac_mod_file($file)
{
	global $live_registry;

	if (isset($live_registry[$file])) {
		$file = $live_registry[$file];

		if (!is_file($file)) {
			echo get_caller(0, 3);
		}
	}

	return $file;
}
