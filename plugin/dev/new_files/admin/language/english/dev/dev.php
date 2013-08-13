<?php
// Heading
$_['head_title']	= 'Development Console';

//Data
$_['data_site_status'] = array(
								'live' => "Live Site",
								'dev' => "Development Site",
								'inactive' => "Inactive Site",
							);
							
// Text
$_['text_sync_site']		= 'Sync Sites';
$_['text_sync_tables']		= 'Sync Tables';
$_['text_sync']				= 'Synchronize Sites';
$_['text_site_management']	= 'Site Management';
$_['text_backup_restore']	= 'Backup & Restore';
$_['text_db_admin']	= 'Database Administration';

//Console
$_['console_sync'] = "Synchronize Sites";
$_['console_site_management'] = "Site Management";
$_['console_backup_restore'] = "Site Backup & Restore";
$_['console_db_admin'] = "DB Admin";

//Column
$_['column_domain'] = "Domain";
$_['column_username'] = "Username";
$_['column_status'] = "Site Status";

// Entry
$_['entry_sync_table']	= 'Tables:';
$_['entry_domain']	= 'Site Domain: <span class="help">(eg: www.yourdomain.com)</span>';
$_['entry_username']	= 'Username:';
$_['entry_password']	= 'Password:';
$_['entry_status']	= 'Site Status:';
$_['entry_backup']	= 'Database Backup:';
$_['entry_restore']	= 'Restore Site From Backup:';
$_['entry_execute_file']	= 'Execute DB Script File:';


//Button
$_['button_add_site'] = "Add Site";
$_['button_sync'] = "Request Synchronization";
$_['button_return'] = "Back To Dashboard";
$_['button_backup'] = "Backup Database";
$_['button_execute_file'] = "Execute File";
$_['button_submit_query'] = "Execute Query";
$_['button_download'] = "Download Backup";

//Success
$_['success_sync_table'] = 'The table %s was successfully synchronized!';

// Error
$_['error_permission'] = 'Warning: You do not have permission to use the development console!';
$_['error_sync_table'] = 'There was a problem while synchronizing from the server.';
$_['error_download_backup_file'] = 'Please select a backup file to download.';
