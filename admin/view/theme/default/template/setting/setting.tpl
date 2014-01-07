<?= $header; ?>
<div class="section">
<?= $this->breadcrumb->render(); ?>
<div class="box">
<div class="heading">
	<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= _l("General Settings"); ?></h1>

	<div class="buttons">
		<a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a>
		<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
	</div>
</div>
<div class="section">
<div id="tabs" class="htabs">
	<a href="#tab-general"><?= _l("General"); ?></a>
	<a href="#tab-store"><?= _l("Store"); ?></a>
	<a href="#tab-local"><?= _l("Local"); ?></a>
	<a href="#tab-option"><?= _l("Option"); ?></a>
	<a href="#tab-image"><?= _l("Image"); ?></a>
	<a href="#tab-mail"><?= _l("Mail"); ?></a>
	<a href="#tab-fraud"><?= _l("Fraud"); ?></a>
	<a href="#tab-file-permissions"><?= _l("File Permissions"); ?></a>
	<a href="#tab-server"><?= _l("Server"); ?></a>
</div>
<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
<div id="tab-general">
	<table class="form">
		<tr>
			<td class="required"> <?= _l("Store Name:"); ?></td>
			<td><input type="text" name="config_name" value="<?= $config_name; ?>" size="40"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Store Owner:"); ?></td>
			<td><input type="text" name="config_owner" value="<?= $config_owner; ?>" size="40"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Address:"); ?></td>
			<td><textarea name="config_address" cols="40" rows="5"><?= $config_address; ?></textarea>
		</tr>
		<tr>
			<td class="required"> <?= _l("E-Mail:"); ?></td>
			<td><input type="text" name="config_email" value="<?= $config_email; ?>" size="40"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Support Email:<span class =\"help\">Please specify an email to send support requests to.</span>"); ?></td>
			<td><input type="text" name="config_email_support" value="<?= $config_email_support; ?>" size="40"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Error Email:<span class=\"help\">Please specify an email to notify when a critical system error has occurred.</span>"); ?></td>
			<td><input type="text" name="config_email_error" value="<?= $config_email_error; ?>" size="40"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Telephone:"); ?></td>
			<td><input type="text" name="config_telephone" value="<?= $config_telephone; ?>"/>
		</tr>
		<tr>
			<td><?= _l("Fax:"); ?></td>
			<td><input type="text" name="config_fax" value="<?= $config_fax; ?>"/></td>
		</tr>
	</table>
</div>
<div id="tab-store">
	<table class="form">
		<tr>
			<td class="required"> <?= _l("Title:"); ?></td>
			<td><input type="text" name="config_title" value="<?= $config_title; ?>"/>
		</tr>
		<tr>
			<td><?= _l("Meta Tag Description:"); ?></td>
			<td><textarea name="config_meta_description" cols="40" rows="5"><?= $config_meta_description; ?></textarea>
			</td>
		</tr>
		<tr>
			<td><?= _l("Theme:"); ?></td>
			<td>
				<? $this->builder->setConfig('name', 'name'); ?>
				<?= $this->builder->build('select', $themes, 'config_theme', $config_theme); ?>
			</td>
		</tr>
		<tr>
			<td></td>
			<td id="theme"></td>
		</tr>
		<tr>
			<td><?= _l("Default Store"); ?></td>
			<td>
				<? $this->builder->setConfig('store_id', 'name'); ?>
				<?= $this->builder->build('select', $stores, 'config_default_store', $config_default_store); ?>
			</td>
		</tr>
		<tr>
			<td><?= _l("Default Layout:"); ?></td>
			<? $this->builder->setConfig('layout_id', 'name'); ?>
			<td><?= $this->builder->build('select', $data_layouts, 'config_default_layout_id', $config_default_layout_id); ?></td>
		</tr>
	</table>
