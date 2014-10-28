<?php
/* Site Domain and URL setup
 * Only override these if you need to setup your site manually. These values are determined in system/startup.php.
*/
//This is the path to Amplo MVC from the site's root directory. If it is in the root make this '/'
define('SITE_BASE', '/');

//Site Domain
//define('DOMAIN', $_SERVER['HTTP_HOST']);

//Site Urls
//define('URL_SITE', '//' . DOMAIN . SITE_BASE);
//define('HTTP_SITE', 'http://' . DOMAIN . SITE_BASE);

//If your SSL site is on a different domain, modify this entry
//define('HTTPS_SITE', 'https://' . DOMAIN . SITE_BASE);

//If your images and download files are stored somewhere else on this domain or a different domain, specify the root directories here.
//define('URL_IMAGE', 'http://example.com/image/');
//define('URL_DOWNLOAD', 'http://example.com/download/');

//ROOT DIRECTORY
define('DIR_SITE', str_replace('\\', '/', dirname(__FILE__) . '/'));

//Directories
define('DIR_IMAGE', DIR_SITE . 'image/');
define('DIR_DOWNLOAD', DIR_SITE . 'download/');
define('DIR_LOGS', DIR_SITE . 'system/logs/');
define('DIR_CACHE', DIR_SITE . 'system/cache/');

//The default page to show when your site is accessed via the base URL. (eg: http://your-domain.com/)
define("DEFAULT_PATH", 'common/home');

//The page to show when no page controller is found (eg: https://your-domain.com/unknown-url)
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
define("DB_PROFILE", false);
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

//Use this to set the timeout for a user session in seconds (will log a user out after x seconds)
//Default: 3600 seconds (1 hour)
define('AMPLO_SESSION_TIMEOUT', 3600);

//Password Hashing
define("PASSWORD_COST", 12);
