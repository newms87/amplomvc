<?php
//Site Domain
define('DOMAIN', $_SERVER['HTTP_HOST']);

//This is the path to Amplo MVC from the site's root directory. If it is in the root make this '/'
define('SITE_BASE', '/');

//Site Urls
define('URL_SITE', '//' . DOMAIN . SITE_BASE);
define('HTTP_SITE', 'http://' . DOMAIN . SITE_BASE);

//If your SSL site is on a different domain, modify this entry
define('HTTPS_SITE', 'https://' . DOMAIN . SITE_BASE);

//Path Setup
define("DEFAULT_PATH", 'common/home');
define("ERROR_404_PATH", 'error/not_found');

// SERVER SETUP
define('DEFAULT_TIMEZONE', 'America/Denver');
define('MYSQL_TIMEZONE', '-6:00');

// DB
define('DB_DRIVER', 'mysqlidb');
define('DB_HOSTNAME', 'localhost');
define('DB_DATABASE', 'caddash');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_PREFIX', 'ac_');

//DB Profiling
define("DB_PROFILE", true);
define("DB_PROFILE_NO_CACHE", false);

//Time logging for system performance profiling
define('AMPLO_TIME_LOG', false);

//Error Reporting
//You should always leave this at E_ALL unless you have a good reason not to
error_reporting(E_ALL);
//This should be set to 0 for production
ini_set('display_errors', 1);

//Cache
define('CACHE_FILE_EXPIRATION', 3600);

//File permissions
define('AMPLO_DIR_MODE', 0755);
define('AMPLO_FILE_MODE', 0644);
define('DEFAULT_PLUGIN_DIR_MODE', 0755);
define('DEFAULT_PLUGIN_FILE_MODE', 0644);

//Set umask for directories
umask(0022);

//This allows for cross store sessions
define("AMPLO_SESSION", "cross-store-session");

//Use this to set the timeout for a user session in seconds (will log a user out after x seconds)
//Default: 3600 seconds (1 hour)
define('AMPLO_SESSION_TIMEOUT', 3600);

//Password Hashing
define("PASSWORD_COST", 12);

//ROOT DIRECTORY
define('DIR_SITE', str_replace('\\', '/', dirname(__FILE__) . '/'));

//Urls
define('URL_RESOURCES', URL_SITE . 'system/resources/');
define('URL_IMAGE', URL_SITE . 'image/');
define('URL_DOWNLOAD', URL_SITE . 'download/');

//Directories
define('DIR_RESOURCES', DIR_SITE . 'system/resources/');
define('DIR_IMAGE', DIR_SITE . 'image/');
define('DIR_LOGS', DIR_SITE . 'system/logs/');
define('DIR_DOWNLOAD', DIR_SITE . 'download/');
define('DIR_CACHE', DIR_SITE . 'system/cache/');

