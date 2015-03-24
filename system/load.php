<?php

// Registry
$registry = new Registry();

//Helpers
//Tip: to override core functions, use a mod file!
require_once(_mod(DIR_SYSTEM . 'helper/core.php'));

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);

$last_update = $db->queryRow("SHOW GLOBAL STATUS WHERE Variable_name = 'com_alter_table' AND Value > '" . (int)cache('db_last_update') . "'");

if ($last_update) {
	clear_cache('model');
	cache('db_last_update', $last_update['Value']);
	$db->updateTables();
}

if (!isset($db->t['store'])) {
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
$registry->set('route', $router);

//Load Helper files
$handle = opendir(DIR_SYSTEM . 'helper/');
while (($helper = readdir($handle))) {
	if (strpos($helper, '.') === 0) {
		continue;
	}

	//Load these last
	if ($helper === 'core.php' || $helper === 'shortcuts.php') {
		continue;
	}

	if (is_file(DIR_SYSTEM . 'helper/' . $helper)) {
		require_once(_mod(DIR_SYSTEM . 'helper/' . $helper));
	}
}

//Load shortcut helper last to allow overriding
require_once(_mod(DIR_SYSTEM . 'helper/shortcuts.php'));

register_routing_hook('amplo', 'amplo_routing_hook');

//Route store after helpers (helper/core.php & helper/shortcuts.php required)
$router->routeSite();

// Request (cleans globals)
$registry->set('request', new Request());

//Model History
global $model_history;
$model_history += (array)option('model_history');

//Customer Override (alternative logins)
if (!defined("AC_CUSTOMER_OVERRIDE")) {
	define("AC_CUSTOMER_OVERRIDE", substr(str_shuffle(md5(microtime())), 0, (int)rand(15, 20)));
}

// Session
$registry->set('session', new Session());

//Mod Files
$registry->set('mod', new Mod());

// Url
$registry->set('url', new Url());

// Response
$response = new Response();
$response->addHeader('Content-Type', 'text/html; charset=UTF-8');
$registry->set('response', $response);

//Plugins (self assigning to registry)
new Plugin();

//Cron Called from system
if (option('config_cron_status')) {
	if (defined("RUN_CRON")) {
		echo $registry->get('cron')->run();
		exit;
	} //Cron Called from browser
	elseif (isset($_GET['run_cron'])) {
		$result = $registry->get('cron')->run();
		echo nl2br($result);
		exit;
	} //Check if poor man's cron should run
	elseif (option('config_cron_check')) {
		$registry->get('cron')->check();
	}
}

//PHP Info
if (isset($_GET['phpinfo']) && $registry->get('user')->isTopAdmin()) {
	phpinfo();
	exit;
}

//Router
$router->dispatch();

// Output
$response->output();