</div>
<div id="tab-local">
	<table class="form">
		<tr>
			<td><?= _l("Default Address Format: <span class=\"help\">Insertables:<br/>
{firstname}, {lastname}, {company}, {address_1}, {address_2}, {postcode}, {zone}, {zone_code}, {country}. <br/><br />Can be individually set under System > Localisation > Countries</span>"); ?></td>
			<td><textarea name="config_address_format" cols="40" rows="5"><?= $config_address_format; ?></textarea></td>
		</tr>
		<tr>
			<td><?= _l("Country:"); ?></td>
			<td>
				<?= $this->builder->setConfig('country_id', 'name'); ?>
				<?= $this->builder->build('select', $countries, "config_country_id", $config_country_id, array('class' => "country_select")); ?>
			</td>
		</tr>
		<tr>
			<td><?= _l("Region / State:"); ?></td>
			<td><select name="config_zone_id" class="zone_select" zone_id="<?= $config_zone_id; ?>"></select></td>
		</tr>
		<tr>
			<td><?= _l("Language:"); ?></td>
			<td><select name="config_language">
					<? foreach ($languages as $language) { ?>
						<? if ($language['code'] == $config_language) { ?>
							<option value="<?= $language['code']; ?>" selected="selected"><?= $language['name']; ?></option>
						<? } else { ?>
							<option value="<?= $language['code']; ?>"><?= $language['name']; ?></option>
						<? } ?>
					<? } ?>
				</select></td>
		</tr>
		<tr>
			<td><?= _l("Administration Language:"); ?></td>
			<td><select name="config_admin_language">
					<? foreach ($languages as $language) { ?>
						<? if ($language['code'] == $config_admin_language) { ?>
							<option value="<?= $language['code']; ?>" selected="selected"><?= $language['name']; ?></option>
						<? } else { ?>
							<option value="<?= $language['code']; ?>"><?= $language['name']; ?></option>
						<? } ?>
					<? } ?>
				</select></td>
		</tr>
		<tr>
			<td><?= _l("Use Macro Languages (experimental):<span class=\"help\">Attempt to resolve languages by country specific macro codes</span>"); ?></td>
			<td><?= $this->builder->build('select', $data_yes_no, 'config_use_macro_languages', $config_use_macro_languages); ?></td>
		</tr>
		<tr>
			<td><?= _l("Currency:<br /><span class=\"help\">Change the default currency. Clear your browser cache to see the change and reset your existing cookie.</span>"); ?></td>
			<td><select name="config_currency">
					<? foreach ($currencies as $currency) { ?>
						<? if ($currency['code'] == $config_currency) { ?>
							<option value="<?= $currency['code']; ?>"
								selected="selected"><?= $currency['title']; ?></option>
						<? } else { ?>
							<option value="<?= $currency['code']; ?>"><?= $currency['title']; ?></option>
						<? } ?>
					<? } ?>
				</select></td>
		</tr>
		<tr>
			<td><?= _l("Auto Update Currency:<br /><span class=\"help\">Set your store to automatically update currencies daily.</span>"); ?></td>
			<td><? if ($config_currency_auto) { ?>
					<input type="radio" name="config_currency_auto" value="1" checked="checked"/>
					<?= _l("Yes"); ?>
					<input type="radio" name="config_currency_auto" value="0"/>
					<?= _l("No"); ?>
				<? } else { ?>
					<input type="radio" name="config_currency_auto" value="1"/>
					<?= _l("Yes"); ?>
					<input type="radio" name="config_currency_auto" value="0" checked="checked"/>
					<?= _l("No"); ?>
				<? } ?></td>
		</tr>
		<tr>
			<td><?= _l("Length Class:"); ?></td>
			<td><select name="config_length_class_id">
					<? foreach ($length_classes as $length_class) { ?>
						<? if ($length_class['length_class_id'] == $config_length_class_id) { ?>
							<option value="<?= $length_class['length_class_id']; ?>"
								selected="selected"><?= $length_class['title']; ?></option>
						<? } else { ?>
							<option value="<?= $length_class['length_class_id']; ?>"><?= $length_class['title']; ?></option>
						<? } ?>
					<? } ?>
				</select></td>
		</tr>
		<tr>
			<td><?= _l("Weight Class:"); ?></td>
			<td><select name="config_weight_class_id">
					<? foreach ($weight_classes as $weight_class) { ?>
						<? if ($weight_class['weight_class_id'] == $config_weight_class_id) { ?>
							<option value="<?= $weight_class['weight_class_id']; ?>"
								selected="selected"><?= $weight_class['title']; ?></option>
						<? } else { ?>
							<option value="<?= $weight_class['weight_class_id']; ?>"><?= $weight_class['title']; ?></option>
						<? } ?>
					<? } ?>
				</select></td>
		</tr>
	</table>
</div>
<div id="tab-option">
	<table class="form">
		<tr>
			<td>
				<?= _l("Administration Bar"); ?>
				<span class="help"><?= _l("This will display a small toolbar on the store fronts when logged into the Admin Panel"); ?></span>
			</td>
			<td><?= $this->builder->build('radio', $data_yes_no, 'config_admin_bar', $config_admin_bar); ?></td>
		</tr>
		<tr>
			<td>
				<?= _l("Automated Tasks"); ?>
				<span class="help"><?= _l("Highly recommended to leave this on!"); ?></span>
			</td>
			<td><?= $this->builder->build('radio', $data_yes_no, 'config_cron_status', $config_cron_status); ?></td>
		</tr>
		<tr>
			<td class="required"> <?= _l("Display Breadcrumbs? <span class=\"help\">Display breadcrumbs in the storefront? (breadcrumbs will still display in the admin panel)</span>"); ?></td>
			<td><?= $this->builder->build('select', $data_yes_no, "config_breadcrumb_display", $config_breadcrumb_display); ?></td>
		</tr>
		<tr>
			<td class="required"> <?= _l("Breadcrumb Separator:"); ?></td>
			<td><input type="text" style="font-size:20px" name="config_breadcrumb_separator" value="<?= $config_breadcrumb_separator; ?>" size="1"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Admin Breadcrumb Separator:"); ?></td>
			<td><input type="text" style="font-size:20px" name="config_breadcrumb_separator_admin" value="<?= $config_breadcrumb_separator_admin; ?>" size="1"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Default Items Per Page (Catalog):<br /><span class=\"help\">Determines how many catalog items are shown per page (products, categories, etc)</span>"); ?></td>
			<td><input type="text" name="config_catalog_limit" value="<?= $config_catalog_limit; ?>" size="3"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Default Items Per Page (Admin):<br /><span class=\"help\">Determines how many admin items are shown per page (orders, customers, etc)</span>"); ?></td>
			<td><input type="text" name="config_admin_limit" value="<?= $config_admin_limit; ?>" size="3"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Default Autocomplete Limit:<br /><span class=\"help\">Determines how many autocomplete items are retrieved at a time</span>"); ?></td>
			<td><input type="text" name="config_autocomplete_limit" value="<?= $config_autocomplete_limit; ?>" size="3"/>
		</tr>
		<tr>
			<td><?= _l("Performance Logging:"); ?></td>
			<td><?= $this->builder->build('select', $data_statuses, 'config_performance_log', $config_performance_log); ?></td>
		</tr>
		<tr>
			<td><?= _l("Default Return Policy:"); ?></td>
			<td>
				<? if (!empty($data_return_policies)) { ?>
					<? $this->builder->setConfig(false, 'title'); ?>
					<?= $this->builder->build('select', $data_return_policies, 'config_default_return_policy', $config_default_return_policy); ?>
				<? } ?>
				<p><?= $text_add_return_policy; ?></p>
			</td>
		</tr>
		<tr>
			<td><?= _l("Default Shipping Policy:"); ?></td>
			<td>
				<? if (!empty($data_shipping_policies)) { ?>
					<? $this->builder->setConfig(false, 'title'); ?>
					<?= $this->builder->build('select', $data_shipping_policies, 'config_default_shipping_policy', $config_default_shipping_policy); ?>
				<? } ?>
				<p><?= $text_add_shipping_policy; ?></p>
			</td>
		</tr>
		<tr>
			<td><?= _l("Shipping / Returns Policy Information:"); ?></td>
			<td>
				<? $this->builder->setConfig('information_id', 'title'); ?>
				<?= $this->builder->build('select', $data_informations, 'config_shipping_return_info_id', $config_shipping_return_info_id); ?>
			</td>
		<tr>
			<td><?= _l("Cache Ignore List:<span class=\"help\">(comma separated list)</span>"); ?></td>
			<td><textarea name="config_cache_ignore"><?= $config_cache_ignore; ?></textarea></td>
		</tr>
		<tr>
			<td><?= _l("Allow Customers to Close Notification Messages?<span class=\'help\'>These are popups that display warning, success and alert/notify messages</span>"); ?></td>
			<td><?= $this->builder->build('radio', $data_yes_no, 'config_allow_close_message_box', $config_allow_close_message_box); ?></td>
		</tr>
		<tr>
			<td><?= _l("Show Category Image:"); ?></td>
			<td><?= $this->builder->build('radio', $data_yes_no, 'config_show_category_image', $config_show_category_image); ?></td>
		</tr>
		<tr>
			<td><?= _l("Show Category Description:"); ?></td>
			<td><?= $this->builder->build('radio', $data_yes_no, 'config_show_category_description', $config_show_category_description); ?></td>
		</tr>
		<tr>
			<td><?= _l("Product List Hover Image:<span class=\"help\">For the Product List pages, show an alternate image when moving the mouse over the product block</span>"); ?></td>
			<td><?= $this->builder->build('radio', $data_yes_no, 'config_show_product_list_hover_image', $config_show_product_list_hover_image); ?></td>
		</tr>
		<tr>
			<td><?= _l("Display Return Policy:<span class=\"help\">(eg: final sale, # days to return, etc.) as a column in the cart</span>"); ?></td>
			<td><?= $this->builder->build('radio', $data_yes_no, 'config_cart_show_return_policy', $config_cart_show_return_policy); ?></td>
		</tr>
		<tr>
			<td><?= _l("Display Prices With Tax:"); ?></td>
			<td><? if ($config_show_price_with_tax) { ?>
					<input type="radio" name="config_show_price_with_tax" value="1" checked="checked"/>
					<?= _l("Yes"); ?>
					<input type="radio" name="config_show_price_with_tax" value="0"/>
					<?= _l("No"); ?>
				<? } else { ?>
					<input type="radio" name="config_show_price_with_tax" value="1"/>
					<?= _l("Yes"); ?>
					<input type="radio" name="config_show_price_with_tax" value="0" checked="checked"/>
					<?= _l("No"); ?>
				<? } ?></td>
		</tr>
		<tr>
			<td><?= _l("Default Tax Class:"); ?></td>
			<td>
				<? $this->builder->setConfig('tax_class_id', 'title'); ?>
				<?= $this->builder->build('select', $tax_classes, 'config_tax_default_id', $config_tax_default_id); ?>
			</td>
		</tr>
		<tr>
			<td><?= _l("Use Store Tax Address:<br /><span class=\"help\">Use the store address to calculate taxes if no one is logged in. You can choose to use the store address for the customers shipping or payment address.</span>"); ?></td>
			<td><select name="config_tax_default">
					<option value=""><?= _l(" --- None --- "); ?></option>
					<? if ($config_tax_default == 'shipping') { ?>
						<option value="shipping" selected="selected"><?= _l("Shipping Address"); ?></option>
					<? } else { ?>
						<option value="shipping"><?= _l("Shipping Address"); ?></option>
					<? } ?>
					<? if ($config_tax_default == 'payment') { ?>
						<option value="payment" selected="selected"><?= _l("Payment Address"); ?></option>
					<? } else { ?>
						<option value="payment"><?= _l("Payment Address"); ?></option>
					<? } ?>
				</select></td>
		</tr>
		<tr>
			<td><?= _l("Use Customer Tax Address:<br /><span class=\"help\">Use the customers default address when they login to calculate taxes. You can choose to use the default address for the customers shipping or payment address.</span>"); ?></td>
			<td><select name="config_tax_customer">
					<option value=""><?= _l(" --- None --- "); ?></option>
					<? if ($config_tax_customer == 'shipping') { ?>
						<option value="shipping" selected="selected"><?= _l("Shipping Address"); ?></option>
					<? } else { ?>
						<option value="shipping"><?= _l("Shipping Address"); ?></option>
					<? } ?>
					<? if ($config_tax_customer == 'payment') { ?>
						<option value="payment" selected="selected"><?= _l("Payment Address"); ?></option>
					<? } else { ?>
						<option value="payment"><?= _l("Payment Address"); ?></option>
					<? } ?>
				</select></td>
		</tr>
		<tr>
			<td>
				<div><?= _l("Invoice Prefix:"); ?></div>
				<span class="help"><?= _l("Set the invoice prefix (e.g. INV-2011-01 or INV-%Y-m%). Invoice ID's will start at 1 for each unique prefix. Use a date format (eg: %Y-m-d%) anywhere - Invoice IDs will reset automatically to 1 for each unique date."); ?></span>
			</td>
			<td><input type="text" name="config_invoice_prefix" value="<?= $config_invoice_prefix; ?>"/></td>
		</tr>
		<tr>
			<td><?= _l("Order Editing:<br /><span class=\"help\">Number of days allowed to edit an order. This is required because prices and discounts may change over time corrupting the order if its edited.</span>"); ?></td>
			<td><input type="text" name="config_order_edit" value="<?= $config_order_edit; ?>" size="3"/></td>
		</tr>
		<tr>
			<td><?= _l("Customer Group:<br /><span class=\"help\">Default customer group.</span>"); ?></td>
			<td><select name="config_customer_group_id">
					<? foreach ($customer_groups as $customer_group) { ?>
						<? if ($customer_group['customer_group_id'] == $config_customer_group_id) { ?>
							<option value="<?= $customer_group['customer_group_id']; ?>"
								selected="selected"><?= $customer_group['name']; ?></option>
						<? } else { ?>
							<option value="<?= $customer_group['customer_group_id']; ?>"><?= $customer_group['name']; ?></option>
						<? } ?>
					<? } ?>
				</select></td>
		</tr>
		<tr>
			<td><?= _l("Login Display Prices:<br /><span class=\"help\">Only show prices when a customer is logged in.</span>"); ?></td>
			<td><?= $this->builder->build('radio', $data_yes_no, 'config_customer_hide_price', $config_customer_hide_price); ?></td>
		</tr>
		<tr>
			<td><?= _l("Approve New Customers:<br /><span class=\"help\">Don\'t allow new customer to login until their account has been approved.</span>"); ?></td>
			<td><?= $this->builder->build('radio', $data_yes_no, 'config_customer_approval', $config_customer_approval); ?></td>
		</tr>
		<tr>
			<td><?= _l("Guest Checkout:<br /><span class=\"help\">Allow customers to checkout without creating an account. This will not be available when a downloadable product is in the shopping cart.</span>"); ?></td>
			<td><?= $this->builder->build('radio', $data_yes_no, 'config_guest_checkout', $config_guest_checkout); ?></td>
		</tr>
		<tr>
			<td><?= _l("Account Terms:<br /><span class=\"help\">Forces people to agree to terms before an account can be created.</span>"); ?></td>
			<td>
				<? $this->builder->setConfig('information_id', 'title'); ?>
				<?= $this->builder->build('select', $data_informations, 'config_account_terms_info_id', $config_account_terms_info_id); ?>
			</td>
		</tr>
		<tr>
			<td><?= _l("Checkout Terms:<br /><span class=\"help\">Forces people to agree to terms before an a customer can checkout.</span>"); ?></td>
			<td>
				<? $this->builder->setConfig('information_id', 'title'); ?>
				<?= $this->builder->build('select', $data_informations, 'config_checkout_terms_info_id', $config_checkout_terms_info_id); ?>
			</td>
		</tr>
		<tr>
			<td><?= _l("Affiliate Terms:<br /><span class=\"help\">Forces people to agree to terms before an affiliate account can be created.</span>"); ?></td>
			<td>
				<? $this->builder->setConfig('information_id', 'title'); ?>
				<?= $this->builder->build('select', $data_informations, 'config_affiliate_terms_info_id', $config_affiliate_terms_info_id); ?>
			</td>
		</tr>
		<tr>
			<td>
				<div><?= _l("Affiliate Commission (%):"); ?></div>
				<span class="help"><?= _l("The default affiliate commission percentage."); ?></span>
			</td>
			<td><input type="text" name="config_commission" value="<?= $config_commission; ?>" size="3"/></td>
		</tr>
		<tr>
			<td><?= _l("Display Stock:<br /><span class=\"help\">Display stock quantity on the product page.</span>"); ?></td>
			<td><?= $this->builder->build('radio', $data_stock_display_types, "config_stock_display", $config_stock_display, array('class' => 'display_stock_radio')); ?></td>
		</tr>
		<tr>
			<td><?= _l("Show Out Of Stock Warning:<br /><span class=\"help\">Display out of stock message on the shopping cart page if a product is out of stock but stock checkout is yes. (Warning always shows if stock checkout is no)</span>"); ?></td>
			<td><?= $this->builder->build('select', $data_yes_no, 'config_stock_warning', $config_stock_warning); ?></td>
		</tr>
		<tr>
			<td><?= _l("Stock Checkout:<br /><span class=\"help\">Allow customers to still checkout if the products they are ordering are not in stock.</span>"); ?></td>
			<td><?= $this->builder->build('radio', $data_yes_no, 'config_stock_checkout', $config_stock_checkout); ?></td>
		</tr>
		<tr>
			<td><?= _l("Out of Stock Status:<br /><span class=\"help\">Set the default out of stock status selected in product edit.</span>"); ?></td>
			<td><select name="config_stock_status_id">
					<? foreach ($data_stock_statuses as $stock_status) { ?>
						<? if ($stock_status['stock_status_id'] == $config_stock_status_id) { ?>
							<option value="<?= $stock_status['stock_status_id']; ?>"
								selected="selected"><?= $stock_status['name']; ?></option>
						<? } else { ?>
							<option value="<?= $stock_status['stock_status_id']; ?>"><?= $stock_status['name']; ?></option>
						<? } ?>
					<? } ?>
				</select></td>
		</tr>
		<tr>
			<td><?= _l("Show Related Products on Product Page:"); ?></td>
			<td><?= $this->builder->build('select', $data_show_product_related, 'config_show_product_related', $config_show_product_related); ?></td>
		</tr>
		<tr>
			<td><?= _l("Order Received Status:<br /><span class=\"help\">Set the initial order status when an order is received.</span>"); ?></td>
			<td>
				<?= $this->builder->setConfig(false, 'title'); ?>
				<?= $this->builder->build('select', $data_order_statuses, 'config_order_received_status_id', $config_order_received_status_id); ?>
			</td>
		</tr>
		<tr>
			<td><?= _l("Complete Order Status:<br /><span class=\"help\">Set the order status for when an order has been fully paid for and products are deducted from the inventory (Downloads / Gift Vouchers are accessible and Products requiring shipping should be shipped).</span>"); ?></td>
			<td>
				<?= $this->builder->setConfig(false, 'title'); ?>
				<?= $this->builder->build('select', $data_order_statuses, 'config_order_complete_status_id', $config_order_complete_status_id); ?>
			</td>
		</tr>
		<tr>
			<td><?= _l("Order Blacklist Status:<br /><span class=\"help\">Set the order status when an order is associated with a blacklisted account.</span>"); ?></td>
			<td>
				<?= $this->builder->setConfig(false, 'title'); ?>
				<?= $this->builder->build('select', $data_order_statuses, 'config_order_blacklist_status_id', $config_order_blacklist_status_id); ?>
			</td>
		</tr>
		<tr>
			<td><?= _l("Fraud Order Status:<br /><span class=\"help\">Orders detected as potentially fraudulent will be assigned this order status and will not be allowed to reach the complete status unless manually overridden.</span>"); ?></td>
			<td>
				<?= $this->builder->setConfig(false, 'title'); ?>
				<?= $this->builder->build('select', $data_order_statuses, 'config_order_fraud_status_id', $config_order_fraud_status_id); ?>
			</td>
		</tr>
		<tr>
			<td><?= _l("Return Status:<br /><span class=\"help\">Set the default return status when an returns request is submitted.</span>"); ?></td>
			<? $this->builder->setConfig(false, 'title'); ?>
			<td><?= $this->builder->build('select', $data_return_statuses, 'config_return_status_id', $config_return_status_id); ?></td>
		</tr>
		<tr>
			<td><?= _l("Allow Reviews:<br /><span class=\"help\">Enable/Disable new review entry and display of existing reviews</span>"); ?></td>
			<td><? if ($config_review_status) { ?>
					<input type="radio" name="config_review_status" value="1" checked="checked"/>
					<?= _l("Yes"); ?>
					<input type="radio" name="config_review_status" value="0"/>
					<?= _l("No"); ?>
				<? } else { ?>
					<input type="radio" name="config_review_status" value="1"/>
					<?= _l("Yes"); ?>
					<input type="radio" name="config_review_status" value="0" checked="checked"/>
					<?= _l("No"); ?>
				<? } ?></td>
		</tr>
		<tr>
			<td><?= _l("Allow Social Sharing:"); ?></td>
			<td><?= $this->builder->build('select', $data_yes_no, "config_share_status", $config_share_status); ?></td>
		</tr>
		<td><?= _l("Show Product Attributes on Product Page:"); ?></td>
		<td><?= $this->builder->build('select', $data_yes_no, "config_show_product_attributes", $config_show_product_attributes); ?></td>
		</tr>
		<tr>
			<td><?= _l("Allow Downloads:"); ?></td>
			<td><?= $this->builder->build('select', $data_yes_no, "config_download", $config_download); ?></td>
		</tr>
		<tr>
			<td><?= _l("Allowed Upload File Extensions:<br /><span class=\"help\">Add which file extensions are allowed to be uploaded. Use comma separated values.</span>"); ?></td>
			<td><textarea name="config_upload_allowed" cols="40" rows="5"><?= $config_upload_allowed; ?></textarea></td>
		</tr>
		<tr>
			<td><?= _l("Allowed Upload Image Extensions:<br /><span class=\"help\">Add which image file extensions are allowed to be uploaded. Use comma separated values.</span>"); ?></td>
			<td><textarea name="config_upload_images_allowed" cols="40"
					rows="5"><?= $config_upload_images_allowed; ?></textarea></td>
		</tr>
		<tr>
			<td><?= _l("Allowed Upload Image Mime Types:<br /><span class=\"help\">Add which image Mime Types are allowed to be uploaded. Use comma separated values.</span>"); ?></td>
			<td><textarea name="config_upload_images_mime_types_allowed" cols="40"
					rows="5"><?= $config_upload_images_mime_types_allowed; ?></textarea></td>
		</tr>
		<tr>
			<td><?= _l("Display Weight on Cart Page:<br /><span class=\"help\">Show the cart weight on the cart page</span>"); ?></td>
			<td><? if ($config_cart_weight) { ?>
					<input type="radio" name="config_cart_weight" value="1" checked="checked"/>
					<?= _l("Yes"); ?>
					<input type="radio" name="config_cart_weight" value="0"/>
					<?= _l("No"); ?>
				<? } else { ?>
					<input type="radio" name="config_cart_weight" value="1"/>
					<?= _l("Yes"); ?>
					<input type="radio" name="config_cart_weight" value="0" checked="checked"/>
					<?= _l("No"); ?>
				<? } ?></td>
		</tr>
	</table>
