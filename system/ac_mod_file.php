<?php
define('AC_MOD_REGISTRY', DIR_SITE . 'system/mods/registry.ac');
define('AC_TEMPLATE_CACHE', DIR_CACHE . 'templates/');

if (!defined("AMPLO_DIR_MODE")) {
	define("AMPLO_DIR_MODE", 0755);
}

if (!defined("AMPLO_FILE_MODE")) {
	define("AMPLO_FILE_MODE", 0755);
}

//TODO: do we allow different modes?
function _is_writable($dir, &$error = null)
{
	if (!is_writable($dir)) {
		if (!is_dir($dir)) {
			mkdir($dir, AMPLO_DIR_MODE, true);
			chmod($dir, AMPLO_DIR_MODE);
		}

		if (!is_dir($dir)) {
			$error = "Do not have write permissions to create directory " . $dir . ". Please change the permissions to allow writing to this directory.";
			return false;
		} else {
			$t_file = $dir . uniqid('test') . '.txt';
			touch($t_file);
			if (!is_file($t_file)) {
				$error = "The write permissions on $dir are not set to allow apache to write. Please change the permissions to allow writing to this directory";
				return false;
			}
			unlink($t_file);
		}
	}

	return true;
}

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

	$ext = pathinfo($file, PATHINFO_EXTENSION);

	if (is_file($file . '.acmod')) {
		$file = $file . '.acmod';
	}

	if (isset($live_registry[$file]) && is_file($live_registry[$file])) {
		$file = $live_registry[$file];
	}

	//Replace PHP short tags in template files. There are servers that disable this by default.
	if ($ext === 'tpl') {
		$tpl = AC_TEMPLATE_CACHE . str_replace(DIR_SITE, '', $file);

		if (!is_file($tpl) || filemtime($tpl) < filemtime($file)) {
			_is_writable(dirname($tpl));

			$contents = preg_replace("/<\\?([^p=])/", "<?php \$1", file_get_contents($file));

			if (defined("AMPLO_REWRITE_SHORT_TAGS") && AMPLO_REWRITE_SHORT_TAGS) {
				$contents = preg_replace("/<\\?=/", "<?php echo", $contents);
			}

			file_put_contents($tpl, $contents);
		}

		$file = $tpl;
	}

	return $file;
}
