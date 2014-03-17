<?php
/**
 * THE ADMIN CONFIGURATION FILE
 */

//Admin
define("IS_ADMIN", true);

//Url Constants
define('URL_THEMES', URL_SITE . 'admin/view/theme/');
define('URL_ELFINDER', URL_SITE . 'system/elfinder/');

//TODO: Remove URL_AJAX after removing ckeditor
define('URL_AJAX', URL_SITE . 'admin/');


// Directory Constants
define('DIR_PLUGIN', DIR_SITE . 'plugin/');
define('DIR_FORM', DIR_SITE . 'catalog/view/form/');
define('DIR_THEMES', DIR_SITE . 'admin/view/theme/');

//SYSTEM DIRECTORIES
define('DIR_EXCEL_TEMPLATE', DIR_SITE . 'system/php-excel/templates/');
define('DIR_EXCEL_FPO', DIR_SITE . 'upload/fpo/');
define('DIR_SYSTEM', DIR_SITE . 'system/');
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
