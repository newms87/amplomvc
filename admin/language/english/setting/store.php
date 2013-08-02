<?php
// Heading
$_['heading_title']				= 'Settings';

//Data
$_['data_stock_display_types'] = array(
	'hide' 	=> "Do not display stock",
	'status' => "Only show stock status",
	-1 		=> "Display stock quantity available",
	10 		=> "Display quantity up to 10",
);
	
// Text
$_['text_success']				= 'Success: You have modified settings!';
$_['text_image_manager']		= 'Image Manager';
$_['text_browse']				= 'Browse Files';
$_['text_clear']					= 'Clear Image';
$_['text_shipping']				= 'Shipping Address';
$_['text_payment']				= 'Payment Address';
$_['text_settings_menu']		= 'Settings Menu';

//Button
$_['button_admin_settings'] = "General Settings";
$_['button_system_update'] = "System Update";

// Column
$_['column_name']				= 'Store Name';
$_['column_url']				= 'Store URL';
$_['column_action']				= 'Action';

// Entry
$_['entry_url']					= 'Store URL:<br /><span class="help">Include the full URL to your store. Make sure to add \'/\' at the end. Example: http://www.yourdomain.com/path/<br /><br />Don\'t use directories to create a new store. You should always point another domain or sub domain to your hosting.</span>';
$_['entry_ssl']					= 'SSL URL:<br /><span class="help">SSL URL to your store. Make sure to add \'/\' at the end. Example: http://www.yourdomain.com/path/<br /><br />Don\'t use directories to create a new store. You should always point another domain or sub domain to your hosting.</span>';
$_['entry_name']					= 'Store Name:';
$_['entry_owner']				= 'Store Owner:';
$_['entry_address']				= 'Address:';
$_['entry_email']				= 'E-Mail:';
$_['entry_telephone']			= 'Telephone:';
$_['entry_fax']					= 'Fax:';
$_['entry_title']				= 'Title:';
$_['entry_meta_description']	= 'Meta Tag Description:';
$_['entry_layout']				= 'Default Layout:';
$_['entry_theme']			= 'Theme:';
$_['entry_country']				= 'Country:';
$_['entry_zone']					= 'Region / State:';
$_['entry_language']			= 'Language:';
$_['entry_currency']			= 'Currency:';
$_['entry_catalog_limit'] 		= 'Default Items Per Page (Catalog):<br /><span class="help">Determines how many catalog items are shown per page (products, categories, etc)</span>';
$_['entry_allowed_shipping_zone']  = 'Allowed Shipping Geo Zone:';
$_['entry_tax']					= 'Display Prices With Tax:';
$_['entry_tax_default']		= 'Use Store Tax Address:<br /><span class="help">Use the store address to calculate taxes if no one is logged in. You can choose to use the store address for the customers shipping or payment address.</span>';
$_['entry_tax_customer']		= 'Use Customer Tax Address:<br /><span class="help">Use the customers default address when they login to calculate taxes. You can choose to use the default address for the customers shipping or payment address.</span>';
$_['entry_customer_group']	= 'Customer Group:<br /><span class="help">Default customer group.</span>';
$_['entry_customer_price']	= 'Login Display Prices:<br /><span class="help">Only show prices when a customer is logged in.</span>';
$_['entry_customer_approval']  = 'Approve New Customers:<br /><span class="help">Don\'t allow new customer to login until their account has been approved.</span>';
$_['entry_guest_checkout']	= 'Guest Checkout:<br /><span class="help">Allow customers to checkout without creating an account. This will not be available when a downloadable product is in the shopping cart.</span>';
$_['entry_account']				= 'Account Terms:<br /><span class="help">Forces people to agree to terms before an account can be created.</span>';
$_['entry_checkout']			= 'Checkout Terms:<br /><span class="help">Forces people to agree to terms before an a customer can checkout.</span>';
$_['entry_stock_display']		= 'Display Stock:<br /><span class="help">Display stock quantity on the product page.</span>';
$_['entry_stock_checkout']	= 'Stock Checkout:<br /><span class="help">Allow customers to still checkout if the products they are ordering are not in stock.</span>';
$_['entry_order_complete_status']		= 'Order Status:<br /><span class="help">Set the default order status when an order is processed.</span>';
$_['entry_cart_weight']		= 'Display Weight on Cart Page:';
$_['entry_show_product_model'] = "Display Model # on product page:";
$_['entry_logo']					= 'Store Logo:';
$_['entry_icon']					= 'Icon:<br /><span class="help">The icon should be a PNG that is 16px x 16px.</span>';
$_['entry_image_category']	= 'Category Image Size:';
$_['entry_image_thumb']		= 'Product Image Thumb Size:';
$_['entry_image_popup']		= 'Product Image Popup Size:';
$_['entry_image_product']		= 'Product Image List Size:';
$_['entry_image_additional']	= 'Additional Product Image Size:';
$_['entry_image_related']		= 'Related Product Image Size:';
$_['entry_image_compare']		= 'Compare Image Size:';
$_['entry_image_wishlist']	= 'Wish List Image Size:';
$_['entry_image_cart']			= 'Cart Image Size:';
$_['entry_use_ssl']				= 'Use SSL:<br /><span class="help">To use SSL check with your host if a SSL certificate is installed.</span>';

// Error
$_['error_store_invalid']				= 'You attempted to access a store that does not exist!';
$_['error_warning']				= 'Warning: Please check the form carefully for errors!';
$_['error_permission']			= 'Warning: You do not have permission to modify stores!';
$_['error_name']					= 'Store Name must be between 1 and 64 characters!';
$_['error_owner']				= 'Store Owner must be between 3 and 64 characters!';
$_['error_address']				= 'Store Address must be between 10 and 256 characters!';
$_['error_email']				= 'E-Mail Address does not appear to be valid!';
$_['error_telephone']			= 'Telephone must be between 3 and 32 characters!';
$_['error_url']					= 'Store URL invalid! Please provide a properly formatted URL (eg: http://yourstore.com)';
$_['error_ssl']					= 'Store SSL invalid!  Please provide a properly formatted URL (eg: http://yourstore.com). NOTE: you may set this to the same value as URL, does not have to be HTTPS protocol.';
$_['error_title']				= 'Title must be between 3 and 32 characters!';
$_['error_limit']				= 'Limit required!';
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
$_['error_default']				= 'Warning: You can not delete your default store!';
$_['error_store']				= 'Warning: This Store cannot be deleted as it is currently assigned to %s orders!';