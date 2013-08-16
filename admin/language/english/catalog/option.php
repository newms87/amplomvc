<?php
// Heading
$_['head_title'] = 'Options';

//Data
$_['data_option_types'] = array(
	'#optgroup1' => 'Choose',
	'select'     => 'Select',
	'radio'      => 'Radio',
	'checkbox'   => 'Checkbox',
	'image'      => 'Image',
	'#optgroup2' => 'Input',
	'text'       => 'Text',
	'textarea'   => 'Textarea',
	'#optgroup4' => 'Date',
	'date'       => 'Date',
	'datetime'   => 'Date &amp; Time',
	'time'       => 'Time'
);

// Text
$_['text_success']                 = 'Success: You have modified options!';
$_['text_add_option_autocomplete'] = ' + Add Option';

// Column
$_['column_name']       = 'Option Name';
$_['column_sort_order'] = 'Sort Order';
$_['column_action']     = 'Action';

// Entry
$_['entry_name']         = 'Option Name:';
$_['entry_display_name'] = 'Option Display Name:';
$_['entry_type']         = 'Type:';
$_['entry_value']        = 'Option Value Name:';
$_['entry_image']        = 'Image:';
$_['entry_sort_order']   = 'Sort Order:';

// Error
$_['error_permission']   = 'Warning: You do not have permission to modify options!';
$_['error_name']         = 'Option Name must be between 3 and 45 characters!';
$_['error_display_name'] = 'Option Display Name must be between 1 and 128 characters!';
$_['error_type']         = 'Warning: Option Values required!';
$_['error_option_value'] = 'Option Value must be between 1 and 128 characters!';
$_['error_product']      = 'Warning: This option cannot be deleted as it is currently assigned to %s products!';
