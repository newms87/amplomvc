<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

if (!defined('DIR_CACHE')) {
	define('DIR_CACHE', DIR_SITE . 'system/cache/');
}

define('AC_TEMPLATE_CACHE', DIR_CACHE . 'templates/');

if (!defined("AMPLO_DIR_MODE")) {
	define("AMPLO_DIR_MODE", 0775);
}

if (!defined("AMPLO_FILE_MODE")) {
	define("AMPLO_FILE_MODE", 0664);
}

function _is_writable($dir, &$error = null)
{
	$dir = rtrim($dir, '/') . '/';

	if (!is_writable($dir)) {
		if (!is_dir($dir)) {
			mkdir($dir, AMPLO_DIR_MODE, true);

			if (!is_dir($dir)) {
				$error = "Do not have write permissions to create directory " . $dir . ". Please change the permissions to allow writing to this directory.";

				return false;
			}
		}

		if (!strpos($dir, '://')) {
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

//Create / make writable Template Cache Dir
_is_writable(AC_TEMPLATE_CACHE);

function _mod($file)
{
	global $registry, $mod_update;

	$ext = pathinfo($file, PATHINFO_EXTENSION);

	$ext_file = is_file($file . '.ext') ? $file . '.ext' : '';

	$mod_file = $file . '.mod';

	if (is_file($mod_file)) {
		if (filemtime($mod_file) < filemtime($file)) {
			if ($registry && function_exists('redirect')) {
				$registry->get('mod')->reapply($mod_file);
			} else {
				$mod_update[$mod_file] = $mod_file;
			}
		}

		$file = $mod_file;
	}

	if ($mod_update && $registry && function_exists('redirect') && $registry->has('db')) {
		foreach ($mod_update as $key => $mod) {
			unset($mod_update[$key]);
			$registry->get('mod')->reapply($mod);
		}

		redirect($registry->get('url')->here());
	}

	//Replace PHP short tags in template files. There are servers that disable this by default.
	if ($ext === 'tpl') {
		$tpl = AC_TEMPLATE_CACHE . option('config_language', 'en') . '/' . str_replace(DIR_SITE, '', $file);

		if (!is_file($tpl) || filemtime($tpl) < filemtime($file)) {
			if (_is_writable(dirname($tpl))) {
				file_put_contents($tpl, render_template(file_get_contents($file)));
			}
		}

		$file = $tpl;
	}

	if ($ext_file) {
		require_once($file);

		return $ext_file;
	}

	return $file;
}

function render_template($contents)
{
	$contents = preg_replace("/<\\?([^p=])/", "<?php \$1", $contents);

	if (AMPLO_REWRITE_SHORT_TAGS) {
		$contents = preg_replace("/<\\?=/", "<?php echo", $contents);
	}

	$new_contents = '';

	//Translate text brackets vars
	$l_string = '';
	$l_inside = false;

	for ($i = 0; $i < strlen($contents); $i++) {
		switch ($contents[$i]) {
			case '{':
				if (!$l_inside && isset($contents[$i + 1]) && $contents[$i + 1] === '{') {
					$l_inside = true;
					$i += 2;
				}
				break;

			case '}':
				if ($l_inside && isset($contents[$i + 1]) && $contents[$i + 1] === '}') {
					$l_inside = false;
					$i += 2;
					$new_contents .= _l($l_string);
					$l_string = '';
				}
				break;
		}

		if (!$l_inside) {
			$new_contents .= $contents[$i];
		} else {
			$l_string .= $contents[$i];
		}
	}

	if (option('defer_scripts', true) && !strpos($new_contents, "NO-AMPLO-DEFER")) {
		$replace = array(
			"/type=['\"]text\\/javascript['\"]/" => "type=\"text/defer-javascript\"",
			"/<script\\s*>/"                     => "<script type=\"text/defer-javascript\">",
		);

		$new_contents = preg_replace(array_keys($replace), array_values($replace), $new_contents);
	}

	return $new_contents;
}

function render_content($content, $data = array())
{
	if (!$content) {
		return '';
	}

	$content_file = DIR_SITE . 'app/view/template/' . uniqid('content-', true) . '.temp';

	if (!@file_put_contents($content_file, render_template($content))) {
		trigger_error(_l("Unable to create content file for rendering: %s.", $content_file));

		return false;
	}

	$rendered = render_file($content_file, $data, false);

	@unlink($content_file);

	return $rendered;
}

function render_file($file, $data = array(), $mod = true)
{
	global $registry;

	if (!is_file($file)) {
		trigger_error(_l("Failed to render file %s because it did not exist.", $file));

		return false;
	}

	if (AMPLO_PROFILE) {
		_profile('RENDER: ' . $file);
	}

	$data += array(
		'r' => $registry,
	);

	extract($data);

	ob_start();
	include($mod ? _mod($file) : $file);
	$content = ob_get_clean();

	if (AMPLO_PROFILE) {
		_profile('RENDER COMPLETED: ' . $file);
	}

	return $content;
}
