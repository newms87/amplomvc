<?php
/**
 *  See system/startup.php for additional config options that can be overridden.
 *
 * For Developers, some useful defines:
 * define('AMPLO_TIME_LOG', true); - Enables Performance logging w/ the dev plugin.
 * define('DB_PROFILE', true); - Enables performance logging on all DB queries w/ the dev plugin.
 */

//This is the path to Amplo MVC from the site's root directory. If it is in the root make this '/'
define('SITE_BASE', '/');

//ROOT DIRECTORY
define('DIR_SITE', str_replace('\\', '/', dirname(__FILE__) . '/'));

//The default page to show when your site is accessed via the base URL. (eg: http://your-domain.com/)
define("DEFAULT_PATH", 'index');

//The page to show when no page controller is found (eg: https://your-domain.com/unknown-url)
define("ERROR_404_PATH", 'error/not_found');

// SERVER SETUP
define('DEFAULT_TIMEZONE', 'America/Denver');
define('MYSQL_TIMEZONE', '-6:00');

// Database Config
define('DB_DRIVER', 'mysqlidb');
define('DB_HOSTNAME', 'localhost');
define('DB_DATABASE', 'myschema');
define('DB_USERNAME', 'dbuser');
define('DB_PASSWORD', 'complex-and-long-db-password');
define('DB_PREFIX', 'am_');

//Error Reporting
//You should always leave this at E_ALL unless you have a good reason not to
error_reporting(E_ALL);
//This should be set to 0 for production
ini_set('display_errors', 1);

//Rewrites <?= PHP tags to <?php echo. Only set this to true if your server does not allow <?= short tags.
define('AMPLO_REWRITE_SHORT_TAGS', false);

//Set umask for directories
umask(0022);

//Password Hashing
define("PASSWORD_COST", 12);
