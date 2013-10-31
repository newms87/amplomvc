<?php
/*
 * By Setting the SITE_URL & SITE_SSL we are limiting the domain to these specific URL's (this is probably what we want)
 * Alternatively, we can dynamically set these (based on waht the user requests), then we can modify the URL based on the DB
 * entries for each store.
 */
//SITE URL
define('SITE_URL', '%site_url%');
define('SITE_SSL', '%site_ssl%');

// SERVER SETUP
define('DEFAULT_TIMEZONE', '%time_zone_name%');
define('MYSQL_TIMEZONE', '%time_zone%');
date_default_timezone_set(DEFAULT_TIMEZONE);

// DB
define('DB_DRIVER', '%db_type%');
define('DB_HOSTNAME', '%db_host%');
define('DB_USERNAME', '%db_username%');
define('DB_PASSWORD', '%db_password%');
define('DB_DATABASE', '%db_name%');
define('DB_PREFIX', '%db_prefix%');

//For MYSQLDUMP (typically for Windows environments)
//define("DB_MYSQLDUMP_FILE", "C:/Program Files (x86)/MySQL/MySQL Workbench 5.2 CE/mysqldump.exe");
//define("DB_MYSQL_FILE", "C:/Program Files (x86)/MySQL/MySQL Workbench 5.2 CE/mysql.exe");

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
define("PASSWORD_COST", %password_cost%);

//ROOT DIRECTORY
define('SITE_DIR', str_replace('\\','/',dirname(__FILE__) . '/'));
