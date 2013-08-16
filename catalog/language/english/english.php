<?php
// Text
$_['text_home']                = 'Home';
$_['text_yes']                 = 'Yes';
$_['text_no']                  = 'No';
$_['text_enable']              = 'Enable';
$_['text_disable']             = 'Disable';
$_['text_enabled']             = 'Enabled';
$_['text_disabled']            = 'Disabled';
$_['text_none']                = ' --- None --- ';
$_['text_select']              = ' --- Please Select --- ';
$_['text_all_zones']           = 'All Zones';
$_['text_pagination']          = 'Showing {start} to {end} of {total} ({pages} Pages)';
$_['text_separator']           = ' &raquo; ';
$_['text_submit_payment']      = '<span style="font-size:16px;font-weight:bold">Please wait while we process your order.</span><br />You will be redirected after the order has been submitted. Thank you!';
$_['text_submit_payment_done'] = 'Complete! You will now be redirected.';

//Data
$_['data_statuses']       = $_['statuses'] = array(
	0 => $_['text_disabled'],
	1 => $_['text_enabled']
);
$_['data_statuses_blank'] = array('' => '') + $_['data_statuses'];
$_['data_yes_no']         = array(
	1 => $_['text_yes'],
	0 => $_['text_no']
);
$_['data_no_yes']         = array(
	0 => $_['text_no'],
	1 => $_['text_yes']
);
$_['data_yes_no_blank']   = array('' => '') + $_['data_yes_no'];
$_['data_no_yes_blank']   = array('' => '') + $_['data_no_yes'];

// Buttons
$_['button_add_address']    = 'Add Address';
$_['button_back']           = 'Back';
$_['button_continue']       = 'Continue';
$_['button_buy_now']        = 'Buy Now';
$_['button_cart']           = 'Add to Cart';
$_['button_compare']        = 'Add to Compare';
$_['button_wishlist']       = 'Add to Wish List';
$_['button_checkout']       = 'Checkout';
$_['button_confirm']        = 'Confirm Order';
$_['button_coupon']         = 'Apply Coupon';
$_['button_delete']         = 'Delete';
$_['button_download']       = 'Download';
$_['button_edit']           = 'Edit';
$_['button_new_address']    = 'New Address';
$_['button_change_address'] = 'Change Address';
$_['button_reviews']        = 'Reviews';
$_['button_write']          = 'Write Review';
$_['button_login']          = 'Login';
$_['button_update']         = 'Update';
$_['button_remove']         = 'Remove';
$_['button_reorder']        = 'Reorder';
$_['button_return']         = 'Return';
$_['button_shopping']       = 'Continue Shopping';
$_['button_search']         = 'Search';
$_['button_shipping']       = 'Apply Shipping';
$_['button_guest']          = 'Guest Checkout';
$_['button_view']           = 'View';
$_['button_voucher']        = 'Apply Voucher';
$_['button_upload']         = 'Upload File';
$_['button_reward']         = 'Apply Points';
$_['button_quote']          = 'Get Quotes';
$_['button_close']          = 'Close';

$_['final_sale_explanation'] = "A Product Marked as <span class='final_sale'></span> cannot be returned. Read our <a href=\"%s\" onclick=\"return colorbox($(this));\">Return Policy</a> for details.";
