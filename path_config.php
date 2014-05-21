<?php
//Urls
define('URL_THEMES', URL_SITE . 'app/view/theme/');

//TODO: Remove URL_AJAX after removing ckeditor
define('URL_AJAX', URL_SITE . 'ajax/');

//Directories
define('DIR_PLUGIN', DIR_SITE . 'plugin/');
define('DIR_FORM', DIR_SITE . 'app/view/form/');
define('DIR_THEMES', DIR_SITE . 'app/view/theme/');
define('DIR_SYSTEM', DIR_SITE . 'system/');

define('DIR_EXCEL_TEMPLATE', DIR_SITE . 'system/php-excel/templates/');
define('DIR_EXCEL_FPO', DIR_SITE . 'upload/fpo/');
define('DIR_CRON', DIR_SITE . 'system/cron/');
define('DIR_CACHE', DIR_SITE . 'system/cache/');
define('DIR_MOD_FILES', DIR_SITE . 'system/mods/');
define('DIR_DATABASE', DIR_SITE . 'system/database/');

if (!defined('DIR_DATABASE_BACKUP')) {
	define('DIR_DATABASE_BACKUP', DIR_SITE . 'system/database/backups/');
}


//Log Files
define("AC_LOG_FILE", DIR_LOGS . 'log.txt');
define("AC_LOG_ERROR_FILE", DIR_LOGS . 'error.txt');
