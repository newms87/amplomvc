<?php
// Heading
$_['heading_title']				= 'General Settings';

//Data
$_['data_stock_display_types'] = array(
	'hide' => "Do not display stock",
		'status' => "Only show stock status",
		-1 => "Display stock quantity available",
		10 => "Display quantity up to 10"
	); 

// Text
$_['text_success']				= 'Success: You have modified settings!';
$_['text_image_manager']		= 'Image Manager';
$_['text_browse']				= 'Browse Files';
$_['text_clear']					= 'Clear Image';
$_['text_shipping']				= 'Shipping Address';
$_['text_payment']				= 'Payment Address';
$_['text_mail']					= 'Mail';
$_['text_smtp']					= 'SMTP';
$_['text_mode_explanation']  = "The file permissions are set user (owner), group, others == ugo == 755 == user has full, group has read & write, others have read & write permissions.";
$_['text_settings']		= 'Settings';

// Entry
$_['entry_name']					= 'Store Name:';
$_['entry_owner']				= 'Store Owner:';
$_['entry_address']				= 'Address:';
$_['entry_email']				= 'E-Mail:';
$_['entry_email_support']		= 'Support Email:<span class="help">Please specify an email to send support requests to.</span>';
$_['entry_email_error']		= 'Error Email:<span class="help">Please specify an email to notify when a critical system error has occurred.</span>';
$_['entry_telephone']			= 'Telephone:';
$_['entry_fax']					= 'Fax:';
$_['entry_title']				= 'Title:';
$_['entry_default_store']		= 'Default Store';
$_['entry_meta_description']	= 'Meta Tag Description:';
$_['entry_layout']				= 'Default Layout:';
$_['entry_debug']					= 'Turn on Global Debug:<span class="help">Should be turned off for production sites.</span>';
$_['entry_allow_close_message_box'] = "Allow Customers to Close Notification Messages?<span class='help'>These are popups that display warning, success and alert/notify messages</span>";
$_['entry_theme']			= 'Theme:';
$_['entry_address_format']	= 'Default Address Format: <span class="help">Insertables:<br/> {firstname}, {lastname}, {company}, {address_1}, {address_2}, {postcode}, {zone}, {zone_code}, {country}. <br/><br />Can be individually set under System > Localisation > Countries</span>';
$_['entry_country']				= 'Country:';
$_['entry_zone']					= 'Region / State:';
$_['entry_language']			= 'Language:';
$_['entry_admin_language']	= 'Administration Language:';
$_['entry_currency']			= 'Currency:<br /><span class="help">Change the default currency. Clear your browser cache to see the change and reset your existing cookie.</span>';
$_['entry_currency_auto']		= 'Auto Update Currency:<br /><span class="help">Set your store to automatically update currencies daily.</span>';
$_['entry_length_class']		= 'Length Class:';
$_['entry_weight_class']		= 'Weight Class:';
$_['entry_catalog_limit'] 		= 'Default Items Per Page (Catalog):<br /><span class="help">Determines how many catalog items are shown per page (products, categories, etc)</span>';
$_['entry_admin_limit']			= 'Default Items Per Page (Admin):<br /><span class="help">Determines how many admin items are shown per page (orders, customers, etc)</span>';
$_['entry_performance_log']		= 'Performance Logging:';
$_['entry_cache_ignore']		= 'Cache Ignore List:<span class="help">(comma separated list)</span>';
$_['entry_tax']					= 'Display Prices With Tax:';
$_['entry_tax_default_id']					= 'Default Tax Class:';
$_['entry_tax_default']		= 'Use Store Tax Address:<br /><span class="help">Use the store address to calculate taxes if no one is logged in. You can choose to use the store address for the customers shipping or payment address.</span>';
$_['entry_tax_customer']		= 'Use Customer Tax Address:<br /><span class="help">Use the customers default address when they login to calculate taxes. You can choose to use the default address for the customers shipping or payment address.</span>';
$_['entry_invoice_prefix']	= 'Invoice Prefix:<br /><span class="help">Set the invoice prefix (e.g. INV-2011-01 or INV-%Y-m%). Invoice ID\'s will start at 1 for each unique prefix. Use a date format (eg: %Y-m-d%) anywhere - Invoice IDs will reset automatically to 1 for each unique date.</span>';
$_['entry_order_edit']			= 'Order Editing:<br /><span class="help">Number of days allowed to edit an order. This is required because prices and discounts may change over time corrupting the order if its edited.</span>';
$_['entry_customer_group']	= 'Customer Group:<br /><span class="help">Default customer group.</span>';
$_['entry_customer_price']	= 'Login Display Prices:<br /><span class="help">Only show prices when a customer is logged in.</span>';
$_['entry_customer_approval']  = 'Approve New Customers:<br /><span class="help">Don\'t allow new customer to login until their account has been approved.</span>';
$_['entry_guest_checkout']	= 'Guest Checkout:<br /><span class="help">Allow customers to checkout without creating an account. This will not be available when a downloadable product is in the shopping cart.</span>';
$_['entry_account']				= 'Account Terms:<br /><span class="help">Forces people to agree to terms before an account can be created.</span>';
$_['entry_checkout']			= 'Checkout Terms:<br /><span class="help">Forces people to agree to terms before an a customer can checkout.</span>';
$_['entry_affiliate']			= 'Affiliate Terms:<br /><span class="help">Forces people to agree to terms before an affiliate account can be created.</span>';
$_['entry_commission']			= 'Affiliate Commission (%):<br /><span class="help">The default affiliate commission percentage.</span>';
$_['entry_breadcrumb_display']			= 'Display Breadcrumbs? <span class="help">Display breadcrumbs in the storefront? (breadcrumbs will still display in the admin panel)</span>';
$_['entry_breadcrumb_separator']			= 'Breadcrumb Separator:';
$_['entry_breadcrumb_separator_admin']			= 'Admin Breadcrumb Separator:';
$_['entry_stock_display']		= 'Display Stock:<br /><span class="help">Display stock quantity on the product page.</span>';
$_['entry_stock_warning']		= 'Show Out Of Stock Warning:<br /><span class="help">Display out of stock message on the shopping cart page if a product is out of stock but stock checkout is yes. (Warning always shows if stock checkout is no)</span>';
$_['entry_stock_checkout']	= 'Stock Checkout:<br /><span class="help">Allow customers to still checkout if the products they are ordering are not in stock.</span>';
$_['entry_stock_status']		= 'Out of Stock Status:<br /><span class="help">Set the default out of stock status selected in product edit.</span>';
$_['entry_order_status']		= 'Order Status:<br /><span class="help">Set the default order status when an order is processed.</span>';
$_['entry_complete_status']	= 'Complete Order Status:<br /><span class="help">Set the order status the customers order must reach before they are allowed to access their downloadable products and gift vouchers.</span>';
$_['entry_return_status']		= 'Return Status:<br /><span class="help">Set the default return status when an returns request is submitted.</span>';
$_['entry_review']				= 'Allow Reviews:<br /><span class="help">Enable/Disable new review entry and display of existing reviews</span>';
$_['entry_download']			= 'Allow Downloads:';
$_['entry_upload_allowed']	= 'Allowed Upload File Extensions:<br /><span class="help">Add which file extensions are allowed to be uploaded. Use comma separated values.</span>';
$_['entry_upload_images_allowed']	= 'Allowed Upload Image Extensions:<br /><span class="help">Add which image file extensions are allowed to be uploaded. Use comma separated values.</span>';
$_['entry_upload_images_mime_types_allowed']	= 'Allowed Upload Image Mime Types:<br /><span class="help">Add which image Mime Types are allowed to be uploaded. Use comma separated values.</span>';
$_['entry_cart_weight']		= 'Display Weight on Cart Page:<br /><span class="help">Show the cart weight on the cart page</span>';
$_['entry_logo']					= 'Store Logo:';
$_['entry_admin_logo']					= 'Admin Panel Logo:';
$_['entry_icon']					= 'Icon:<br /><span class="help">The icon should be a PNG that is 16px x 16px.</span>';
$_['entry_image_admin_thumb']	= 'Admin Image Thumb Size:';
$_['entry_image_admin_list']	= 'Admin Image List Size:';
$_['entry_image_category']	= 'Category Image Size:';
$_['entry_image_manufacturer']	= 'Manufacturer Image Size:';
$_['entry_image_thumb']		= 'Product Image Thumb Size:';
$_['entry_image_popup']		= 'Product Image Popup Size:';
$_['entry_image_product']		= 'Product Image List Size:';
$_['entry_image_product_option']		= 'Product Options Image Size:';
$_['entry_image_additional']	= 'Additional Product Image Size:';
$_['entry_image_related']		= 'Related Product Image Size:';
$_['entry_image_compare']		= 'Compare Image Size:';
$_['entry_image_wishlist']	= 'Wish List Image Size:';
$_['entry_image_cart']			= 'Cart Image Size:';
$_['entry_mail_protocol']		= 'Mail Protocol:<span class="help">Only choose \'Mail\' unless your host has disabled the php mail function.';
$_['entry_mail_parameter']	= 'Mail Parameters:<span class="help">When using \'Mail\', additional mail parameters can be added here (e.g. "-femail@storeaddress.com".';
$_['entry_smtp_host']			= 'SMTP Host:';
$_['entry_smtp_username']		= 'SMTP Username:';
$_['entry_smtp_password']		= 'SMTP Password:';
$_['entry_smtp_port']			= 'SMTP Port:';
$_['entry_smtp_timeout']		= 'SMTP Timeout:';
$_['entry_account_mail']		= 'New Account Alert Mail:<br /><span class="help">Send a email to the store owner when a new account is registered.</span>';
$_['entry_alert_mail']			= 'New Order Alert Mail:<br /><span class="help">Send a email to the store owner when a new order is created.</span>';
$_['entry_alert_emails']		= 'Additional Alert E-Mails:<br /><span class="help">Any additional emails you want to receive the alert email, in addition to the main store email. (comma separated)</span>';
$_['entry_fraud_detection']	= 'Use MaxMind Fraud Detection System:<br /><span class="help">MaxMind is a fraud detections service. If you don\'t have a license key you can <a onclick="window.open(\'http://www.maxmind.com/?rId=opencart\');"><u>sign up here</u></a>. Once you have obtained a key copy and paste it into the field below.</span>';
$_['entry_fraud_key']			= 'MaxMind License Key:</span>';
$_['entry_fraud_score']		= 'MaxMind Risk Score:<br /><span class="help">The higher the score the more likly the order is fraudulent. Set a score between 0 - 100.</span>';
$_['entry_fraud_status']		= 'MaxMind Fraud Order Status:<br /><span class="help">Orders over your set score will be assigned this order status and will not be allowed to reach the complete status automatically.</span>';
$_['entry_use_ssl']				= 'Use SSL:<br /><span class="help">To use SSL check with your host if a SSL certificate is installed and added the SSL URL to the catalog and admin config files.</span>';
$_['entry_seo_url']				= 'Use SEO URL\'s:<br /><span class="help">To use SEO URL\'s apache module mod-rewrite must be installed and you need to rename the htaccess.txt to .htaccess.</span>';
$_['entry_maintenance']		= 'Maintenance Mode:<br /><span class="help">Prevents customers from browsing your store. They will instead see a maintenance message. If logged in as admin, you will see the store as normal.</span>';
$_['entry_image_max_mem']		= 'Image Resize Max Memory<span class="help">The maximum allowed memory when resizing images for the cache. Must be in php memory format (eg: 128M, 512M, 1G, etc.)</span>';
$_['entry_encryption']			= 'Encryption Key:<br /><span class="help">Please provide a secret key that will be used to encrypt private information when processing orders.</span>';
$_['entry_compression']		= 'Output Compression Level:<br /><span class="help">GZIP for more efficient transfer to requesting clients. Compression level must be between 0 - 9</span>';
$_['entry_debug_send_emails']  = 'Send Emails to third parties? <span class="help">Emails sent to people other than the current user and the system emails</span>';
$_['entry_error_display']		= 'Display Errors:';
$_['entry_error_log']			= 'Log Errors:';
$_['entry_error_filename']	= 'Error Log Filename:';
$_['entry_statcounter']		= 'Stat Counter Code:<span class="help">Sign up at <a onclick="window.open(\'http://www.statcounter.com/sign-up/\');"><u>Stat Counter</u></a> and copy and past the code in this field.</span>';
$_['entry_statcounter_project'] = 'Project ID';
$_['entry_statcounter_security'] = 'Security Code';
$_['entry_google_analytics']	= 'Google Analytics Code:<br /><span class="help">Login to your <a onclick="window.open(\'http://www.google.com/analytics/\');"><u>Google Analytics</u></a> account and after creating your web site profile copy and paste the analytics code into this field.</span>';
$_['entry_mail_registration']  = 'Customer Registration Email:';
$_['entry_mail_mailto'] = "Send To:<span class='help'>(comma separated list)</span>";
$_['entry_mail_cc'] = "Copy To:<span class='help'>(comma separated list)</span>";
$_['entry_mail_bcc'] = "Blind Copy To:<span class='help'>(comma separated list)</span>";
$_['entry_mail_subject'] = "Subject:";
$_['entry_mail_message'] = "Message:";