</div>
<div id="tab-image">
	<table class="form">
		<tr>
			<td><?= _l("Admin Panel Logo:"); ?></td>
			<td>
				<?= $this->builder->setBuilderTemplate('click_image'); ?>
				<?= $this->builder->imageInput("config_admin_logo", $config_admin_logo); ?>
			</td>
		</tr>
		<tr>
			<td><?= _l("Icon:<br /><span class=\"help\">The icon should be a PNG that is 16px x 16px.</span>"); ?></td>
			<td>
				<?= $this->builder->setBuilderTemplate('click_image'); ?>
				<?= $this->builder->imageInput("config_icon", $config_icon); ?>
			</td>
		</tr>
		<tr>
			<td class="required"> <?= _l("Admin Image Thumb Size:"); ?></td>
			<td>
				<input type="text" name="config_image_admin_thumb_width" value="<?= $config_image_admin_thumb_width; ?>" size="3"/>
				x
				<input type="text" name="config_image_admin_thumb_height" value="<?= $config_image_admin_thumb_height; ?>" size="3"/>
			</td>
		</tr>
		<tr>
			<td class="required"> <?= _l("Admin Image List Size:"); ?></td>
			<td><input type="text" name="config_image_admin_list_width" value="<?= $config_image_admin_list_width; ?>"
					size="3"/>
				x
				<input type="text" name="config_image_admin_list_height" value="<?= $config_image_admin_list_height; ?>"
					size="3"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Category Image Size:"); ?></td>
			<td><input type="text" name="config_image_category_width" value="<?= $config_image_category_width; ?>"
					size="3"/>
				x
				<input type="text" name="config_image_category_height" value="<?= $config_image_category_height; ?>"
					size="3"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Manufacturer Image Size:"); ?></td>
			<td><input type="text" name="config_image_manufacturer_width" value="<?= $config_image_manufacturer_width; ?>" size="3"/>
				x
				<input type="text" name="config_image_manufacturer_height" value="<?= $config_image_manufacturer_height; ?>" size="3"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Product Image Thumb Size:"); ?></td>
			<td><input type="text" name="config_image_thumb_width" value="<?= $config_image_thumb_width; ?>" size="3"/>
				x
				<input type="text" name="config_image_thumb_height" value="<?= $config_image_thumb_height; ?>" size="3"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Product Image Popup Size:"); ?></td>
			<td><input type="text" name="config_image_popup_width" value="<?= $config_image_popup_width; ?>" size="3"/>
				x
				<input type="text" name="config_image_popup_height" value="<?= $config_image_popup_height; ?>" size="3"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Product Image List Size:"); ?></td>
			<td><input type="text" name="config_image_product_width" value="<?= $config_image_product_width; ?>"
					size="3"/>
				x
				<input type="text" name="config_image_product_height" value="<?= $config_image_product_height; ?>"
					size="3"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Product Options Image Size:"); ?></td>
			<td><input type="text" name="config_image_product_option_width" value="<?= $config_image_product_option_width; ?>" size="3"/>
				x
				<input type="text" name="config_image_product_option_height" value="<?= $config_image_product_option_height; ?>" size="3"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Additional Product Image Size:"); ?></td>
			<td><input type="text" name="config_image_additional_width" value="<?= $config_image_additional_width; ?>"
					size="3"/>
				x
				<input type="text" name="config_image_additional_height" value="<?= $config_image_additional_height; ?>"
					size="3"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Related Product Image Size:"); ?></td>
			<td><input type="text" name="config_image_related_width" value="<?= $config_image_related_width; ?>"
					size="3"/>
				x
				<input type="text" name="config_image_related_height" value="<?= $config_image_related_height; ?>"
					size="3"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Compare Image Size:"); ?></td>
			<td><input type="text" name="config_image_compare_width" value="<?= $config_image_compare_width; ?>"
					size="3"/>
				x
				<input type="text" name="config_image_compare_height" value="<?= $config_image_compare_height; ?>"
					size="3"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Wish List Image Size:"); ?></td>
			<td><input type="text" name="config_image_wishlist_width" value="<?= $config_image_wishlist_width; ?>"
					size="3"/>
				x
				<input type="text" name="config_image_wishlist_height" value="<?= $config_image_wishlist_height; ?>"
					size="3"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Cart Image Size:"); ?></td>
			<td><input type="text" name="config_image_cart_width" value="<?= $config_image_cart_width; ?>" size="3"/>
				x
				<input type="text" name="config_image_cart_height" value="<?= $config_image_cart_height; ?>" size="3"/>
		</tr>
	</table>
