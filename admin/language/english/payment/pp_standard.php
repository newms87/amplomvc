<?php
// Heading
$_['heading_title']					= 'PayPal Standard';

// Text
$_['text_payment']					= 'Payment';
$_['text_success']					= 'Success: You have modified PayPal account details!';
$_['text_pp_standard']				= '<a onclick="window.open(\'https://www.paypal.com/uk/mrb/pal=W9TBB5DTD6QJW\');"><img src="view/theme/default/image/payment/paypal.png" alt="PayPal" title="PayPal" style="border: 1px solid #EEEEEE;" /></a>';

//Data
$_['data_auth_sale']	= array(
	0 => "Authorization",
	1 => "Sale",
);

// Entry
$_['entry_email']					= 'E-Mail:';
$_['entry_test_email']					= 'Test Account E-Mail: <span class="help">(if different than your primary account)</span>';
$_['entry_test']					= 'Sandbox Mode:';
$_['entry_transaction']				= 'Transaction Method:';
$_['entry_debug']					= 'Debug Mode:<br/><span class="help">Logs additional information to the system log.</span>';
$_['entry_total']						= 'Total:<br /><span class="help">The checkout total the order must reach before this payment method becomes active.</span>';
$_['entry_canceled_reversal_status'] = 'Canceled Reversal Status:';
$_['entry_completed_status']			= 'Completed Status:';
$_['entry_denied_status']			= 'Denied Status:';
$_['entry_expired_status']			= 'Expired Status:';
$_['entry_failed_status']			= 'Failed Status:';
$_['entry_pending_status']			= 'Pending Status:';
$_['entry_processed_status']		= 'Processed Status:';
$_['entry_refunded_status']			= 'Refunded Status:';
$_['entry_reversed_status']			= 'Reversed Status:';
$_['entry_voided_status']			= 'Voided Status:';
$_['entry_geo_zone']				= 'Geo Zone:';
$_['entry_status']					= 'Status:';
$_['entry_sort_order']				= 'Sort Order:';
$_['entry_page_style']				= 'Page Style: <span class="help">Enter \'primary\' for the primary style set on your Paypal account, or enter the name of the style as you named it on your paypal account. Leave blank to use the default</span>';
$_['entry_pdt_enabled']		= "PDT is enabled?<span class=\"help\">This will allow the user to be instantly returned to your site after payment.</span>";
$_['entry_pdt_enabled_help'] = "To enabled PDT on your account you must <a target=\"_blank\" href=\"http://www.paypal.com/\">login to your paypal account</a>.<br />Go to Profile > Website payments preferences.<br />From here enable PDT and Auto Return.";
$_['entry_pdt_token'] = "PDT Identity Token";

// Error
$_['error_permission']				= 'Warning: You do not have permission to modify payment PayPal!';
$_['error_email']					= 'E-Mail required!';
$_['error_pdt_token'] = "PDT Token is required to enable Payment Data Transfer!";
