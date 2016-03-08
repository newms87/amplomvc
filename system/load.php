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

// Registry
$registry = new Registry();

//Initialize Router (run routeRequest after registering routing hooks in helpers)
$router = new Router();
$registry->set('router', $router);

//Helpers
//Tip: to override core / shortcuts functions, use a mod file.
require_once(_mod(DIR_SYSTEM . 'helper/core.php'));
require_once(_mod(DIR_SYSTEM . 'helper/shortcuts.php'));

if (AMPLO_PROFILE) {
	_profile('Core / Shortcut Helpers loaded');
}

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);

if (AMPLO_PROFILE) {
	_profile('Database loaded');
}

//TODO: REMOVE 'store' check once all sites updated for future
if (!isset($db->t['site']) && !isset($db->t['store'])) {
	$url = '//' . DOMAIN . SITE_BASE;

	echo <<<HTML
		<h2>The Database was not installed correctly. config.php has been renamed to config.php.bkp. Please reinstall Amplo MVC.</h2>
		<p>You are being redirected to the install page. Please wait... (refresh the page if you are not redirected in <b id="count">10</b> seconds)</p>
		<script type="text/javascript">
		(function countdown(c) {c ? setTimeout(function(){countdown(document.getElementById('count').innerHTML = --c)}, 1000) : window.location = "$url"})(10);
		</script>
HTML;

	rename(DIR_SITE . 'config.php', DIR_SITE . 'config.php.bkp');
	exit;
}

//Load Helper files (requires DB & cache)
$handle = opendir(DIR_SYSTEM . 'helper/');
while (($helper = readdir($handle))) {
	if (strpos($helper, '.') === 0) {
		continue;
	}

	//Already loaded
	if ($helper === 'core.php' || $helper === 'shortcuts.php') {
		continue;
	}

	if (is_file(DIR_SYSTEM . 'helper/' . $helper)) {
		require_once(_mod(DIR_SYSTEM . 'helper/' . $helper));
	}
}

//PHP Info
if (isset($_GET['phpinfo']) && $registry->get('user')->isTopAdmin()) {
	phpinfo();
	exit;
}

//Table Insert / Update / Delete History
$db->setHistoryTables((array)option('history_tables'));

//Amplo Cookie Token Check
if (isset($_GET['amp_token'])) {
	set_cookie($_GET['amp_token'], 1, 31536000, false);
}

//Customer Override (alternative logins)
if (!defined("AC_CUSTOMER_OVERRIDE")) {
	define("AC_CUSTOMER_OVERRIDE", substr(str_shuffle(md5(microtime())), 0, (int)rand(15, 20)));
}

//Cron Called from system
if (option('cron_status', true)) {
	if (defined("RUN_CRON")) {
		echo $registry->get('cron')->run();
		exit;
	} //Cron Called from browser
	elseif (isset($_GET['run_cron'])) {
		echo nl2br($registry->get('cron')->run());
		exit;
	} //Check if poor man's cron should run
	elseif (option('cron_check')) {
		$registry->get('cron')->check();
	}
}

if (AMPLO_PROFILE) {
	_profile('Route request');
}

//Route request after helpers (helper/core.php & helper/shortcuts.php required)
$router->routeRequest();

if (AMPLO_PROFILE) {
	_profile('Dispatching request');
}

//Router
$router->dispatch();

if (AMPLO_PROFILE) {
	_profile('Finished');
}