</div>
<div id="tab-mail">
	<table class="form">
		<tr>
			<td><?= _l("Mail Protocol:<span class=\"help\">Only choose \'Mail\' unless your host has disabled the php mail function."); ?></td>
			<td><?= $this->builder->build('select', $data_mail_protocols, "config_mail_protocol", $config_mail_protocol); ?></td>
		</tr>
		<tr>
			<td><?= _l("Mail Parameters:<span class=\"help\">When using \'Mail\', additional mail parameters can be added here (e.g. \"-femail@storeaddress.com\"."); ?></td>
			<td><input type="text" name="config_mail_parameter" value="<?= $config_mail_parameter; ?>"/></td>
		</tr>
		<tr>
			<td><?= _l("SMTP Host:"); ?></td>
			<td><input type="text" name="config_smtp_host" value="<?= $config_smtp_host; ?>"/></td>
		</tr>
		<tr>
			<td><?= _l("SMTP Username:"); ?></td>
			<td><input type="text" name="config_smtp_username" value="<?= $config_smtp_username; ?>"/></td>
		</tr>
		<tr>
			<td><?= _l("SMTP Password:"); ?></td>
			<td><input type="text" name="config_smtp_password" value="<?= $config_smtp_password; ?>"/></td>
		</tr>
		<tr>
			<td><?= _l("SMTP Port:"); ?></td>
			<td><input type="text" name="config_smtp_port" value="<?= $config_smtp_port; ?>"/></td>
		</tr>
		<tr>
			<td><?= _l("SMTP Timeout:"); ?></td>
			<td><input type="text" name="config_smtp_timeout" value="<?= $config_smtp_timeout; ?>"/></td>
		</tr>
		<tr>
			<td><?= _l("New Order Alert Mail:<br /><span class=\"help\">Send a email to the store owner when a new order is created.</span>"); ?></td>
			<td><?= $this->builder->build('radio', $data_yes_no, 'config_alert_mail', $config_alert_mail); ?></td>
		</tr>
		<tr>
			<td><?= _l("New Account Alert Mail:<br /><span class=\"help\">Send a email to the store owner when a new account is registered.</span>"); ?></td>
			<td><?= $this->builder->build('radio', $data_yes_no, 'config_account_mail', $config_account_mail); ?></td>
		</tr>
		<tr>
			<td><?= _l("Additional Alert E-Mails:<br /><span class=\"help\">Any additional emails you want to receive the alert email, in addition to the main store email. (comma separated)</span>"); ?></td>
			<td><textarea name="config_alert_emails" cols="40" rows="5"><?= $config_alert_emails; ?></textarea></td>
		</tr>
		<tr>
			<td><?= _l("Enable Mail Logging"); ?></td>
			<td><?= $this->builder->build('radio', $data_yes_no, 'config_mail_logging', $config_mail_logging); ?></td>
		</tr>
	</table>