$_['entry_default_modes'] = "Default File Permissions <span class='help'>These are the permissions set for system generated files and directories</span>";
$_['entry_default_file_mode'] = "Default File Permissions";
$_['entry_default_dir_mode'] = "Default Directory Permissions";

$_['entry_image_modes'] = "Image File Permissions <span class='help'>These are the permissions set for system generated image files and directories</span>";
$_['entry_image_file_mode'] = "Image File Permissions";
$_['entry_image_dir_mode'] = "Image Directory Permissions";

$_['entry_plugin_modes'] = "Plugin File Permissions <span class='help'>These are the permissions set for system generated plugin files and directories</span>";
$_['entry_plugin_file_mode'] = "Plugin File Permissions";
$_['entry_plugin_dir_mode'] = "Plugin Directory Permissions";

//Tabs
$_['tab_mail_msgs']			= 'Mail Messages';
$_['tab_file_permissions']			= 'File Permissions';

// Error
$_['error_warning']				= 'Warning: Please check the form carefully for errors!';
$_['error_permission']			= 'Warning: You do not have permission to modify settings!';
$_['error_name']					= 'Store Name must be between 3 and 32 characters!';
$_['error_owner']				= 'Store Owner must be between 3 and 64 characters!';
$_['error_address']				= 'Store Address must be between 10 and 256 characters!';
$_['error_email']				= 'E-Mail Address does not appear to be valid!';
$_['error_telephone']			= 'Telephone must be between 3 and 32 characters!';
$_['error_title']				= 'Title must be between 3 and 32 characters!';
$_['error_limit']				= 'Limit required!';
$_['error_image_admin_thumb']		= 'Product Image Admin Thumb Size dimensions required!';
$_['error_image_admin_list']		= 'Product Image Admin List Size dimensions required!';
$_['error_image_thumb']		= 'Product Image Thumb Size dimensions required!';
$_['error_image_popup']		= 'Product Image Popup Size dimensions required!';
$_['error_image_product']		= 'Product List Size dimensions required!';
$_['error_image_category']	= 'Category List Size dimensions required!';
$_['error_image_manufacturer'] = 'Designer List Size dimensions required!';
$_['error_image_additional']	= 'Additional Product Image Size dimensions required!';
$_['error_image_related']		= 'Related Product Image Size dimensions required!';
$_['error_image_compare']		= 'Compare Image Size dimensions required!';
$_['error_image_wishlist']	= 'Wish List Image Size dimensions required!';
$_['error_image_cart']			= 'Cart Image Size dimensions required!';
$_['error_error_filename']	= 'Error Log Filename required!';
