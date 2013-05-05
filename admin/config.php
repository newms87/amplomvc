<?php
/**
 * THE ADMIN CONFIGURATION FILE
 */
 //This is a hack to allow config file to be found from elfinder imagemanager system (and possibly other systems)
if(is_file('../oc_config.php')){
   require_once('../oc_config.php');
}
elseif(is_file('../../oc_config.php')){
   require_once('../../oc_config.php');
}
else{
   require_once('../../../oc_config.php');
}

//Admin
define("IS_ADMIN",true);

// HTTP
define('HTTP_SERVER', SITE_URL . 'admin/');
define('HTTP_CATALOG', SITE_URL);
define('HTTP_IMAGE', HTTP_CATALOG . 'image/');
define('HTTP_STYLES', SITE_URL . 'admin/view/stylesheet/');

// HTTPS
define('HTTPS_SERVER', HTTP_SERVER);
define('HTTPS_IMAGE', HTTP_IMAGE);

define('ELFINDER_URL', SITE_URL. 'system/elfinder/');

// DIR
define('DIR_APPLICATION', SITE_DIR . 'admin/');
define('DIR_PLUGIN', SITE_DIR . 'plugin/');
define('DIR_LANGUAGE', SITE_DIR . 'admin/language/');
define('DIR_TEMPLATE', SITE_DIR . 'admin/view/template/');
define('DIR_TEMPLATE_OPTION', SITE_DIR . 'admin/view/template_option/');
define('DIR_CATALOG', SITE_DIR . 'catalog/');
define('DIR_GENERATED_IMAGE', SITE_DIR . 'image/generated/');