</div>
<div id="tab-fraud">
	<table class="form">
		<tr>
			<td><?= _l("Use MaxMind Fraud Detection System:<br /><span class=\"help\">MaxMind is a fraud detections service. If you don\'t have a license key you can <a target=\"_blank\" href=\"http://www.maxmind.com/?rId=opencart\">sign up here</a>. Once you have obtained a key copy and paste it into the field below.</span>"); ?></td>
			<td><? if ($config_fraud_detection) { ?>
					<input type="radio" name="config_fraud_detection" value="1" checked="checked"/>
					<?= _l("Yes"); ?>
					<input type="radio" name="config_fraud_detection" value="0"/>
					<?= _l("No"); ?>
				<? } else { ?>
					<input type="radio" name="config_fraud_detection" value="1"/>
					<?= _l("Yes"); ?>
					<input type="radio" name="config_fraud_detection" value="0" checked="checked"/>
					<?= _l("No"); ?>
				<? } ?></td>
		</tr>
		<tr>
			<td><?= _l("MaxMind License Key:</span>"); ?></td>
			<td><input type="text" name="config_fraud_key" value="<?= $config_fraud_key; ?>"/></td>
		</tr>
		<tr>
			<td><?= _l("MaxMind Risk Score:<br /><span class=\"help\">The higher the score the more likly the order is fraudulent. Set a score between 0 - 100.</span>"); ?></td>
			<td><input type="text" name="config_fraud_score" value="<?= $config_fraud_score; ?>"/></td>
		</tr>
	</table>
