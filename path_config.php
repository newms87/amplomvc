<?php
define('HTTP_SERVER',SITE_URL);

// HTTP
define('HTTP_IMAGE', HTTP_SERVER .'image/');
define('HTTP_ADMIN', HTTP_SERVER . 'admin/');

// HTTPS
define('HTTPS_SERVER', HTTP_SERVER);
define('HTTPS_IMAGE', HTTP_SERVER . 'image/');

define('ELFINDER_URL', SITE_URL . 'system/elfinder/');

define('DIR_APPLICATION', SITE_DIR . 'catalog/');
define('DIR_PLUGIN', SITE_DIR . 'plugin/');
define('DIR_LANGUAGE', SITE_DIR . 'catalog/language/');
define('DIR_TEMPLATE', SITE_DIR . 'catalog/view/theme/');
define('DIR_TEMPLATE_OPTION', SITE_DIR . 'catalog/view/template_option/');
define('DIR_CONTROLLER', SITE_DIR . 'catalog/controller/');
define('DIR_GENERATED_IMAGE', SITE_DIR . 'image/generated/');
   