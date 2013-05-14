<?php
// Heading
$_['heading_title']    = 'Plugins';

//Data
$_['call_when'] = array('before'=>"Before", 'replace'=>"Instead of", 'after'=>"After");
$_['types'] = array('controller'=>"Controller", 'model'=>"Model");
$_['base_types'] = array('admin'=>'Admin', 'system'=>"System", 'catalog'=>"Catalog");

// Column
$_['column_name']      = 'Plugin Name';
$_['column_action']    = 'Action';

$_['entry_function']   = 'Function Name:';
$_['entry_plugin_path']   = 'Plugin Class Path:';
$_['entry_class_path']   = 'Path of Class to Plug Into:<span class="help">(eg: common/home, etc.)</span>';
$_['entry_hooks']   = 'Hook Functions:';
$_['entry_base_type']   = 'Base Folder:';
$_['entry_route']   = 'Plugin For Route:';
$_['entry_type']   = 'Plugin Type';
$_['entry_status']   = 'Status';
$_['entry_hook_method']   = 'Call Plugin Method';
$_['entry_hook_for']   = 'Invoking';

$_['tab_admin']        = 'Admin Plugins';
$_['tab_catalog']        = 'Catalog Plugins';

$_['button_add_plug'] = 'Add Plug';
$_['button_add_hook'] = 'Add Hook';
$_['button_hook_remove'] = 'Remove Hook';

$_['text_success'] = "You have successfully updated the plugins!";

$_['text_install'] = "Install Plugin";
$_['text_uninstall'] = "Uninstall";

$_['success_install'] = "%s was successfully installed!";

// Error
$_['error_permission'] = 'Warning: You do not have permission to modify plugins!';
$_['error_no_plugin'] = 'Warning: There was no plugin found.';
$_['error_plug_into'] = 'You must specify the "Plugin For" for each plug.';
$_['error_plugin_method'] = 'The function %s does not exist in the class %s.';
$_['error_class_path'] = 'The Class File %s does not exist.';
$_['error_install_function'] = "There was a problem installing %s. The plugin setup file did not contain an install() function!";
$_['error_install_file'] = "The plugin setup file was not found at %s. Please make a setup.php file in the root of the %s plugin directory!";
$_['error_uninstall_file'] = "The plugin setup file was not found at %s. Please make a setup.php file in the root of the %s plugin directory!";