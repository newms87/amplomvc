<?php
// Text
$_['text_title']              = 'PayPal';
$_['text_reason']             = 'REASON';
$_['text_testmode']           = 'Warning: The Payment Gateway is in \'Sandbox Mode\'. Your account will not be charged.';
$_['text_total']              = 'Shipping, Handling, Discounts & Taxes';
$_['text_processing_payment'] = '<span style="font-size:16px;font-weight:bold">Please complete your payment on Paypal.</span><br />You will be redirected after the order has been submitted. Thank you!<br /><br />If you did not see a paypal window, or you closed the paypal window,<br />use the button below to try again.';
$_['text_submit_payment']     = 'You will enter your payment details on Paypal in a new window.<br /><span style="font-weight:normal;">After submitting payment, return to this window to continue.</span>';

$_['button_try_again'] = "To Paypal";

//Error
$_['error_checkout_callback']               = "There was an error while verifying your payment from Paypal. Please contact <a href=\"%s\">Customer Support</a> to resolve the payment.";
$_['error_checkout_callback_email_subject'] = "ATTENTION: There was a critical error while resolving an order payment!";
$_['error_checkout_callback_email']         = "There was an error while verifying the payment for %s from Paypal. The transaction completed, but payment status their order information could not be resolved.<br />" .
	"Order ID: %s<br />Paid Amount: %s<br />Customer ID: %s<br />Customer Email: %s<br />";