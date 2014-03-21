<?= $common_header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= $name; ?></h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
					href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
		</div>
		<div class="section">
			<div id="tabs" class="htabs"><a href="#tab-general"><?= _l("General"); ?></a><a
					href="#tab-store"><?= _l("Store"); ?></a><a href="#tab-local"><?= _l("Local"); ?></a><a
					href="#tab-option"><?= _l("Option"); ?></a><a href="#tab-image"><?= _l("Image"); ?></a><a
					href="#tab-server"><?= _l("Server"); ?></a></div>
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">
					<table class="form">
						<tr>
							<td class="required"> <?= _l("Store Name:"); ?></td>
							<td><input type="text" name="name" value="<?= $name; ?>" size="40"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Store URL:<br /><span class=\"help\">Include the full URL to your store. Make sure to add \'/\' at the end. Example: http://www.yourdomain.com/path/<br /><br />Don\'t use directories to create a new store. You should always point another domain or sub domain to your hosting.</span>"); ?></td>
							<td><input type="text" name="url" value="<?= $url; ?>" size="40"/></td>
						</tr>
						<tr>
							<td><?= _l("SSL URL:<br /><span class=\"help\">SSL URL to your store. Make sure to add \'/\' at the end. Example: http://www.yourdomain.com/path/<br /><br />Don\'t use directories to create a new store. You should always point another domain or sub domain to your hosting.</span>"); ?></td>
							<td><input type="text" name="ssl" value="<?= $ssl; ?>" size="40"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Store Owner:"); ?></td>
							<td><input type="text" name="config_owner" value="<?= $config_owner; ?>" size="40"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Address:"); ?></td>
							<td><textarea name="config_address" cols="40" rows="5"><?= $config_address; ?></textarea></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("E-Mail:"); ?></td>
							<td><input type="text" name="config_email" value="<?= $config_email; ?>" size="40"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Telephone:"); ?></td>
							<td><input type="text" name="config_telephone" value="<?= $config_telephone; ?>"/></td>
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
							<td><input type="text" name="config_title" value="<?= $config_title; ?>"/></td>
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
							<td><?= _l("Default Layout:"); ?></td>
							<td><select name="config_default_layout_id">
									<? foreach ($layouts as $layout) { ?>
										<? if ($layout['layout_id'] == $config_default_layout_id) { ?>
											<option value="<?= $layout['layout_id']; ?>"
												selected="selected"><?= $layout['name']; ?></option>
										<? } else { ?>
											<option value="<?= $layout['layout_id']; ?>"><?= $layout['name']; ?></option>
										<? } ?>
									<? } ?>
								</select></td>
						</tr>
					</table>
				</div>
				<div id="tab-local">
					<table class="form">
						<tr>
							<td><?= _l("Country:"); ?></td>
							<td>
								<?= $this->builder->setConfig('country_id', 'name'); ?>
								<?= $this->builder->build('select', $countries, "config_country_id", $config_country_id, array('class' => "country_select")); ?>
							</td>
						</tr>
						<tr>
							<td><?= _l("Region / State:"); ?></td>
							<td><select name="config_zone_id" class="zone_select" data-zone_id="<?= $config_zone_id; ?>"></select></td>
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
							<td><?= _l("Currency:"); ?></td>
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
					</table>
				</div>
				<div id="tab-option">
					<table class="form">
						<tr>
							<td class="required"> <?= _l("Default Items Per Page (Catalog):<br /><span class=\"help\">Determines how many catalog items are shown per page (products, categories, etc)</span>"); ?></td>
							<td><input type="text" name="config_catalog_limit" value="<?= $config_catalog_limit; ?>" size="3"/></td>
						</tr>
						<tr>
							<td><?= _l("Allowed Shipping Geo Zone:"); ?></td>
							<td>
								<? $this->builder->setConfig('geo_zone_id', 'name'); ?>
								<?= $this->builder->build('select', $geo_zones, "config_allowed_shipping_zone", (int)$config_allowed_shipping_zone); ?>
							</td>
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
							<td><?= _l("Display Model # on product page:"); ?></td>
							<td><?= $this->builder->build('radio', $data_yes_no, 'config_show_product_model', $config_show_product_model); ?></td>
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
							<td><?= _l("Customer Group:<br /><span class=\"help\">Default customer group.</span>"); ?></td>
							<td>
								<? $this->builder->setConfig('customer_group_id', 'name'); ?>
								<?= $this->builder->build('select', $data_customer_groups, 'config_customer_gorup_id', $config_customer_group_id); ?>
							</td>
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
							<td><?= _l("The Contact Page"); ?></td>
							<td>
								<? $this->builder->setConfig('page_id', 'title'); ?>
								<?= $this->builder->build('select', $data_pages, 'config_contact_page_id', $config_contact_page_id); ?>
							</td>
						</tr>
						<tr>
							<td><?= _l("Account Terms:<br /><span class=\"help\">Forces people to agree to terms before an account can be created.</span>"); ?></td>
							<td><select name="config_account_id">
									<option value="0"><?= _l(" --- None --- "); ?></option>
									<? foreach ($informations as $information) { ?>
										<? if ($information['information_id'] == $config_account_id) { ?>
											<option value="<?= $information['information_id']; ?>"
												selected="selected"><?= $information['title']; ?></option>
										<? } else { ?>
											<option value="<?= $information['information_id']; ?>"><?= $information['title']; ?></option>
										<? } ?>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= _l("Checkout Terms:<br /><span class=\"help\">Forces people to agree to terms before an a customer can checkout.</span>"); ?></td>
							<td><select name="config_checkout_id">
									<option value="0"><?= _l(" --- None --- "); ?></option>
									<? foreach ($informations as $information) { ?>
										<? if ($information['information_id'] == $config_checkout_id) { ?>
											<option value="<?= $information['information_id']; ?>"
												selected="selected"><?= $information['title']; ?></option>
										<? } else { ?>
											<option value="<?= $information['information_id']; ?>"><?= $information['title']; ?></option>
										<? } ?>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= _l("Display Stock:<br /><span class=\"help\">Display stock quantity on the product page.</span>"); ?></td>
							<td>
								<?= $this->builder->build('radio', $data_stock_display_types, "config_stock_display", $config_stock_display, array('class' => 'display_stock_radio')); ?>
							</td>
						</tr>
						<tr>
							<td><?= _l("Stock Checkout:<br /><span class=\"help\">Allow customers to still checkout if the products they are ordering are not in stock.</span>"); ?></td>
							<td><? if ($config_stock_checkout) { ?>
									<input type="radio" name="config_stock_checkout" value="1" checked="checked"/>
									<?= _l("Yes"); ?>
									<input type="radio" name="config_stock_checkout" value="0"/>
									<?= _l("No"); ?>
								<? } else { ?>
									<input type="radio" name="config_stock_checkout" value="1"/>
									<?= _l("Yes"); ?>
									<input type="radio" name="config_stock_checkout" value="0" checked="checked"/>
									<?= _l("No"); ?>
								<? } ?></td>
						</tr>
						<tr>
							<td><?= _l("Order Status:<br /><span class=\"help\">Set the default order status when an order is processed.</span>"); ?></td>
							<td>
								<?= $this->builder->setConfig(false, 'title'); ?>
								<?= $this->builder->build('select', $data_order_statuses, 'config_order_complete_status_id', $config_order_complete_status_id); ?>
							</td>
						</tr>
						<tr>
							<td><?= _l("Display Weight on Cart Page:"); ?></td>
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
							<td><?= _l("Store Logo:"); ?></td>
							<td>
								<?= $this->builder->setBuilderTemplate('click_image'); ?>
								<?= $this->builder->imageInput("config_logo", $config_logo); ?>
							</td>
						</tr>
						<tr>
							<td>
								<span><?= _l("Icon:"); ?></span>
								<span class="help"><?= _l("Use a png file that is at least 152px X 152px. Then click generate to generate all required icon file sizes and the .ico file."); ?></span>
							</td>
							<td>
								<div id="icon-generator">
									<div class="generate">
										<div class="icon-file">
											<?= $this->builder->setBuilderTemplate('click_image'); ?>
											<?= $this->builder->imageInput("config_icon[orig]", $config_icon['orig']); ?>
											<div class="icon-label">
												<a id="generate-icons" class="button"><?= _l("Generate Icon Files"); ?></a>
											</div>
										</div>
									</div>
									<div class="icon-files">
										<div class="icon-file icon-ico">
											<?= $this->builder->imageInput("config_icon[ico]", $config_icon['ico'], URL_IMAGE . $config_icon['ico'], 64, 64); ?>
											<div class="icon-label"><?= _l("ICO File"); ?></div>
										</div>
										<? foreach ($data_icon_sizes as $size) { ?>
											<div class="icon-file icon-size">
												<? $key = $size[0] . 'x' . $size[1]; ?>
												<?= $this->builder->imageInput('config_icon[' . $key . ']', $config_icon[$key], URL_IMAGE . $config_icon[$key], $size[0], $size[1]); ?>
												<div class="icon-label"><?= _l("%s X %s Icon", $size[0], $size[1]); ?></div>
											</div>
										<? } ?>
									</div>
								</div>
							</td>
						</tr>
					</table>

					<div class="image_sizes">
						<h1><?= _l("Image Sizes"); ?></h1>
						<span class="help"><?= _l("Leave width or height blank to constrain proportion. Leave both blank to use raw size."); ?></span>
					</div>

					<table class="form">
						<tr>
							<td class="required"><?= _l("Logo Size"); ?></td>
							<td>
								<input type="text" name="config_logo_width" value="<?= $config_logo_width; ?>" size="3"/>
								x
								<input type="text" name="config_logo_height" value="<?= $config_logo_height; ?>" size="3"/>
							</td>
						</tr>
						<tr>
							<td class="required"><?= _l("Logo Size in Emails"); ?></td>
							<td>
								<input type="text" name="config_email_logo_width" value="<?= $config_email_logo_width; ?>" size="3"/>
								x
								<input type="text" name="config_email_logo_height" value="<?= $config_email_logo_height; ?>" size="3"/>
							</td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Category Image Size:"); ?></td>
							<td>
								<input type="text" name="config_image_category_width" value="<?= $config_image_category_width; ?>" size="3"/>
								x
								<input type="text" name="config_image_category_height" value="<?= $config_image_category_height; ?>" size="3"/>
							</td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Product Image Thumb Size:"); ?></td>
							<td><input type="text" name="config_image_thumb_width" value="<?= $config_image_thumb_width; ?>" size="3"/>
								x
								<input type="text" name="config_image_thumb_height" value="<?= $config_image_thumb_height; ?>" size="3"/>
							</td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Product Image Popup Size:"); ?></td>
							<td><input type="text" name="config_image_popup_width" value="<?= $config_image_popup_width; ?>" size="3"/>
								x
								<input type="text" name="config_image_popup_height" value="<?= $config_image_popup_height; ?>" size="3"/>
							</td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Product Image List Size:"); ?></td>
							<td><input type="text" name="config_image_product_width" value="<?= $config_image_product_width; ?>"
									size="3"/>
								x
								<input type="text" name="config_image_product_height" value="<?= $config_image_product_height; ?>"
									size="3"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Additional Product Image Size:"); ?></td>
							<td><input type="text" name="config_image_additional_width" value="<?= $config_image_additional_width; ?>"
									size="3"/>
								x
								<input type="text" name="config_image_additional_height" value="<?= $config_image_additional_height; ?>"
									size="3"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Related Product Image Size:"); ?></td>
							<td><input type="text" name="config_image_related_width" value="<?= $config_image_related_width; ?>"
									size="3"/>
								x
								<input type="text" name="config_image_related_height" value="<?= $config_image_related_height; ?>"
									size="3"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Compare Image Size:"); ?></td>
							<td><input type="text" name="config_image_compare_width" value="<?= $config_image_compare_width; ?>"
									size="3"/>
								x
								<input type="text" name="config_image_compare_height" value="<?= $config_image_compare_height; ?>"
									size="3"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Wish List Image Size:"); ?></td>
							<td><input type="text" name="config_image_wishlist_width" value="<?= $config_image_wishlist_width; ?>"
									size="3"/>
								x
								<input type="text" name="config_image_wishlist_height" value="<?= $config_image_wishlist_height; ?>"
									size="3"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Cart Image Size:"); ?></td>
							<td><input type="text" name="config_image_cart_width" value="<?= $config_image_cart_width; ?>" size="3"/>
								x
								<input type="text" name="config_image_cart_height" value="<?= $config_image_cart_height; ?>" size="3"/>
							</td>
						</tr>
					</table>
				</div>
				<div id="tab-server">
					<table class="form">
						<tr>
							<td><?= _l("Use SSL:<br /><span class=\"help\">To use SSL check with your host if a SSL certificate is installed.</span>"); ?></td>
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
					</table>
				</div>
			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('load_zones', 'table.form', '.country_select', '.zone_select'); ?>

<script type="text/javascript">
	$('[name=config_theme]').change(function () {
		$('#theme').load('<?= $load_theme_img; ?>' + '&theme=' + $(this).val());
	}).change();

	$('#generate-icons').click(function(){
		var icon = $('[name="config_icon[orig]"]').val();

		if (!icon) {
			return $('#icon-generator').ac_msg('error', "<?= _l("You must choose an icon PNG image file first"); ?>");
		}

		$.post("<?= $url_generate_icons; ?>", {icon: icon}, function(json){
			$gen = $('#icon-generator');
			for (var c in json) {
				input = $gen.find('[name="config_icon['+c+']"]').val(json[c].relpath);
				input.closest('.image').find('img.iu_thumb').attr('src', json[c].url);
			}
		}, 'json');
	});

	$('#tabs a').tabs();
</script>
<?= $this->builder->js('errors', $errors); ?>
<?= $common_footer; ?>
