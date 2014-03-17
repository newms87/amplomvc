<?php
//Site Domain
define('DOMAIN', '%domain%');

//Site Urls
define('URL_SITE', '//' . DOMAIN . '/');
define('HTTP_SITE', 'http://' . DOMAIN . '/');

//If your SSL site is on a different domain, modify this entry
define('HTTPS_SITE', 'https://' . DOMAIN . '/');

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
define('DIR_SITE', str_replace('\\', '/', dirname(__FILE__) . '/'));

//Urls
define('URL_RESOURCES', URL_SITE . 'system/resources/');
define('URL_IMAGE', URL_SITE . 'image/');

//Directories
define('DIR_RESOURCES', DIR_SITE . 'system/resources/');
define('DIR_IMAGE', DIR_SITE . 'image/');
define('DIR_LOGS', DIR_SITE . 'system/logs/');
define('DIR_DOWNLOAD', DIR_SITE . 'download/');
