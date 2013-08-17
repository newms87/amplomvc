<?php
/**
 * THE ADMIN CONFIGURATION FILE
 */

//Admin
define("IS_ADMIN", true);

// HTTP
define('HTTP_ROOT', SITE_URL . 'admin/');
define('HTTP_CONTENT', SITE_URL . 'admin/');
define('HTTP_ADMIN', SITE_URL . 'admin/');
define('HTTP_CATALOG', SITE_URL);
define('HTTP_IMAGE', SITE_URL . 'image/');
define('HTTP_JS', SITE_URL . 'system/javascript/');
define('HTTP_THEME_JS', HTTP_ADMIN . 'view/javascript/');
define('HTTP_AJAX', HTTP_ADMIN . '');

// HTTPS
define('HTTPS_ROOT', SITE_SSL . 'admin/');
define('HTTPS_IMAGE', SITE_SSL . 'image/');

define('ELFINDER_URL', SITE_URL . 'system/elfinder/');

// DIR
define('DIR_APPLICATION', SITE_DIR . 'admin/');
define('DIR_PLUGIN', SITE_DIR . 'plugin/');
define('DIR_LANGUAGE', SITE_DIR . 'admin/language/');
define('DIR_THEME', SITE_DIR . 'admin/view/theme/');
define('DIR_THEME_OPTION', SITE_DIR . 'admin/view/template_option/');
define('DIR_CATALOG', SITE_DIR . 'catalog/');
define('DIR_GENERATED_IMAGE', SITE_DIR . 'image/generated/');

//SYSTEM DIRECTORIES
define('DIR_EXCEL_TEMPLATE', SITE_DIR . 'system/php-excel/templates/');
define('DIR_EXCEL_FPO', SITE_DIR . 'upload/fpo/');
define('DIR_RESOURCES', SITE_DIR . 'system/resources/');
define('DIR_SYSTEM', SITE_DIR . 'system/');
define('DIR_DATABASE', SITE_DIR . 'system/database/');
define('DIR_DATABASE_BACKUP', SITE_DIR . 'system/database/backups/');
define('DIR_CONFIG', SITE_DIR . 'system/config/');
define('DIR_IMAGE', SITE_DIR . 'image/');
define('DIR_JS', SITE_DIR . 'system/javascript/');
define('DIR_CACHE', SITE_DIR . 'system/cache/');
define('DIR_DOWNLOAD', SITE_DIR . 'download/');
define('DIR_MERGED_FILES', SITE_DIR . 'system/plugins/merged/');
define('DIR_LOGS', SITE_DIR . 'system/logs/');
