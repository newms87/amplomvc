<?php
// Heading
$_['head_title']	= 'Language';

//Data
$_['data_direction']	= array('ltr'=>"Left to Right", 'rtl'=>"Right to Left");
$_['data_statuses']		= array(-1 => 'Disabled', 0 => 'Inactive', 1 => 'Active');

// Text
$_['text_success']		= 'Success: You have modified languages!';

// Column
$_['column_name']		= 'Language Name';
$_['column_code']		= 'Code';
$_['column_sort_order'] = 'Sort Order';
$_['column_action']	= 'Action';

// Entry
$_['entry_name']		= 'Language Name:';
$_['entry_code']		= 'Code:<br /><span class="help">eg: en. Do not change if this is your default language.</span>';
$_['entry_locale']		= 'Locale:<br /><span class="help">eg: en_US.UTF-8,en_US,en-gb,en_gb,english</span>';
$_['entry_datetime_format']		= 'Datetime Format:';
$_['entry_date_format_short'] = 'Short Date Format:';
$_['entry_date_format_long'] = 'Long Date Format:';
$_['entry_time_format'] = 'Time Format:';
$_['entry_decimal_point'] = 'Decimal Point:';
$_['entry_thousand_point'] = 'Thousand Point:';
$_['entry_direction'] = 'Reading Direction:';
$_['entry_image']		= 'Image:<br /><span class="help">eg: gb.png</span>';
$_['entry_directory']	= 'Directory:<br /><span class="help">name of the language directory (case-sensitive)</span>';
$_['entry_filename']	= 'Filename:<br /><span class="help">main language filename without extension</span>';
$_['entry_status']		= 'Status:<br /><span class="help">Hide/Show it in language dropdown</span>';
$_['entry_sort_order']  = 'Sort Order:';

// Error
$_['error_permission']  = 'Warning: You do not have permission to modify languages!';
$_['error_name']		= 'Language Name must be between 3 and 32 characters!';
$_['error_code']		= 'Language Code must at least 2 characters!';
$_['error_locale']		= 'Locale required!';
$_['error_image']		= 'Image Filename must be between 3 and 64 characters!';
$_['error_directory']	= 'Directory required!';
$_['error_filename']	= 'Filename must be between 3 and 64 characters!';
$_['error_default']	= 'Warning: This language cannot be deleted as it is currently assigned as the default store language!';
$_['error_admin']		= 'Warning: This Language cannot be deleted as it is currently assigned as the administration language!';
$_['error_store']		= 'Warning: This language cannot be deleted as it is currently assigned to %s stores!';
$_['error_order']		= 'Warning: This language cannot be deleted as it is currently assigned to %s orders!';