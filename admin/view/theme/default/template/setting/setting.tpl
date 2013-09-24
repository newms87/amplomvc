<?= $header; ?>
	<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
	<div class="heading">
		<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= $head_title; ?></h1>

		<div class="buttons">
			<a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a>
			<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
		</div>
	</div>
	<div class="section">
	<div id="tabs" class="htabs">
		<a href="#tab-general"><?= $tab_general; ?></a>
		<a href="#tab-store"><?= $tab_store; ?></a>
		<a href="#tab-local"><?= $tab_local; ?></a>
		<a href="#tab-option"><?= $tab_option; ?></a>
		<a href="#tab-image"><?= $tab_image; ?></a>
		<a href="#tab-mail"><?= $tab_mail; ?></a>
		<a href="#tab-fraud"><?= $tab_fraud; ?></a>
		<a href="#tab-file-permissions"><?= $tab_file_permissions; ?></a>
		<a href="#tab-server"><?= $tab_server; ?></a>
	</div>
	<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
	<div id="tab-general">
		<table class="form">
			<tr>
				<td class="required"> <?= $entry_name; ?></td>
				<td><input type="text" name="config_name" value="<?= $config_name; ?>" size="40"/>
			</tr>
			<tr>
				<td class="required"> <?= $entry_owner; ?></td>
				<td><input type="text" name="config_owner" value="<?= $config_owner; ?>" size="40"/>
			</tr>
			<tr>
				<td class="required"> <?= $entry_address; ?></td>
				<td><textarea name="config_address" cols="40" rows="5"><?= $config_address; ?></textarea>
			</tr>
			<tr>
				<td class="required"> <?= $entry_email; ?></td>
				<td><input type="text" name="config_email" value="<?= $config_email; ?>" size="40"/>
			</tr>
			<tr>
				<td class="required"> <?= $entry_email_support; ?></td>
				<td><input type="text" name="config_email_support" value="<?= $config_email_support; ?>" size="40"/>
			</tr>
			<tr>
				<td class="required"> <?= $entry_email_error; ?></td>
				<td><input type="text" name="config_email_error" value="<?= $config_email_error; ?>" size="40"/>
			</tr>
			<tr>
				<td class="required"> <?= $entry_telephone; ?></td>
				<td><input type="text" name="config_telephone" value="<?= $config_telephone; ?>"/>
			</tr>
			<tr>
				<td><?= $entry_fax; ?></td>
				<td><input type="text" name="config_fax" value="<?= $config_fax; ?>"/></td>
			</tr>
		</table>
	</div>
	<div id="tab-store">
		<table class="form">
			<tr>
				<td class="required"> <?= $entry_title; ?></td>
				<td><input type="text" name="config_title" value="<?= $config_title; ?>"/>
			</tr>
			<tr>
				<td><?= $entry_meta_description; ?></td>
				<td><textarea name="config_meta_description" cols="40" rows="5"><?= $config_meta_description; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><?= $entry_theme; ?></td>
				<td>
					<? $this->builder->set_config('name', 'name'); ?>
					<?= $this->builder->build('select', $themes, 'config_theme', $config_theme); ?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td id="theme"></td>
			</tr>
			<tr>
				<td><?= $entry_default_store; ?></td>
				<td>
					<? $this->builder->set_config('store_id', 'name'); ?>
					<?= $this->builder->build('select', $stores, 'config_default_store', $config_default_store); ?>
				</td>
			</tr>
			<tr>
				<td><?= $entry_layout; ?></td>
				<? $this->builder->set_config('layout_id', 'name'); ?>
				<td><?= $this->builder->build('select', $data_layouts, 'config_default_layout_id', $config_default_layout_id); ?></td>
			</tr>
		</table>
	</div>
	<div id="tab-local">
		<table class="form">
			<tr>
				<td><?= $entry_address_format; ?></td>
				<td><textarea name="config_address_format" cols="40" rows="5"><?= $config_address_format; ?></textarea></td>
			</tr>
			<tr>
				<td><?= $entry_country; ?></td>
				<td>
					<?= $this->builder->set_config('country_id', 'name'); ?>
					<?= $this->builder->build('select', $countries, "config_country_id", $config_country_id, array('class' => "country_select")); ?>
				</td>
			</tr>
			<tr>
				<td><?= $entry_zone; ?></td>
				<td><select name="config_zone_id" class="zone_select" zone_id="<?= $config_zone_id; ?>"></select></td>
			</tr>
			<tr>
				<td><?= $entry_language; ?></td>
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
				<td><?= $entry_admin_language; ?></td>
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
				<td><?= $entry_use_macro_languages; ?></td>
				<td><?= $this->builder->build('select', $data_yes_no, 'config_use_macro_languages', $config_use_macro_languages); ?></td>
			</tr>
			<tr>
				<td><?= $entry_currency; ?></td>
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
				<td><?= $entry_currency_auto; ?></td>
				<td><? if ($config_currency_auto) { ?>
						<input type="radio" name="config_currency_auto" value="1" checked="checked"/>
						<?= $text_yes; ?>
						<input type="radio" name="config_currency_auto" value="0"/>
						<?= $text_no; ?>
					<? } else { ?>
						<input type="radio" name="config_currency_auto" value="1"/>
						<?= $text_yes; ?>
						<input type="radio" name="config_currency_auto" value="0" checked="checked"/>
						<?= $text_no; ?>
					<? } ?></td>
			</tr>
			<tr>
				<td><?= $entry_length_class; ?></td>
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
				<td><?= $entry_weight_class; ?></td>
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
		<td class="required"> <?= $entry_breadcrumb_display; ?></td>
		<td><?= $this->builder->build('select', $data_yes_no, "config_breadcrumb_display", $config_breadcrumb_display); ?></td>
	</tr>
	<tr>
		<td class="required"> <?= $entry_breadcrumb_separator; ?></td>
		<td><input type="text" style='font-size:20px' name="config_breadcrumb_separator" value="<?= $config_breadcrumb_separator; ?>" size="1"/>
	</tr>
	<tr>
		<td class="required"> <?= $entry_breadcrumb_separator_admin; ?></td>
		<td><input type="text" style='font-size:20px' name="config_breadcrumb_separator_admin" value="<?= $config_breadcrumb_separator_admin; ?>" size="1"/>
	</tr>
	<tr>
		<td class="required"> <?= $entry_catalog_limit; ?></td>
		<td><input type="text" name="config_catalog_limit" value="<?= $config_catalog_limit; ?>" size="3"/>
	</tr>
	<tr>
		<td class="required"> <?= $entry_admin_limit; ?></td>
		<td><input type="text" name="config_admin_limit" value="<?= $config_admin_limit; ?>" size="3"/>
	</tr>
	<tr>
		<td class="required"> <?= $entry_autocomplete_limit; ?></td>
		<td><input type="text" name="config_autocomplete_limit" value="<?= $config_autocomplete_limit; ?>" size="3"/>
	</tr>
	<tr>
		<td><?= $entry_performance_log; ?></td>
		<td><?= $this->builder->build('select', $data_statuses, 'config_performance_log', $config_performance_log); ?></td>
	</tr>
	<tr>
		<td><?= $entry_default_return_policy; ?></td>
		<td>
			<? if (!empty($data_return_policies)) { ?>
				<? $this->builder->set_config(false, 'title'); ?>
				<?= $this->builder->build('select', $data_return_policies, 'config_default_return_policy', $config_default_return_policy); ?>
			<? } ?>
			<p><?= $text_add_return_policy; ?></p>
		</td>
	</tr>
	<tr>
		<td><?= $entry_default_shipping_policy; ?></td>
		<td>
			<? if (!empty($data_shipping_policies)) { ?>
				<? $this->builder->set_config(false, 'title'); ?>
				<?= $this->builder->build('select', $data_shipping_policies, 'config_default_shipping_policy', $config_default_shipping_policy); ?>
			<? } ?>
			<p><?= $text_add_shipping_policy; ?></p>
		</td>
	</tr>
	<tr>
		<td><?= $entry_shipping_return_policy_info; ?></td>
		<td>
			<? $this->builder->set_config('information_id', 'title'); ?>
			<?= $this->builder->build('select', $data_informations, 'config_shipping_return_info_id', $config_shipping_return_info_id); ?>
		</td>
	<tr>
		<td><?= $entry_cache_ignore; ?></td>
		<td><textarea name='config_cache_ignore'><?= $config_cache_ignore; ?></textarea></td>
	</tr>
	<tr>
		<td><?= $entry_allow_close_message_box; ?></td>
		<td><?= $this->builder->build('radio', $data_yes_no, 'config_allow_close_message_box', $config_allow_close_message_box); ?></td>
	</tr>
	<tr>
		<td><?= $entry_show_category_image; ?></td>
		<td><?= $this->builder->build('radio', $data_yes_no, 'config_show_category_image', $config_show_category_image); ?></td>
	</tr>
	<tr>
		<td><?= $entry_show_category_description; ?></td>
		<td><?= $this->builder->build('radio', $data_yes_no, 'config_show_category_description', $config_show_category_description); ?></td>
	</tr>
	<tr>
		<td><?= $entry_show_product_list_hover_image; ?></td>
		<td><?= $this->builder->build('radio', $data_yes_no, 'config_show_product_list_hover_image', $config_show_product_list_hover_image); ?></td>
	</tr>
	<tr>
		<td><?= $entry_cart_show_return_policy; ?></td>
		<td><?= $this->builder->build('radio', $data_yes_no, 'config_cart_show_return_policy', $config_cart_show_return_policy); ?></td>
	</tr>
	<tr>
		<td><?= $entry_tax; ?></td>
		<td><? if ($config_show_price_with_tax) { ?>
				<input type="radio" name="config_show_price_with_tax" value="1" checked="checked"/>
				<?= $text_yes; ?>
				<input type="radio" name="config_show_price_with_tax" value="0"/>
				<?= $text_no; ?>
			<? } else { ?>
				<input type="radio" name="config_show_price_with_tax" value="1"/>
				<?= $text_yes; ?>
				<input type="radio" name="config_show_price_with_tax" value="0" checked="checked"/>
				<?= $text_no; ?>
			<? } ?></td>
	</tr>
	<tr>
		<td><?= $entry_tax_default_id; ?></td>
		<td>
			<? $this->builder->set_config('tax_class_id', 'title'); ?>
			<?= $this->builder->build('select', $tax_classes, 'config_tax_default_id', $config_tax_default_id); ?>
		</td>
	</tr>
	<tr>
		<td><?= $entry_tax_default; ?></td>
		<td><select name="config_tax_default">
				<option value=""><?= $text_none; ?></option>
				<? if ($config_tax_default == 'shipping') { ?>
					<option value="shipping" selected="selected"><?= $text_shipping; ?></option>
				<? } else { ?>
					<option value="shipping"><?= $text_shipping; ?></option>
				<? } ?>
				<? if ($config_tax_default == 'payment') { ?>
					<option value="payment" selected="selected"><?= $text_payment; ?></option>
				<? } else { ?>
					<option value="payment"><?= $text_payment; ?></option>
				<? } ?>
			</select></td>
	</tr>
	<tr>
		<td><?= $entry_tax_customer; ?></td>
		<td><select name="config_tax_customer">
				<option value=""><?= $text_none; ?></option>
				<? if ($config_tax_customer == 'shipping') { ?>
					<option value="shipping" selected="selected"><?= $text_shipping; ?></option>
				<? } else { ?>
					<option value="shipping"><?= $text_shipping; ?></option>
				<? } ?>
				<? if ($config_tax_customer == 'payment') { ?>
					<option value="payment" selected="selected"><?= $text_payment; ?></option>
				<? } else { ?>
					<option value="payment"><?= $text_payment; ?></option>
				<? } ?>
			</select></td>
	</tr>
	<tr>
		<td><?= $entry_invoice_prefix; ?></td>
		<td><input type="text" name="config_invoice_prefix" value="<?= $config_invoice_prefix; ?>"/></td>
	</tr>
	<tr>
		<td><?= $entry_order_edit; ?></td>
		<td><input type="text" name="config_order_edit" value="<?= $config_order_edit; ?>" size="3"/></td>
	</tr>
	<tr>
		<td><?= $entry_customer_group; ?></td>
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
		<td><?= $entry_customer_price; ?></td>
		<td><?= $this->builder->build('radio', $data_yes_no, 'config_customer_hide_price', $config_customer_hide_price); ?></td>
	</tr>
	<tr>
		<td><?= $entry_customer_approval; ?></td>
		<td><?= $this->builder->build('radio', $data_yes_no, 'config_customer_approval', $config_customer_approval); ?></td>
	</tr>
	<tr>
		<td><?= $entry_guest_checkout; ?></td>
		<td><?= $this->builder->build('radio', $data_yes_no, 'config_guest_checkout', $config_guest_checkout); ?></td>
	</tr>
	<tr>
		<td><?= $entry_account; ?></td>
		<td>
			<? $this->builder->set_config('information_id', 'title'); ?>
			<?= $this->builder->build('select', $data_informations, 'config_account_terms_info_id', $config_account_terms_info_id); ?>
		</td>
	</tr>
	<tr>
		<td><?= $entry_checkout; ?></td>
		<td>
			<? $this->builder->set_config('information_id', 'title'); ?>
			<?= $this->builder->build('select', $data_informations, 'config_checkout_terms_info_id', $config_checkout_terms_info_id); ?>
		</td>
	</tr>
	<tr>
		<td><?= $entry_affiliate; ?></td>
		<td>
			<? $this->builder->set_config('information_id', 'title'); ?>
			<?= $this->builder->build('select', $data_informations, 'config_affiliate_terms_info_id', $config_affiliate_terms_info_id); ?>
		</td>
	</tr>
	<tr>
		<td><?= $entry_commission; ?></td>
		<td><input type="text" name="config_commission" value="<?= $config_commission; ?>" size="3"/></td>
	</tr>
	<tr>
		<td><?= $entry_stock_display; ?></td>
		<td><?= $this->builder->build('radio', $data_stock_display_types, "config_stock_display", $config_stock_display, array('class' => 'display_stock_radio')); ?></td>
	</tr>
	<tr>
		<td><?= $entry_stock_warning; ?></td>
		<td><?= $this->builder->build('select', $data_yes_no, 'config_stock_warning', $config_stock_warning); ?></td>
	</tr>
	<tr>
		<td><?= $entry_stock_checkout; ?></td>
		<td><?= $this->builder->build('radio', $data_yes_no, 'config_stock_checkout', $config_stock_checkout); ?></td>
	</tr>
	<tr>
		<td><?= $entry_stock_status; ?></td>
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
		<td><?= $entry_show_product_related; ?></td>
		<td><?= $this->builder->build('select', $data_show_product_related, 'config_show_product_related', $config_show_product_related); ?></td>
	</tr>
	<tr>
		<td><?= $entry_order_received_status; ?></td>
		<td>
			<?= $this->builder->set_config(false, 'title'); ?>
			<?= $this->builder->build('select', $data_order_statuses, 'config_order_received_status_id', $config_order_received_status_id); ?>
		</td>
	</tr>
	<tr>
		<td><?= $entry_order_complete_status; ?></td>
		<td>
			<?= $this->builder->set_config(false, 'title'); ?>
			<?= $this->builder->build('select', $data_order_statuses, 'config_order_complete_status_id', $config_order_complete_status_id); ?>
		</td>
	</tr>
	<tr>
		<td><?= $entry_order_blacklist_status; ?></td>
		<td>
			<?= $this->builder->set_config(false, 'title'); ?>
			<?= $this->builder->build('select', $data_order_statuses, 'config_order_blacklist_status_id', $config_order_blacklist_status_id); ?>
		</td>
	</tr>
	<tr>
		<td><?= $entry_order_fraud_status; ?></td>
		<td>
			<?= $this->builder->set_config(false, 'title'); ?>
			<?= $this->builder->build('select', $data_order_statuses, 'config_order_fraud_status_id', $config_order_fraud_status_id); ?>
		</td>
	</tr>
	<tr>
		<td><?= $entry_return_status; ?></td>
		<? $this->builder->set_config(false, 'title'); ?>
		<td><?= $this->builder->build('select', $data_return_statuses, 'config_return_status_id', $config_return_status_id); ?></td>
	</tr>
	<tr>
		<td><?= $entry_review; ?></td>
		<td><? if ($config_review_status) { ?>
				<input type="radio" name="config_review_status" value="1" checked="checked"/>
				<?= $text_yes; ?>
				<input type="radio" name="config_review_status" value="0"/>
				<?= $text_no; ?>
			<? } else { ?>
				<input type="radio" name="config_review_status" value="1"/>
				<?= $text_yes; ?>
				<input type="radio" name="config_review_status" value="0" checked="checked"/>
				<?= $text_no; ?>
			<? } ?></td>
	</tr>
	<tr>
		<td><?= $entry_allow_sharing; ?></td>
		<td><?= $this->builder->build('select', $data_yes_no, "config_share_status", $config_share_status); ?></td>
	</tr>
	<td><?= $entry_show_product_attributes; ?></td>
	<td><?= $this->builder->build('select', $data_yes_no, "config_show_product_attributes", $config_show_product_attributes); ?></td>
	</tr>
	<tr>
		<td><?= $entry_download; ?></td>
		<td><?= $this->builder->build('select', $data_yes_no, "config_download", $config_download); ?></td>
	</tr>
	<tr>
		<td><?= $entry_upload_allowed; ?></td>
		<td><textarea name="config_upload_allowed" cols="40" rows="5"><?= $config_upload_allowed; ?></textarea></td>
	</tr>
	<tr>
		<td><?= $entry_upload_images_allowed; ?></td>
		<td><textarea name="config_upload_images_allowed" cols="40"
		              rows="5"><?= $config_upload_images_allowed; ?></textarea></td>
	</tr>
	<tr>
		<td><?= $entry_upload_images_mime_types_allowed; ?></td>
		<td><textarea name="config_upload_images_mime_types_allowed" cols="40"
		              rows="5"><?= $config_upload_images_mime_types_allowed; ?></textarea></td>
	</tr>
	<tr>
		<td><?= $entry_cart_weight; ?></td>
		<td><? if ($config_cart_weight) { ?>
				<input type="radio" name="config_cart_weight" value="1" checked="checked"/>
				<?= $text_yes; ?>
				<input type="radio" name="config_cart_weight" value="0"/>
				<?= $text_no; ?>
			<? } else { ?>
				<input type="radio" name="config_cart_weight" value="1"/>
				<?= $text_yes; ?>
				<input type="radio" name="config_cart_weight" value="0" checked="checked"/>
				<?= $text_no; ?>
			<? } ?></td>
	</tr>
	</table>
	</div>
	<div id="tab-image">
		<table class="form">
			<tr>
				<td><?= $entry_admin_logo; ?></td>
				<td>
					<?= $this->builder->set_builder_template('click_image'); ?>
					<?= $this->builder->image_input("config_admin_logo", $config_admin_logo); ?>
				</td>
			</tr>
			<tr>
				<td><?= $entry_icon; ?></td>
				<td>
					<?= $this->builder->set_builder_template('click_image'); ?>
					<?= $this->builder->image_input("config_icon", $config_icon); ?>
				</td>
			</tr>
			<tr>
				<td class="required"> <?= $entry_image_admin_thumb; ?></td>
				<td><input type="text" name="config_image_admin_thumb_width" value="<?= $config_image_admin_thumb_width; ?>"
				           size="3"/>
					x
					<input type="text" name="config_image_admin_thumb_height" value="<?= $config_image_admin_thumb_height; ?>" size="3"/>
			</tr>
			<tr>
				<td class="required"> <?= $entry_image_admin_list; ?></td>
				<td><input type="text" name="config_image_admin_list_width" value="<?= $config_image_admin_list_width; ?>"
				           size="3"/>
					x
					<input type="text" name="config_image_admin_list_height" value="<?= $config_image_admin_list_height; ?>"
					       size="3"/>
			</tr>
			<tr>
				<td class="required"> <?= $entry_image_category; ?></td>
				<td><input type="text" name="config_image_category_width" value="<?= $config_image_category_width; ?>"
				           size="3"/>
					x
					<input type="text" name="config_image_category_height" value="<?= $config_image_category_height; ?>"
					       size="3"/>
			</tr>
			<tr>
				<td class="required"> <?= $entry_image_manufacturer; ?></td>
				<td><input type="text" name="config_image_manufacturer_width" value="<?= $config_image_manufacturer_width; ?>" size="3"/>
					x
					<input type="text" name="config_image_manufacturer_height" value="<?= $config_image_manufacturer_height; ?>" size="3"/>
			</tr>
			<tr>
				<td class="required"> <?= $entry_image_thumb; ?></td>
				<td><input type="text" name="config_image_thumb_width" value="<?= $config_image_thumb_width; ?>" size="3"/>
					x
					<input type="text" name="config_image_thumb_height" value="<?= $config_image_thumb_height; ?>" size="3"/>
			</tr>
			<tr>
				<td class="required"> <?= $entry_image_popup; ?></td>
				<td><input type="text" name="config_image_popup_width" value="<?= $config_image_popup_width; ?>" size="3"/>
					x
					<input type="text" name="config_image_popup_height" value="<?= $config_image_popup_height; ?>" size="3"/>
			</tr>
			<tr>
				<td class="required"> <?= $entry_image_product; ?></td>
				<td><input type="text" name="config_image_product_width" value="<?= $config_image_product_width; ?>"
				           size="3"/>
					x
					<input type="text" name="config_image_product_height" value="<?= $config_image_product_height; ?>"
					       size="3"/>
			</tr>
			<tr>
				<td class="required"> <?= $entry_image_product_option; ?></td>
				<td><input type="text" name="config_image_product_option_width" value="<?= $config_image_product_option_width; ?>" size="3"/>
					x
					<input type="text" name="config_image_product_option_height" value="<?= $config_image_product_option_height; ?>" size="3"/>
			</tr>
			<tr>
				<td class="required"> <?= $entry_image_additional; ?></td>
				<td><input type="text" name="config_image_additional_width" value="<?= $config_image_additional_width; ?>"
				           size="3"/>
					x
					<input type="text" name="config_image_additional_height" value="<?= $config_image_additional_height; ?>"
					       size="3"/>
			</tr>
			<tr>
				<td class="required"> <?= $entry_image_related; ?></td>
				<td><input type="text" name="config_image_related_width" value="<?= $config_image_related_width; ?>"
				           size="3"/>
					x
					<input type="text" name="config_image_related_height" value="<?= $config_image_related_height; ?>"
					       size="3"/>
			</tr>
			<tr>
				<td class="required"> <?= $entry_image_compare; ?></td>
				<td><input type="text" name="config_image_compare_width" value="<?= $config_image_compare_width; ?>"
				           size="3"/>
					x
					<input type="text" name="config_image_compare_height" value="<?= $config_image_compare_height; ?>"
					       size="3"/>
			</tr>
			<tr>
				<td class="required"> <?= $entry_image_wishlist; ?></td>
				<td><input type="text" name="config_image_wishlist_width" value="<?= $config_image_wishlist_width; ?>"
				           size="3"/>
					x
					<input type="text" name="config_image_wishlist_height" value="<?= $config_image_wishlist_height; ?>"
					       size="3"/>
			</tr>
			<tr>
				<td class="required"> <?= $entry_image_cart; ?></td>
				<td><input type="text" name="config_image_cart_width" value="<?= $config_image_cart_width; ?>" size="3"/>
					x
					<input type="text" name="config_image_cart_height" value="<?= $config_image_cart_height; ?>" size="3"/>
			</tr>
		</table>
	</div>
	<div id="tab-mail">
		<table class="form">
			<tr>
				<td><?= $entry_mail_protocol; ?></td>
				<td><select name="config_mail_protocol">
						<? if ($config_mail_protocol == 'mail') { ?>
							<option value="mail" selected="selected"><?= $text_mail; ?></option>
						<? } else { ?>
							<option value="mail"><?= $text_mail; ?></option>
						<? } ?>
						<? if ($config_mail_protocol == 'smtp') { ?>
							<option value="smtp" selected="selected"><?= $text_smtp; ?></option>
						<? } else { ?>
							<option value="smtp"><?= $text_smtp; ?></option>
						<? } ?>
					</select></td>
			</tr>
			<tr>
				<td><?= $entry_mail_parameter; ?></td>
				<td><input type="text" name="config_mail_parameter" value="<?= $config_mail_parameter; ?>"/></td>
			</tr>
			<tr>
				<td><?= $entry_smtp_host; ?></td>
				<td><input type="text" name="config_smtp_host" value="<?= $config_smtp_host; ?>"/></td>
			</tr>
			<tr>
				<td><?= $entry_smtp_username; ?></td>
				<td><input type="text" name="config_smtp_username" value="<?= $config_smtp_username; ?>"/></td>
			</tr>
			<tr>
				<td><?= $entry_smtp_password; ?></td>
				<td><input type="text" name="config_smtp_password" value="<?= $config_smtp_password; ?>"/></td>
			</tr>
			<tr>
				<td><?= $entry_smtp_port; ?></td>
				<td><input type="text" name="config_smtp_port" value="<?= $config_smtp_port; ?>"/></td>
			</tr>
			<tr>
				<td><?= $entry_smtp_timeout; ?></td>
				<td><input type="text" name="config_smtp_timeout" value="<?= $config_smtp_timeout; ?>"/></td>
			</tr>
			<tr>
				<td><?= $entry_alert_mail; ?></td>
				<td><? if ($config_alert_mail) { ?>
						<input type="radio" name="config_alert_mail" value="1" checked="checked"/>
						<?= $text_yes; ?>
						<input type="radio" name="config_alert_mail" value="0"/>
						<?= $text_no; ?>
					<? } else { ?>
						<input type="radio" name="config_alert_mail" value="1"/>
						<?= $text_yes; ?>
						<input type="radio" name="config_alert_mail" value="0" checked="checked"/>
						<?= $text_no; ?>
					<? } ?></td>
			</tr>
			<tr>
				<td><?= $entry_account_mail; ?></td>
				<td><? if ($config_account_mail) { ?>
						<input type="radio" name="config_account_mail" value="1" checked="checked"/>
						<?= $text_yes; ?>
						<input type="radio" name="config_account_mail" value="0"/>
						<?= $text_no; ?>
					<? } else { ?>
						<input type="radio" name="config_account_mail" value="1"/>
						<?= $text_yes; ?>
						<input type="radio" name="config_account_mail" value="0" checked="checked"/>
						<?= $text_no; ?>
					<? } ?></td>
			</tr>
			<tr>
				<td><?= $entry_alert_emails; ?></td>
				<td><textarea name="config_alert_emails" cols="40" rows="5"><?= $config_alert_emails; ?></textarea></td>
			</tr>
		</table>
	</div>
	<div id="tab-fraud">
		<table class="form">
			<tr>
				<td><?= $entry_fraud_detection; ?></td>
				<td><? if ($config_fraud_detection) { ?>
						<input type="radio" name="config_fraud_detection" value="1" checked="checked"/>
						<?= $text_yes; ?>
						<input type="radio" name="config_fraud_detection" value="0"/>
						<?= $text_no; ?>
					<? } else { ?>
						<input type="radio" name="config_fraud_detection" value="1"/>
						<?= $text_yes; ?>
						<input type="radio" name="config_fraud_detection" value="0" checked="checked"/>
						<?= $text_no; ?>
					<? } ?></td>
			</tr>
			<tr>
				<td><?= $entry_fraud_key; ?></td>
				<td><input type="text" name="config_fraud_key" value="<?= $config_fraud_key; ?>"/></td>
			</tr>
			<tr>
				<td><?= $entry_fraud_score; ?></td>
				<td><input type="text" name="config_fraud_score" value="<?= $config_fraud_score; ?>"/></td>
			</tr>
		</table>
	</div>
	<div id='tab-file-permissions'>
		<table class="form">
			<tr>
				<td></td>
				<td>
					<table class='mode_explanation'>
						<tbody>
						<tr><?= $text_mode_explanation; ?></tr>
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
				<td><?= $entry_default_modes; ?></td>
				<td>
					<label for='default_file_mode'><?= $entry_default_file_mode; ?></label>
					<input id='default_file_mode' type='text' size='3' maxlength='3' name='config_default_file_mode' value="<?= $config_default_file_mode; ?>"/>
					<label for='default_dir_mode'><?= $entry_default_dir_mode; ?></label>
					<input id='default_dir_mode' type='text' size='3' maxlength='3' name='config_default_dir_mode' value="<?= $config_default_dir_mode; ?>"/>
				</td>
			</tr>
			<tr>
				<td><?= $entry_image_modes; ?></td>
				<td>
					<label for='image_file_mode'><?= $entry_image_file_mode; ?></label>
					<input id='image_file_mode' type='text' size='3' maxlength='3' name='config_image_file_mode' value="<?= $config_image_file_mode; ?>"/>
					<label for='_dir_mode'><?= $entry_image_dir_mode; ?></label>
					<input id='image_dir_mode' type='text' size='3' maxlength='3' name='config_image_dir_mode' value="<?= $config_image_dir_mode; ?>"/>
				</td>
			</tr>
			<tr>
				<td><?= $entry_plugin_modes; ?></td>
				<td>
					<label for='plugin_file_mode'><?= $entry_plugin_file_mode; ?></label>
					<input id='plugin_file_mode' type='text' size='3' maxlength='3' name='config_plugin_file_mode' value="<?= $config_plugin_file_mode; ?>"/>
					<label for='_dir_mode'><?= $entry_plugin_dir_mode; ?></label>
					<input id='plugin_dir_mode' type='text' size='3' maxlength='3' name='config_plugin_dir_mode' value="<?= $config_plugin_dir_mode; ?>"/>
				</td>
			</tr>
		</table>
	</div>
	<div id="tab-server">
		<table class="form">
			<tr>
				<td><?= $entry_debug; ?></td>
				<td><?= $this->builder->build('select', $data_yes_no, 'config_debug', (int)$config_debug); ?></td>
			</tr>
			<tr>
				<td><?= $entry_jquery_cdn; ?></td>
				<td><?= $this->builder->build('select', $data_yes_no, 'config_jquery_cdn', (int)$config_jquery_cdn); ?></td>
			</tr>
			<tr>
				<td><?= $entry_debug_send_emails; ?></td>
				<td><?= $this->builder->build('select', $data_yes_no, 'config_debug_send_emails', (int)$config_debug_send_emails); ?></td>
			</tr>
			<tr>
				<td><?= $entry_use_ssl; ?></td>
				<td><? if ($config_use_ssl) { ?>
						<input type="radio" name="config_use_ssl" value="1" checked="checked"/>
						<?= $text_yes; ?>
						<input type="radio" name="config_use_ssl" value="0"/>
						<?= $text_no; ?>
					<? } else { ?>
						<input type="radio" name="config_use_ssl" value="1"/>
						<?= $text_yes; ?>
						<input type="radio" name="config_use_ssl" value="0" checked="checked"/>
						<?= $text_no; ?>
					<? } ?></td>
			</tr>
			<tr>
				<td><?= $entry_seo_url; ?></td>
				<td><? if ($config_seo_url) { ?>
						<input type="radio" name="config_seo_url" value="1" checked="checked"/>
						<?= $text_yes; ?>
						<input type="radio" name="config_seo_url" value="0"/>
						<?= $text_no; ?>
					<? } else { ?>
						<input type="radio" name="config_seo_url" value="1"/>
						<?= $text_yes; ?>
						<input type="radio" name="config_seo_url" value="0" checked="checked"/>
						<?= $text_no; ?>
					<? } ?></td>
			</tr>
			<tr>
				<td><?= $entry_maintenance; ?></td>
				<td><? if ($config_maintenance) { ?>
						<input type="radio" name="config_maintenance" value="1" checked="checked"/>
						<?= $text_yes; ?>
						<input type="radio" name="config_maintenance" value="0"/>
						<?= $text_no; ?>
					<? } else { ?>
						<input type="radio" name="config_maintenance" value="1"/>
						<?= $text_yes; ?>
						<input type="radio" name="config_maintenance" value="0" checked="checked"/>
						<?= $text_no; ?>
					<? } ?></td>
			</tr>
			<tr>
				<td><?= $entry_image_max_mem; ?></td>
				<td><input type="text" name="config_image_max_mem" value="<?= $config_image_max_mem; ?>"/></td>
			</tr>
			<tr>
				<td><?= $entry_encryption; ?></td>
				<td><input type="text" name="config_encryption" value="<?= $config_encryption; ?>"/></td>
			</tr>
			<tr>
				<td><?= $entry_compression; ?></td>
				<td><input type="text" name="config_compression" value="<?= $config_compression; ?>" size="3"/></td>
			</tr>
			<tr>
				<td class="required"> <?= $entry_log_filename; ?></td>
				<td><input type="text" name="config_log_filename" value="<?= $config_log_filename; ?>"/>
			</tr>
			<tr>
				<td class="required"> <?= $entry_error_filename; ?></td>
				<td><input type="text" name="config_error_filename" value="<?= $config_error_filename; ?>"/>
			</tr>
			<tr>
				<td><?= $entry_error_display; ?></td>
				<td><?= $this->builder->build('radio', $data_yes_no, 'config_error_display', $config_error_display); ?></td>
			</tr>
			<tr>
				<td><?= $entry_error_log; ?></td>
				<td><?= $this->builder->build('radio', $data_yes_no, 'config_error_log', $config_error_log); ?></td>
			</tr>
			<tr>
				<td><?= $entry_google_analytics; ?></td>
				<td><textarea name="config_google_analytics" cols="40" rows="5"><?= $config_google_analytics; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><?= $entry_statcounter; ?></td>
				<td>
					<label for="statcounter_project"><?= $entry_statcounter_project; ?></label><br/>
					<input type="text" name="config_statcounter[project]" value="<?= $config_statcounter['project']; ?>"/><br/><br/>
					<label for="statcounter_project"><?= $entry_statcounter_security; ?></label><br/>
					<input type="text" name="config_statcounter[security]" value="<?= $config_statcounter['security']; ?>"/>
				</td>
			</tr>
		</table>
	</div>
	</form>
	</div>
	</div>
	</div>

	<script type="text/javascript">//<!--
		$('[name=config_theme]').change(function () {
			$('#theme').load('<?= $load_theme_img; ?>' + '&theme=' + $(this).val());
		}).change();
//--></script>

<?= $this->builder->js('load_zones', 'table.form', '.country_select', '.zone_select'); ?>

	<script type="text/javascript">//<!--
		$('#tabs a').tabs();
//--></script>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>