</div>
<div id="tab-file-permissions">
	<table class="form">
		<tr>
			<td></td>
			<td>
				<table class="mode_explanation">
					<tbody>
						<tr><?= _l("The file permissions are set user (owner), group, others == ugo == 755 == user has full, group has read & write, others have read & write permissions."); ?></tr>
						<tr>
							<th>#</th>
							<th>Permission</th>
							<th>rwx</th>
						</tr>
						<tr>
							<td>7</td>
							<td>full</td>
							<td>111</td>
						</tr>
						<tr>
							<td>6</td>
							<td>read and write</td>
							<td>110</td>
						</tr>
						<tr>
							<td>5</td>
							<td>read and execute</td>
							<td>101</td>
						</tr>
						<tr>
							<td>4</td>
							<td>read only</td>
							<td>100</td>
						</tr>
						<tr>
							<td>3</td>
							<td>write and execute</td>
							<td>011</td>
						</tr>
						<tr>
							<td>2</td>
							<td>write only</td>
							<td>010</td>
						</tr>
						<tr>
							<td>1</td>
							<td>execute only</td>
							<td>001</td>
						</tr>
						<tr>
							<td>0</td>
							<td>none</td>
							<td>000</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td><?= _l("Default File Permissions <span class=\'help\'>These are the permissions set for system generated files and directories</span>"); ?></td>
			<td>
				<label for="default_file_mode"><?= _l("Default File Permissions"); ?></label>
				<input id="default_file_mode" type="text" size="3" maxlength="3" name="config_default_file_mode" value="<?= $config_default_file_mode; ?>"/>
				<label for="default_dir_mode"><?= _l("Default Directory Permissions"); ?></label>
				<input id="default_dir_mode" type="text" size="3" maxlength="3" name="config_default_dir_mode" value="<?= $config_default_dir_mode; ?>"/>
			</td>
		</tr>
		<tr>
			<td><?= _l("Image File Permissions <span class=\'help\'>These are the permissions set for system generated image files and directories</span>"); ?></td>
			<td>
				<label for="image_file_mode"><?= _l("Image File Permissions"); ?></label>
				<input id="image_file_mode" type="text" size="3" maxlength="3" name="config_image_file_mode" value="<?= $config_image_file_mode; ?>"/>
				<label for="_dir_mode"><?= _l("Image Directory Permissions"); ?></label>
				<input id="image_dir_mode" type="text" size="3" maxlength="3" name="config_image_dir_mode" value="<?= $config_image_dir_mode; ?>"/>
			</td>
		</tr>
		<tr>
			<td><?= _l("Plugin File Permissions <span class=\'help\'>These are the permissions set for system generated plugin files and directories</span>"); ?></td>
			<td>
				<label for="plugin_file_mode"><?= _l("Plugin File Permissions"); ?></label>
				<input id="plugin_file_mode" type="text" size="3" maxlength="3" name="config_plugin_file_mode" value="<?= $config_plugin_file_mode; ?>"/>
				<label for="_dir_mode"><?= _l("Plugin Directory Permissions"); ?></label>
				<input id="plugin_dir_mode" type="text" size="3" maxlength="3" name="config_plugin_dir_mode" value="<?= $config_plugin_dir_mode; ?>"/>
			</td>
		</tr>
	</table>
