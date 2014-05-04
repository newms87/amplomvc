<?php
define('AC_MOD_REGISTRY', DIR_MOD_FILES . 'registry.ac');
define('AC_TEMPLATE_CACHE', DIR_CACHE . 'templates/');

if (!file_exists(AC_MOD_REGISTRY)) {
	_is_writable(dirname(AC_MOD_REGISTRY));

	touch(AC_MOD_REGISTRY);
	chmod(AC_MOD_REGISTRY, 0444); //Set Read Only access
}

//Create / make writable Template Cache Dir
_is_writable(AC_TEMPLATE_CACHE);

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

	//Replace PHP short tags in template files. There are servers that disable this by default.
	if (pathinfo($file, PATHINFO_EXTENSION) === 'tpl') {
		$tpl = AC_TEMPLATE_CACHE . str_replace(DIR_SITE, '', $file);

		if (!is_file($tpl) || filemtime($tpl) < filemtime($file)) {
			_is_writable(dirname($tpl));

			$contents = preg_replace("/<\\?([^p=])/", "<?php \$1", file_get_contents($file));

			file_put_contents($tpl, $contents);
		}

		$file = $tpl;
	}

	return $file;
}
