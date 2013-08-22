<?

if (!defined("AMPLOCART_INSTALL")) {
	echo "Please call the Amplo Cart index.php in your installation root directory.";
	exit;
}

$language = (!empty($_GET['language']) && is_file($_GET['language'] . '.php')) ? $_GET['language'] : 'english';

$_ = array();

require_once($language . '.php');

extract($_);

$template = !empty($_GET['page']) ? $_GET['page'] : 'db';
$error_msg = '';
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
	switch ($_POST['action']) {
		case 'db_setup':
			$result = setup_db($_);

			if ($result === true) {
				$success_msg = $_['success_db'];
				$template = 'user';
			} else {
				$error_msg = $result;
			}
			break;

		case 'user_setup':
			$template = 'user';
			$result = setup_user($_);

			if ($result === true) {
				$success_msg = $_['success_user'];
			} else {
				$error_msg = $result;
			}
			break;

	}

}

switch ($template) {
	case 'db':
		$defaults = array(
			'db_type' => 'mysqlidb',
			'db_host' => '',
			'db_name' => '',
			'db_username' => '',
			'db_password' => '',
			'db_prefix' => 'ac_',
		);
		break;

	case 'user':
		$defaults = array(
			'username'	=> '',
			'email'		=> '',
			'password'	=> '',
			'confirm'	=> '',
		);
		break;

	default:
		echo "Unknown installation page!";
		exit;
}

foreach ($defaults as $key => $default) {
	if (isset($_POST[$key])) {
		$$key = $_POST[$key];
	} else {
		$$key = $default;
	}
}


$logo = "image/data/ac_logo.png";

$db_types = array(
	'mysqlidb'	=> "MySQL",
	'mmsql'		=> "MMSQL",
	'odbc'		=> "ODBC",
	'postgre'	=> "Postgre",
	'sqlite'		=> "SQLite",
);

require_once("system/install/install_{$template}.tpl");


function setup_db($_) {
	define("SITE_DIR", str_replace('system/install','', rtrim(str_replace('\\','/',dirname(__FILE__)), '/')));
	define("DIR_DATABASE", SITE_DIR . 'system/database/');
	define("DB_PREFIX", $_POST['db_prefix']);

	require_once(DIR_DATABASE . "database.php");
	require_once(SITE_DIR . "system/library/db.php");

	$db = @new DB($_POST['db_type'], $_POST['db_host'], $_POST['db_username'], $_POST['db_password'], $_POST['db_name']);

	$error = $db->getError();

	if ($error) {
		return $error;
	}

	$db_prefix = DB_PREFIX;

	$db_sql = SITE_DIR . 'system/install/db.sql';

	$contents = file_get_contents($db_sql);

	$contents = str_replace("%__TABLE_PREFIX__%", DB_PREFIX, $contents);

	if (!$db->multiquery($contents)) {
		return $db->getError();
	}

	$config_template = SITE_DIR . 'system/install/config_template.php';
	$ac_config = SITE_DIR . 'ac_config.php';

	$contents = file_get_contents($config_template);

	$patterns = array(
		"/%site_url%/",
		'/%site_ssl%/',
		'/%db_type%/',
		'/%db_name%/',
		'/%db_host%/',
		'/%db_username%/',
		'/%db_password%/',
		'/%db_prefix%/',
		'/%time_zone_name%/',
		'/%time_zone%/',
	);

	$url = $_SERVER["SERVER_NAME"];

	if ($_SERVER["SERVER_PORT"] !== "80") {
		$url .= ":".$_SERVER["SERVER_PORT"];
	}

	if (strpos($_SERVER['REQUEST_URI'],'index.php')) {
		$uri = dirname($_SERVER["REQUEST_URI"]);
	} else {
		$uri = $_SERVER['REQUEST_URI'];
	}

	$url .= rtrim($uri, '/') . '/';

	$replacements = array(
		'http://' . $url,
		'https://' . $url,
		$_POST['db_type'],
		$_POST['db_name'],
		$_POST['db_host'],
		$_POST['db_username'],
		$_POST['db_password'],
		$_POST['db_prefix'],
		"America/New_York",
		"-4:00",
	);

	$contents = preg_replace($patterns, $replacements, $contents);

	file_put_contents($ac_config, $contents);

	//Setup .htaccess file
	$htaccess_template = SITE_DIR . 'system/install/template.htaccess';
	$htaccess = SITE_DIR . '.htaccess';

	$contents = file_get_contents($htaccess_template);

	$contents = preg_replace("/%base%/", $uri, $contents);

	file_put_contents($htaccess, $contents);

	return true;
}

function setup_user($_) {
	if ($_POST['password'] !== $_POST['confirm']) {
		$_POST['password'] = $_POST['confirm'] = '';

		return $_['error_password_confirm'];
	}
	require_once("ac_config.php");
	require_once(SITE_DIR . "system/library/db.php");

	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

	$username = $db->escape($_POST['username']);
	$email = $db->escape($_POST['email']);
	$password = $db->escape(md5($_POST['password']));
	$ip = $_SERVER['REMOTE_ADDR'];
	$date_added = date('Y-m-d H:i:s', time());

	$db->query("DELETE FROM " . DB_PREFIX . "user WHERE email = '$email' OR username = '$username'");
	$db->query("INSERT INTO " . DB_PREFIX . "user SET user_group_id = '1', firstname = 'Admin', username = '$username', email = '$email', password = '$password', ip = '$ip', status = '1', date_added = '$date_added'");

	if ($db->getError()) {
		return $db->getError();
	}

	$ac_config = SITE_DIR . 'ac_config.php';

	//remove user install configuration
	$contents = file_get_contents($ac_config);

	$contents = str_replace("define(\"AMPLOCART_INSTALL_USER\", 1);", '', $contents);

	file_put_contents($ac_config, $contents);

	//Start the session so we can send a message for the new user
	ini_set('session.use_cookies', 'On');
	ini_set('session.use_trans_sid', 'Off');
	session_name(AMPLOCART_SESSION);
	session_set_cookie_params(0, '/', COOKIE_DOMAIN);
	session_start();

	$_SESSION['messages'] = array(
		'success' => array($_['success_user']),
	);

	header("Location: " . SITE_URL . 'admin');
}

exit;