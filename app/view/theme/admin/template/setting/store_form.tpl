<?= call('admin/common/header'); ?>

<div class="section">
	<?= breadcrumbs(); ?>
	<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" class="box">
		<div class="heading">
			<h1>
				<img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= $name; ?>
			</h1>

			<div class="buttons">
				<button><?= _l("Save"); ?></button>
				<a href="<?= site_url('admin/setting/store'); ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>
		<div class="section">
			<div id="tabs" class="htabs">
				<a href="#tab-general"><?= _l("General"); ?></a>
				<a href="#tab-store"><?= _l("Store"); ?></a>
				<a href="#tab-local"><?= _l("Local"); ?></a>
				<a href="#tab-option"><?= _l("Option"); ?></a>
				<a href="#tab-image"><?= _l("Image"); ?></a>
				<a href="#tab-server"><?= _l("Server"); ?></a>
			</div>

			<div id="tab-general">
				<table class="form">
					<tr>
						<td class="required"> <?= _l("Store Name:"); ?></td>
						<td>
							<input type="text" name="name" value="<?= $name; ?>" size="40"/>
						</td>
					</tr>
					<tr>
						<td class="required"> <?= _l("Store URL:<br /><span class=\"help\">Include the full URL to your store. Make sure to add \'/\' at the end. Example: http://www.yourdomain.com/path/<br /><br />Don\'t use directories to create a new store. You should always point another domain or sub domain to your hosting.</span>"); ?></td>
						<td>
							<input type="text" name="url" value="<?= $url; ?>" size="40"/>
						</td>
					</tr>
					<tr>
						<td><?= _l("SSL URL:<br /><span class=\"help\">SSL URL to your store. Make sure to add \'/\' at the end. Example: http://www.yourdomain.com/path/<br /><br />Don\'t use directories to create a new store. You should always point another domain or sub domain to your hosting.</span>"); ?></td>
						<td>
							<input type="text" name="ssl" value="<?= $ssl; ?>" size="40"/>
						</td>
					</tr>
					<tr>
						<td class="required"> <?= _l("Store Owner:"); ?></td>
						<td>
							<input type="text" name="config_owner" value="<?= $config_owner; ?>" size="40"/>
						</td>
					</tr>
					<tr>
						<td class="required"> <?= _l("Address:"); ?></td>
						<td>
							<textarea name="config_address" cols="40" rows="5"><?= $config_address; ?></textarea>
						</td>
					</tr>
					<tr>
						<td class="required"> <?= _l("E-Mail:"); ?></td>
						<td>
							<input type="text" name="config_email" value="<?= $config_email; ?>" size="40"/>
						</td>
					</tr>
					<tr>
						<td class="required"> <?= _l("Telephone:"); ?></td>
						<td>
							<input type="text" name="config_telephone" value="<?= $config_telephone; ?>"/>
						</td>
					</tr>
					<tr>
						<td><?= _l("Fax:"); ?></td>
						<td>
							<input type="text" name="config_fax" value="<?= $config_fax; ?>"/>
						</td>
					</tr>
				</table>
			</div>

			<div id="tab-store">
				<table class="form">
					<tr>
						<td class="required"> <?= _l("Title:"); ?></td>
						<td>
							<input type="text" name="config_title" value="<?= $config_title; ?>"/>
						</td>
					</tr>
					<tr>
						<td><?= _l("Meta Tag Description:"); ?></td>
						<td>
							<textarea name="config_meta_description" cols="40" rows="5"><?= $config_meta_description; ?></textarea>
						</td>
					</tr>
					<tr>
						<td><?= _l("Theme:"); ?></td>
						<td>
							<?=
							build('select', array(
								'name'   => 'config_theme',
								'data'   => $data_themes,
								'select' => $config_theme,
								'key'    => 'name',
								'value'  => 'name',
							)); ?>

							<?=
							build('select', array(
								'name'   => 'config_theme',
								'data'   => $data_themes,
								'select' => $config_theme,
								'key'    => 'name',
								'value'  => 'name',
							)); ?>
						</td>
					</tr>
					<tr>
						<td></td>
						<td id="theme"></td>
					</tr>
					<tr>
						<td><?= _l("Default Layout:"); ?></td>
						<td>
							<select name="config_default_layout_id">
								<? foreach ($data_layouts as $layout) { ?>
									<? if ($layout['layout_id'] == $config_default_layout_id) { ?>
										<option value="<?= $layout['layout_id']; ?>"
											selected="selected"><?= $layout['name']; ?></option>
									<? } else { ?>
										<option value="<?= $layout['layout_id']; ?>"><?= $layout['name']; ?></option>
									<? } ?>
								<? } ?>
							</select>
						</td>
					</tr>
				</table>
			</div>

			<div id="tab-local">
				<table class="form">
					<tr>
						<td><?= _l("Country:"); ?></td>
						<td>
							<?=
							build('select', array(
								'name'   => 'config_country_id',
								'data'   => $data_countries,
								'select' => $config_country_id,
								'key'    => 'country_id',
								'value'  => 'name',
							)); ?>
						</td>
					</tr>
					<tr>
						<td><?= _l("Region / State:"); ?></td>
						<td>
							<select name="config_zone_id" class="zone_select" data-zone_id="<?= $config_zone_id; ?>"></select>
						</td>
					</tr>
					<tr>
						<td><?= _l("Language:"); ?></td>
						<td>
							<select name="config_language">
								<? foreach ($data_languages as $language) { ?>
									<? if ($language['code'] == $config_language) { ?>
										<option value="<?= $language['code']; ?>" selected="selected"><?= $language['name']; ?></option>
									<? } else { ?>
										<option value="<?= $language['code']; ?>"><?= $language['name']; ?></option>
									<? } ?>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><?= _l("Currency:"); ?></td>
						<td>
							<select name="config_currency">
								<? foreach ($data_currencies as $currency) { ?>
									<? if ($currency['code'] == $config_currency) { ?>
										<option value="<?= $currency['code']; ?>"
											selected="selected"><?= $currency['title']; ?></option>
									<? } else { ?>
										<option value="<?= $currency['code']; ?>"><?= $currency['title']; ?></option>
									<? } ?>
								<? } ?>
							</select>
						</td>
					</tr>
				</table>
			</div>

			<div id="tab-option">
				<table class="form">
					<tr>
						<td class="required"> <?= _l("Default Items Per Page (Catalog):<br /><span class=\"help\">Determines how many catalog items are shown per page (products, categories, etc)</span>"); ?></td>
						<td>
							<input type="text" name="config_catalog_limit" value="<?= $config_catalog_limit; ?>" size="3"/>
						</td>
					</tr>
					<tr>
						<td><?= _l("Customer Group:<br /><span class=\"help\">Default customer group.</span>"); ?></td>
						<td>
							<?=
							build('select', array(
								'name'   => 'config_customer_gorup_id',
								'data'   => $data_customer_groups,
								'select' => $config_customer_group_id,
								'key'    => 'customer_group_id',
								'value'  => 'name',
							)); ?>
						</td>
					</tr>
					<tr>
						<td><?= _l("Approve New Customers:<br /><span class=\"help\">Don\'t allow new customer to login until their account has been approved.</span>"); ?></td>
						<td><?= build('radio', array(
	'name'   => 'config_customer_approval',
	'data'   => $data_yes_no,
	'select' => $config_customer_approval
)); ?></td>
					</tr>
					<tr>
						<td><?= _l("The Contact Page"); ?></td>
						<td>
							<? $build = array(
								'name'   => 'config_contact_page_id',
								'data'   => $data_pages,
								'select' => $config_contact_page_id,
								'key'    => 'page_id',
								'value'  => 'title',
							); ?>

							<?= build('select', $build); ?>
						</td>
					</tr>
					<tr>
						<td><?= _l("Account Terms:<br /><span class=\"help\">Forces people to agree to terms before an account can be created.</span>"); ?></td>
						<td>
							<? $build = array(
								'name'   => 'config_account_terms_page_id',
								'data'   => $data_pages,
								'select' => $config_account_terms_page_id,
								'key'    => 'page_id',
								'value'  => 'title',
							); ?>

							<?= build('select', $build); ?>
						</td>
					</tr>
				</table>
			</div>

			<div id="tab-image">
				<table class="form">
					<tr>
						<td><?= _l("Store Logo:"); ?></td>
						<td>
							<input type="text" class="imageinput" name="config_logo" data-thumb="<?= image($config_logo); ?>" value="<?= $config_logo; ?>"/>
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
										<input type="text" class="imageinput" name="config_icon[orig]" value="<?= $config_icon['orig']['src']; ?>" data-thumb="<?= $config_icon['orig']['thumb']; ?>"/>

										<div class="icon-label">
											<a id="generate-icons" data-loading="<?= _l("Generating..."); ?>" class="button"><?= _l("Generate Icon Files"); ?></a>
										</div>
									</div>
								</div>
								<div class="icon-files left">
									<div class="icon-file icon-ico">
										<input type="text" class="imageinput" name="config_icon[ico]" value="<?= $config_icon['ico']['src']; ?>" data-thumb="<?= $config_icon['ico']['thumb']; ?>"/>

										<div class="icon-label"><?= _l("ICO File"); ?></div>
									</div>
									<? foreach ($data_icon_sizes as $size) { ?>
										<div class="icon-file icon-size">
											<? $key = $size[0] . 'x' . $size[1]; ?>
											<input type="text" class="imageinput" name="config_icon[<?= $key; ?>]" value="<?= $config_icon[$key]['src']; ?>" data-thumb="<?= $config_icon[$key]['thumb']; ?>" data-width="<?= $size[0]; ?>" data-height="<?= $size[1]; ?>"/>

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
						<td>
							<input type="text" name="config_image_thumb_width" value="<?= $config_image_thumb_width; ?>" size="3"/>
							x
							<input type="text" name="config_image_thumb_height" value="<?= $config_image_thumb_height; ?>" size="3"/>
						</td>
					</tr>
					<tr>
						<td class="required"> <?= _l("Product Image Popup Size:"); ?></td>
						<td>
							<input type="text" name="config_image_popup_width" value="<?= $config_image_popup_width; ?>" size="3"/>
							x
							<input type="text" name="config_image_popup_height" value="<?= $config_image_popup_height; ?>" size="3"/>
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
		</div>
	</form>
</div>

<script type="text/javascript">
	$('.table.form .zone_select').ac_zoneselect({listen: '.table.form .country_select'});

	$('[name=config_theme]').change(function () {
		$('#theme').load($ac.admin_url + 'setting/setting/theme?theme=' + $(this).val());
	}).change();

	$('#generate-icons').click(function () {
		var $this = $(this);
		var icon = $('[name="config_icon[orig]"]').val();

		if (!icon) {
			return $('#icon-generator').ac_msg('error', "<?= _l("You must choose an icon PNG image file first"); ?>");
		}

		$this.loading();
		$.post("<?= $url_generate_icons; ?>", {icon: icon}, function (json) {
			$this.loading('stop');

			var $gen = $('#icon-generator');

			for (var c in json) {
				input = $gen.find('[name="config_icon[' + c + ']"]').val(json[c].relpath);
				input.closest('.icon-file').find('img.thumb').attr('src', json[c].url);
			}
		}, 'json');
	});

	$('[name=config_logo]').ac_imageinput({width: 'auto'});
	$('.icon-file .imageinput').ac_imageinput({width: 'auto', height: 'auto'});

	$('.imageinput').ac_imageinput();

	$('#tabs a').tabs();

	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= call('admin/common/footer'); ?>
