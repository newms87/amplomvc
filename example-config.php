<?php
/**
 *  See system/startup.php for additional config options that can be overridden.
 *
 * For Developers, some useful defines:
 * define('AMPLO_TIME_LOG', true); - Enables Performance logging w/ the dev plugin.
 * define('AMPLO_PROFILE', true); - Enables performance logging on all DB queries w/ the dev plugin.
 */

//Set this to 1 for production environments
define("AMPLO_PRODUCTION", 0);

//This is the path to Amplo MVC from the site's root directory. If it is in the root make this '/'
define('SITE_BASE', '/');

//ROOT DIRECTORY
define('DIR_SITE', str_replace('\\', '/', dirname(__FILE__) . '/'));

//The Homepage. Can be changed via General Settings. Use this to override. (eg: http://your-domain.com/)
//$_options['homepage_path'] = 'index';

//The Error 404 Not Found page. Can be changed via General Settings. Use this to override. (eg: https://your-domain.com/unknown-url)
//$_options['error_404_path'] = 'error/not_found';

//Cookie Prefix prevents cookie conflicts across top level domain to sub domain (ex: .example.com and .sub.example.com)
// and for different sites on same domain with different in different directories (ex: example.com/site-a and example.com/site-b)
define('COOKIE_PREFIX', '');

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
ini_set('display_errors', AMPLO_PRODUCTION ? 0 : 1);

//Rewrites <?= PHP tags to <?php echo. Only set this to true if your server does not allow <?= short tags.
define('AMPLO_REWRITE_SHORT_TAGS', false);

//Set umask for directories
umask(0022);

//Password Hashing
define("PASSWORD_COST", 12);
define("AMPLO_SECRET_KEY", 'abc-123-this-is-my-secret-key!#$%');