</div>
<div id="tab-server">
	<table class="form">
		<tr>
			<td><?= _l("Turn on Global Debug:<span class=\"help\">Should be turned off for production sites.</span>"); ?></td>
			<td><?= $this->builder->build('select', $data_yes_no, 'config_debug', (int)$config_debug); ?></td>
		</tr>
		<tr>
			<td><?= _l("Use the jQuery CDN:<span class=\"help\">This will load jQuery and jQuery UI from the jQuery Content Delivery Network. Recommended for production sites</span>"); ?></td>
			<td><?= $this->builder->build('select', $data_yes_no, 'config_jquery_cdn', (int)$config_jquery_cdn); ?></td>
		</tr>
		<tr>
			<td><?= _l("Send Emails to third parties? <span class=\"help\">Emails sent to people other than the current user and the system emails</span>"); ?></td>
			<td><?= $this->builder->build('select', $data_yes_no, 'config_debug_send_emails', (int)$config_debug_send_emails); ?></td>
		</tr>
		<tr>
			<td><?= _l("Use SSL:<br /><span class=\"help\">To use SSL check with your host if a SSL certificate is installed and added the SSL URL to the catalog and admin config files.</span>"); ?></td>
			<td><? if ($config_use_ssl) { ?>
					<input type="radio" name="config_use_ssl" value="1" checked="checked"/>
					<?= _l("Yes"); ?>
					<input type="radio" name="config_use_ssl" value="0"/>
					<?= _l("No"); ?>
				<? } else { ?>
					<input type="radio" name="config_use_ssl" value="1"/>
					<?= _l("Yes"); ?>
					<input type="radio" name="config_use_ssl" value="0" checked="checked"/>
					<?= _l("No"); ?>
				<? } ?></td>
		</tr>
		<tr>
			<td><?= _l("Use SEO URL\'s:<br /><span class=\"help\">To use SEO URL\'s apache module mod-rewrite must be installed and you need to rename the htaccess.txt to .htaccess.</span>"); ?></td>
			<td><? if ($config_seo_url) { ?>
					<input type="radio" name="config_seo_url" value="1" checked="checked"/>
					<?= _l("Yes"); ?>
					<input type="radio" name="config_seo_url" value="0"/>
					<?= _l("No"); ?>
				<? } else { ?>
					<input type="radio" name="config_seo_url" value="1"/>
					<?= _l("Yes"); ?>
					<input type="radio" name="config_seo_url" value="0" checked="checked"/>
					<?= _l("No"); ?>
				<? } ?></td>
		</tr>
		<tr>
			<td><?= _l("Maintenance Mode:<br /><span class=\"help\">Prevents customers from browsing your store. They will instead see a maintenance message. If logged in as admin, you will see the store as normal.</span>"); ?></td>
			<td><? if ($config_maintenance) { ?>
					<input type="radio" name="config_maintenance" value="1" checked="checked"/>
					<?= _l("Yes"); ?>
					<input type="radio" name="config_maintenance" value="0"/>
					<?= _l("No"); ?>
				<? } else { ?>
					<input type="radio" name="config_maintenance" value="1"/>
					<?= _l("Yes"); ?>
					<input type="radio" name="config_maintenance" value="0" checked="checked"/>
					<?= _l("No"); ?>
				<? } ?></td>
		</tr>
		<tr>
			<td><?= _l("Image Resize Max Memory<span class=\"help\">The maximum allowed memory when resizing images for the cache. Must be in php memory format (eg: 128M, 512M, 1G, etc.)</span>"); ?></td>
			<td><input type="text" name="config_image_max_mem" value="<?= $config_image_max_mem; ?>"/></td>
		</tr>
		<tr>
			<td><?= _l("Encryption Key:<br /><span class=\"help\">Please provide a secret key that will be used to encrypt private information when processing orders.</span>"); ?></td>
			<td><input type="text" name="config_encryption" value="<?= $config_encryption; ?>"/></td>
		</tr>
		<tr>
			<td><?= _l("Output Compression Level:<br /><span class=\"help\">GZIP for more efficient transfer to requesting clients. Compression level must be between 0 - 9</span>"); ?></td>
			<td><input type="text" name="config_compression" value="<?= $config_compression; ?>" size="3"/></td>
		</tr>
		<tr>
			<td class="required"> <?= _l("Log Filename:"); ?></td>
			<td><input type="text" name="config_log_filename" value="<?= $config_log_filename; ?>"/>
		</tr>
		<tr>
			<td class="required"> <?= _l("Error Log Filename:"); ?></td>
			<td><input type="text" name="config_error_filename" value="<?= $config_error_filename; ?>"/>
		</tr>
		<tr>
			<td><?= _l("Display Errors:"); ?></td>
			<td><?= $this->builder->build('radio', $data_yes_no, 'config_error_display', $config_error_display); ?></td>
		</tr>
		<tr>
			<td><?= _l("Log Errors:"); ?></td>
			<td><?= $this->builder->build('radio', $data_yes_no, 'config_error_log', $config_error_log); ?></td>
		</tr>
		<tr>
			<td><?= _l("Google Analytics Code:<br /><span class=\"help\">Login to your <a target=\"_blank\" href=\"http://www.google.com/analytics/\">Google Analytics</a> account and after creating your web site profile copy and paste the analytics code into this field.</span>"); ?></td>
			<td><textarea name="config_google_analytics" cols="40" rows="5"><?= $config_google_analytics; ?></textarea>
			</td>
		</tr>
		<tr>
			<td><?= _l("Stat Counter Code:<span class=\"help\">Sign up at <a target=\"_blank\" href=\"http://www.statcounter.com/sign-up/\">Stat Counter</a> and copy and past the code in this field.</span>"); ?></td>
			<td>
				<label for="statcounter_project"><?= _l("Project ID"); ?></label><br/>
				<input type="text" name="config_statcounter[project]" value="<?= $config_statcounter['project']; ?>"/><br/><br/>
				<label for="statcounter_project"><?= _l("Security Code"); ?></label><br/>
				<input type="text" name="config_statcounter[security]" value="<?= $config_statcounter['security']; ?>"/>
			</td>
		</tr>
	</table>
</div>
</form>
</div>
</div>
</div>

<script type="text/javascript">
	$('[name=config_theme]').change(function () {
		$('#theme').load('<?= $load_theme_img; ?>' + '&theme=' + $(this).val());
	}).change();
</script>

<?= $this->builder->js('load_zones', 'table.form', '.country_select', '.zone_select'); ?>

<script type="text/javascript">
	$('#tabs a').tabs();
</script>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
