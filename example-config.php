<?php
//Site Domain
define('DOMAIN', $_SERVER['HTTP_HOST']);
define('SITE_BASE', '/dev/');

//Site Urls
define('URL_SITE', '//' . DOMAIN . SITE_BASE);
define('HTTP_SITE', 'http://' . DOMAIN . SITE_BASE);

//If your SSL site is on a different domain, modify this entry
define('HTTPS_SITE', 'https://' . DOMAIN . SITE_BASE);

//Path Setup
define("DEFAULT_PATH", 'common/home');
define("ERROR_404_PATH", 'error/not_found');

// SERVER SETUP
define('DEFAULT_TIMEZONE', 'America/New_York');
define('MYSQL_TIMEZONE', '-4:00');

// DB
define('DB_DRIVER', 'mysqlidb');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'cadscope');
define('DB_PREFIX', 'ac_');

//Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

//Cache
define('CACHE_FILE_EXPIRATION', 3600);

//File permissions
define('AMPLOCART_DIR_MODE', 0755);
define('AMPLOCART_FILE_MODE', 0644);
define('DEFAULT_PLUGIN_DIR_MODE', 0755);
define('DEFAULT_PLUGIN_FILE_MODE', 0644);

//Set umask for directories
umask(0022);

//This allows for cross store sessions
define("AMPLOCART_SESSION", "cross-store-session");

//Password Hashing
define("PASSWORD_COST", 12);

//ROOT DIRECTORY
define('DIR_SITE', str_replace('\\', '/', dirname(__FILE__) . '/'));

//Urls
define('URL_RESOURCES', URL_SITE . 'system/resources/');
define('URL_IMAGE', URL_SITE . 'image/');

//Directories
define('DIR_RESOURCES', DIR_SITE . 'system/resources/');
define('DIR_IMAGE', DIR_SITE . 'image/');
define('DIR_LOGS', DIR_SITE . 'system/logs/');
define('DIR_DOWNLOAD', DIR_SITE . 'download/');

