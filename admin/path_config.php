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
define('HTTP_AJAX', HTTP_ADMIN . 'ajax/');

// HTTPS
define('HTTPS_ROOT', SITE_SSL . 'admin/');
define('HTTPS_IMAGE', SITE_SSL . 'image/');

define('ELFINDER_URL', SITE_URL. 'system/elfinder/');

// DIR
define('DIR_APPLICATION', SITE_DIR . 'admin/');
define('DIR_PLUGIN', SITE_DIR . 'plugin/');
define('DIR_LANGUAGE', SITE_DIR . 'admin/language/');
define('DIR_THEME', SITE_DIR . 'admin/view/theme/');
define('DIR_THEME_OPTION', SITE_DIR . 'admin/view/template_option/');
define('DIR_CATALOG', SITE_DIR . 'catalog/');
define('DIR_GENERATED_IMAGE', SITE_DIR . 'image/generated/');