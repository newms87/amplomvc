<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

define('AMPLO_VERSION', '0.3.6');

if (is_file(dirname(__FILE__) . '/config.php')) {
	include_once(dirname(__FILE__) . '/config.php');
}

// Install
if (!defined('SITE_BASE') || defined("AMPLO_INSTALL_USER")) {
	define("AMPLO_INSTALL", true);
	require_once('system/install/install.php');
	exit;
}

//Timer for full system performance profiling
$__start = microtime(true);

//File Modifications
require_once(DIR_SITE . 'system/_mod.php');

// System Startup
require_once(_mod(DIR_SITE . 'system/startup.php'));

// Load
require_once(_mod(DIR_SYSTEM . 'load.php'));

