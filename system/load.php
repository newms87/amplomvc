<?php
//Model History
global $model_history;
if (!$model_history) {
	$model_history = array();
}

// Registry
$registry = new Registry();

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

$last_update = $db->queryRow("SHOW GLOBAL STATUS WHERE Variable_name = 'com_alter_table' AND Value > '" . (int)cache('db_last_update') . "'");

if (AMPLO_PROFILE) {
	_profile('Database loaded');
}

if ($last_update) {
	clear_cache('model');
	cache('db_last_update', $last_update['Value']);
	$db->updateTables();

	if (AMPLO_PROFILE) {
		_profile('Database Model refreshed');
	}
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

//Initialize Router
$router = new Router();

if (AMPLO_PROFILE) {
	_profile('Router loaded');
}

//Load Helper files
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

//Amplo Cookie Token Check
if (isset($_GET['amp_token'])) {
	set_cookie($_GET['amp_token'], 1, 31536000, false);
}

//Load Config
new Config();

if (AMPLO_AUTO_UPDATE) {
	$version = option('AMPLO_VERSION');

	if ($version !== AMPLO_VERSION) {
		message('notify', _l("The database version %s was out of date and has been updated to version %s", $version, AMPLO_VERSION));

		$this->System_Update->updateSystem(AMPLO_VERSION);
	}
}

if (AMPLO_PROFILE) {
	_profile('Config loaded');
}

//Route store after helpers (helper/core.php & helper/shortcuts.php required)
$router->routeSite();

if (AMPLO_PROFILE) {
	_profile('Site Routed');
}

//Register the core routing hook
register_routing_hook('amplo', 'amplo_routing_hook');

if (AMPLO_PROFILE) {
	_profile('Routing hooks run');
}

// Request (cleans globals)
$registry->set('request', new Request());

//Model History User Defined
$model_history = (array)option('model_history') + $model_history;

//Customer Override (alternative logins)
if (!defined("AC_CUSTOMER_OVERRIDE")) {
	define("AC_CUSTOMER_OVERRIDE", substr(str_shuffle(md5(microtime())), 0, (int)rand(15, 20)));
}

// Session
$registry->set('session', new Session());

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
	_profile('Dispatching request');
}

//Router
$router->dispatch();

if (AMPLO_PROFILE) {
	_profile('Finished');
}
