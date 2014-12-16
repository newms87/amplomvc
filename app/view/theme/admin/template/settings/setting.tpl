<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<form action="<?= site_url('admin/settings/setting/save'); ?>" method="post" enctype="multipart/form-data" class="box ctrl-save">
		<div class="heading">
			<h1>
				<img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{General Settings}}
			</h1>

			<div class="buttons">
				<button>{{Save}}</button>
				<a href="<?= site_url('admin/settings/store'); ?>" class="button cancel">{{Cancel}}</a>
			</div>
		</div>
		<div class="section">
			<div id="tabs" class="htabs">
				<a href="#tab-general">{{General}}</a>
				<a href="#tab-store">{{Store}}</a>
				<a href="#tab-local">{{Local}}</a>
				<a href="#tab-option">{{Option}}</a>
				<a href="#tab-image">{{Image}}</a>
				<a href="#tab-mail">{{Mail}}</a>
				<a href="#tab-fraud">{{Fraud}}</a>
				<a href="#tab-file-permissions">{{File Permissions}}</a>
				<a href="#tab-server">{{Server}}</a>
			</div>

			<div id="tab-general">
				<table class="form">
					<tr>
						<td class="required"> {{Store Name:}}</td>
						<td>
							<input type="text" name="config_name" value="<?= $config_name; ?>" size="40"/>
					</tr>
					<tr>
						<td class="required"> {{Store Owner:}}</td>
						<td>
							<input type="text" name="config_owner" value="<?= $config_owner; ?>" size="40"/>
					</tr>
					<tr>
						<td class="required"> {{Address:}}</td>
						<td>
							<textarea name="config_address" cols="40" rows="5"><?= $config_address; ?></textarea>
					</tr>
					<tr>
						<td class="required"> {{E-Mail:}}</td>
						<td>
							<input type="text" name="config_email" value="<?= $config_email; ?>" size="40"/>
					</tr>
					<tr>
						<td class="required"> <?= _l("Support Email:<span class =\"help\">Please specify an email to send support requests to.</span>"); ?></td>
						<td>
							<input type="text" name="config_email_support" value="<?= $config_email_support; ?>" size="40"/>
					</tr>
					<tr>
						<td class="required"> <?= _l("Error Email:<span class=\"help\">Please specify an email to notify when a critical system error has occurred.</span>"); ?></td>
						<td>
							<input type="text" name="config_email_error" value="<?= $config_email_error; ?>" size="40"/>
					</tr>
					<tr>
						<td class="required"> {{Telephone:}}</td>
						<td>
							<input type="text" name="config_telephone" value="<?= $config_telephone; ?>"/>
					</tr>
					<tr>
						<td>{{Fax:}}</td>
						<td>
							<input type="text" name="config_fax" value="<?= $config_fax; ?>"/>
						</td>
					</tr>
				</table>
			</div>
			<div id="tab-store">
				<table class="form">
					<tr>
						<td class="required"> {{Title:}}</td>
						<td>
							<input type="text" name="config_title" value="<?= $config_title; ?>"/>
					</tr>
					<tr>
						<td>{{Meta Tag Description:}}</td>
						<td>
							<textarea name="config_meta_description" cols="40" rows="5"><?= $config_meta_description; ?></textarea>
						</td>
					</tr>
					<tr>
						<td>{{Theme:}}</td>
						<td>
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
						<td>{{Default Layout:}}</td>
						<td>
							<?=
							build('select', array(
								'name'   => 'config_default_layout_id',
								'data'   => $data_layouts,
								'select' => $config_default_layout_id,
								'key'    => 'layout_id',
								'value'  => 'name',
							)); ?>
						</td>
					</tr>
				</table>
			</div>
			<div id="tab-local">
				<table class="form">
					<tr>
						<td>{{Default Address Format:}}
							<span class="help">
								{{Insertables:}}<br/>
								{firstname}, {lastname}, {company}, {address_1}, {address_2}, {postcode}, {zone}, {zone_code}, {country}.<br/><br/>
								{{Can be individually set under System > Localisation > Countries}}
							</span>
						</td>
						<td>
							<textarea name="config_address_format" cols="40" rows="5"><?= $config_address_format; ?></textarea>
						</td>
					</tr>
					<tr>
						<td>{{Country:}}</td>
						<td>
							<?=
							build('select', array(
								'name'   => 'config_country_id',
								'data'   => $countries,
								'select' => $config_country_id,
								'key'    => 'country_id',
								'value'  => 'name',
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{Region / State:}}</td>
						<td>
							<select name="config_zone_id" class="zone_select" data-zone_id="<?= $config_zone_id; ?>"></select>
						</td>
					</tr>
					<tr>
						<td>{{Language:}}</td>
						<td>
							<select name="config_language">
								<? foreach ($languages as $language) { ?>
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
						<td>{{Administration Language:}}</td>
						<td>
							<select name="config_admin_language">
								<? foreach ($languages as $language) { ?>
									<? if ($language['code'] == $config_admin_language) { ?>
										<option value="<?= $language['code']; ?>" selected="selected"><?= $language['name']; ?></option>
									<? } else { ?>
										<option value="<?= $language['code']; ?>"><?= $language['name']; ?></option>
									<? } ?>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><?= _l("Use Macro Languages (experimental):<span class=\"help\">Attempt to resolve languages by country specific macro codes</span>"); ?></td>
						<td><?=
							build('select', array(
								'name'   => 'config_use_macro_languages',
								'data'   => $data_yes_no,
								'select' => $config_use_macro_languages
							)); ?></td>
					</tr>
					<tr>
						<td><?= _l("Currency:<br /><span class=\"help\">Change the default currency. Clear your browser cache to see the change and reset your existing cookie.</span>"); ?></td>
						<td>
							<select name="config_currency">
								<? foreach ($currencies as $currency) { ?>
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
					<tr>
						<td><?= _l("Auto Update Currency:<br /><span class=\"help\">Set your store to automatically update currencies daily.</span>"); ?></td>
						<td><? if ($config_currency_auto) { ?>
								<input type="radio" name="config_currency_auto" value="1" checked="checked"/>
								{{Yes}}
								<input type="radio" name="config_currency_auto" value="0"/>
								{{No}}
							<? } else { ?>
								<input type="radio" name="config_currency_auto" value="1"/>
								{{Yes}}
								<input type="radio" name="config_currency_auto" value="0" checked="checked"/>
								{{No}}
							<? } ?></td>
					</tr>
				</table>
			</div>
			<div id="tab-option">
				<table class="form">
					<tr>
						<td>
							{{Administration Bar}}
							<span class="help">{{This will display a small toolbar on the store fronts when logged into the Admin Panel}}</span>
						</td>
						<td><?=
							build('radio', array(
								'name'   => 'config_admin_bar',
								'data'   => $data_yes_no,
								'select' => $config_admin_bar
							)); ?></td>
					</tr>
					<tr>
						<td>
							{{Automated Tasks}}
							<span class="help">{{Highly recommended to leave this on!}}</span>
						</td>
						<td><?=
							build('radio', array(
								'name'   => 'config_cron_status',
								'data'   => $data_yes_no,
								'select' => $config_cron_status
							)); ?></td>
					</tr>
					<tr>
						<td class="required"> <?= _l("Display Breadcrumbs? <span class=\"help\">Display breadcrumbs in the storefront? (breadcrumbs will still display in the admin panel)</span>"); ?></td>
						<td><?=
							build('select', array(
								'name'   => "config_breadcrumb_display",
								'data'   => $data_yes_no,
								'select' => $config_breadcrumb_display
							)); ?></td>
					</tr>
					<tr>
						<td class="required"> {{Breadcrumb Separator:}}</td>
						<td>
							<input type="text" style="font-size:20px" name="config_breadcrumb_separator" value="<?= $config_breadcrumb_separator; ?>" size="1"/>
					</tr>
					<tr>
						<td class="required"> {{Admin Breadcrumb Separator:}}</td>
						<td>
							<input type="text" style="font-size:20px" name="config_breadcrumb_separator_admin" value="<?= $config_breadcrumb_separator_admin; ?>" size="1"/>
					</tr>
					<tr>
						<td class="required"> <?= _l("Default Items Per Page (Catalog):<br /><span class=\"help\">Determines how many catalog items are shown per page (products, categories, etc)</span>"); ?></td>
						<td>
							<input type="text" name="config_catalog_limit" value="<?= $config_catalog_limit; ?>" size="3"/>
					</tr>
					<tr>
						<td class="required"> <?= _l("Default Items Per Page (Admin):<br /><span class=\"help\">Determines how many admin items are shown per page (orders, customers, etc)</span>"); ?></td>
						<td>
							<input type="text" name="config_admin_limit" value="<?= $config_admin_limit; ?>" size="3"/>
					</tr>
					<tr>
						<td class="required"> <?= _l("Default Autocomplete Limit:<br /><span class=\"help\">Determines how many autocomplete items are retrieved at a time</span>"); ?></td>
						<td>
							<input type="text" name="config_autocomplete_limit" value="<?= $config_autocomplete_limit; ?>" size="3"/>
					</tr>
					<tr>
						<td>{{Performance Logging:}}</td>
						<td><?=
							build('select', array(
								'name'   => 'config_performance_log',
								'data'   => $data_statuses,
								'select' => $config_performance_log
							)); ?></td>
					</tr>
					<tr>
						<td><?= _l("Cache Ignore List:<span class=\"help\">(comma separated list)</span>"); ?></td>
						<td>
							<textarea name="config_cache_ignore"><?= $config_cache_ignore; ?></textarea>
						</td>
					</tr>
					<tr>
						<td>{{Allow Customers to Close Notification Messages?<span class=\'help\'>These are popups that display warning, success and alert/notify messages</span>}}
						</td>
						<td><?=
							build('radio', array(
								'name'   => 'config_allow_close_message',
								'data'   => $data_yes_no,
								'select' => $config_allow_close_message
							)); ?></td>
					</tr>
					<tr>
						<td><?= _l("Customer Group:<br /><span class=\"help\">Default customer group.</span>"); ?></td>
						<td>
							<select name="config_customer_group_id">
								<? foreach ($customer_groups as $customer_group) { ?>
									<? if ($customer_group['customer_group_id'] == $config_customer_group_id) { ?>
										<option value="<?= $customer_group['customer_group_id']; ?>"
											selected="selected"><?= $customer_group['name']; ?></option>
									<? } else { ?>
										<option value="<?= $customer_group['customer_group_id']; ?>"><?= $customer_group['name']; ?></option>
									<? } ?>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><?= _l("Approve New Customers:<br /><span class=\"help\">Don\'t allow new customer to login until their account has been approved.</span>"); ?></td>
						<td><?=
							build('radio', array(
								'name'   => 'config_customer_approval',
								'data'   => $data_yes_no,
								'select' => $config_customer_approval
							)); ?></td>
					</tr>
					<tr>
						<td><?= _l("Account Terms:<br /><span class=\"help\">Forces people to agree to terms before an account can be created.</span>"); ?></td>
						<td>
							<?=
							build('select', array(
								'name'   => 'config_account_terms_page_id',
								'data'   => $data_pages,
								'select' => $config_account_terms_page_id,
								'key'    => 'page_id',
								'value'  => 'title',
							)); ?>
						</td>
					</tr>

					<tr>
						<td>{{Allow Social Sharing:}}</td>
						<td><?=
							build('select', array(
								'name'   => "config_share_status",
								'data'   => $data_yes_no,
								'select' => $config_share_status
							)); ?></td>
					</tr>

					<tr>
						<td><?= _l("Allowed Upload File Extensions:<br /><span class=\"help\">Add which file extensions are allowed to be uploaded. Use comma separated values.</span>"); ?></td>
						<td>
							<textarea name="config_upload_allowed" cols="40" rows="5"><?= $config_upload_allowed; ?></textarea>
						</td>
					</tr>
					<tr>
						<td><?= _l("Allowed Upload Image Extensions:<br /><span class=\"help\">Add which image file extensions are allowed to be uploaded. Use comma separated values.</span>"); ?></td>
						<td>
							<textarea name="config_upload_images_allowed" cols="40"
								rows="5"><?= $config_upload_images_allowed; ?></textarea>
						</td>
					</tr>
					<tr>
						<td><?= _l("Allowed Upload Image Mime Types:<br /><span class=\"help\">Add which image Mime Types are allowed to be uploaded. Use comma separated values.</span>"); ?></td>
						<td>
							<textarea name="config_upload_images_mime_types_allowed" cols="40"
								rows="5"><?= $config_upload_images_mime_types_allowed; ?></textarea>
						</td>
					</tr>

				</table>
			</div>
			<div id="tab-image">
				<table class="form">
					<tr>
						<td>{{Admin Panel Logo:}}</td>
						<td>
							<input type="text" class="imageinput" name="config_admin_logo" value="<?= $config_admin_logo; ?>"/>
						</td>
					</tr>
					<tr>
						<td>
							<span>{{Icon:}}</span>
							<span class="help">{{Use a png file that is at least 152px X 152px. Then click generate to generate all required icon file sizes and the .ico file.}}</span>
						</td>
						<td>
							<div id="icon-generator">
								<div class="generate">
									<div class="icon-file">
										<input type="text" class="imageinput" name="config_icon[orig]" value="<?= $config_icon['orig']['src']; ?>" data-thumb="<?= $config_icon['orig']['thumb']; ?>"/>

										<div class="icon-label">
											<a id="generate-icons" data-loading="{{Generating...}}" class="button">{{Generate Icon Files}}</a>
										</div>
									</div>
								</div>
								<div class="icon-files left">
									<div class="icon-file icon-ico">
										<input type="text" class="imageinput" name="config_icon[ico]" value="<?= $config_icon['ico']['src']; ?>" data-thumb="<?= $config_icon['ico']['thumb']; ?>"/>

										<div class="icon-label">{{ICO File}}</div>
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
					<tr>
						<td class="required"> {{Admin Image Thumb Size:}}</td>
						<td>
							<input type="text" name="config_image_admin_thumb_width" value="<?= $config_image_admin_thumb_width; ?>" size="3"/>
							x
							<input type="text" name="config_image_admin_thumb_height" value="<?= $config_image_admin_thumb_height; ?>" size="3"/>
						</td>
					</tr>
					<tr>
						<td class="required"> {{Admin Image List Size:}}</td>
						<td>
							<input type="text" name="config_image_admin_list_width" value="<?= $config_image_admin_list_width; ?>"
								size="3"/>
							x
							<input type="text" name="config_image_admin_list_height" value="<?= $config_image_admin_list_height; ?>"
								size="3"/>
					</tr>
					<tr>
						<td class="required"> {{Category Image Size:}}</td>
						<td>
							<input type="text" name="config_image_category_width" value="<?= $config_image_category_width; ?>"
								size="3"/>
							x
							<input type="text" name="config_image_category_height" value="<?= $config_image_category_height; ?>"
								size="3"/>
					</tr>
				</table>
			</div>
			<div id="tab-mail">
				<table class="form">
					<tr>
						<td><?= _l("Mail Protocol:<span class=\"help\">Only choose \'Mail\' unless your host has disabled the php mail function."); ?></td>
						<td><?=
							build('select', array(
								'name'   => "config_mail_protocol",
								'data'   => $data_mail_protocols,
								'select' => $config_mail_protocol
							)); ?></td>
					</tr>
					<tr>
						<td><?= _l("Mail Parameters:<span class=\"help\">When using \'Mail\', additional mail parameters can be added here (e.g. \"-femail@storeaddress.com\"."); ?></td>
						<td>
							<input type="text" name="config_mail_parameter" value="<?= $config_mail_parameter; ?>"/>
						</td>
					</tr>
					<tr>
						<td>{{SMTP Host:}}</td>
						<td>
							<input type="text" name="config_smtp_host" value="<?= $config_smtp_host; ?>"/>
						</td>
					</tr>
					<tr>
						<td>{{SMTP Username:}}</td>
						<td>
							<input type="text" name="config_smtp_username" value="<?= $config_smtp_username; ?>"/>
						</td>
					</tr>
					<tr>
						<td>{{SMTP Password:}}</td>
						<td>
							<input type="text" name="config_smtp_password" value="<?= $config_smtp_password; ?>"/>
						</td>
					</tr>
					<tr>
						<td>{{SMTP Port:}}</td>
						<td>
							<input type="text" name="config_smtp_port" value="<?= $config_smtp_port; ?>"/>
						</td>
					</tr>
					<tr>
						<td>{{SMTP Timeout:}}</td>
						<td>
							<input type="text" name="config_smtp_timeout" value="<?= $config_smtp_timeout; ?>"/>
						</td>
					</tr>
					<tr>
						<td><?= _l("New Order Alert Mail:<br /><span class=\"help\">Send a email to the store owner when a new order is created.</span>"); ?></td>
						<td><?=
							build('radio', array(
								'name'   => 'config_alert_mail',
								'data'   => $data_yes_no,
								'select' => $config_alert_mail
							)); ?></td>
					</tr>
					<tr>
						<td><?= _l("New Account Alert Mail:<br /><span class=\"help\">Send a email to the store owner when a new account is registered.</span>"); ?></td>
						<td><?=
							build('radio', array(
								'name'   => 'config_account_mail',
								'data'   => $data_yes_no,
								'select' => $config_account_mail
							)); ?></td>
					</tr>
					<tr>
						<td><?= _l("Additional Alert E-Mails:<br /><span class=\"help\">Any additional emails you want to receive the alert email, in addition to the main store email. (comma separated)</span>"); ?></td>
						<td>
							<textarea name="config_alert_emails" cols="40" rows="5"><?= $config_alert_emails; ?></textarea>
						</td>
					</tr>
					<tr>
						<td>{{Enable Mail Logging}}</td>
						<td><?=
							build('radio', array(
								'name'   => 'config_mail_logging',
								'data'   => $data_yes_no,
								'select' => $config_mail_logging
							)); ?></td>
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
								<tr>{{The file permissions are set user (owner), group, others == ugo == 755 == user has full, group has read & write, others have read & write permissions.}}</tr>
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
						<td>{{Default File Permissions
							<span class=\'help\'>These are the permissions set for system generated files and directories</span>}}
						</td>
						<td>
							<label for="default_file_mode">{{Default File Permissions}}</label>
							<input id="default_file_mode" type="text" size="3" maxlength="3" name="config_default_file_mode" value="<?= $config_default_file_mode; ?>"/>
							<label for="default_dir_mode">{{Default Directory Permissions}}</label>
							<input id="default_dir_mode" type="text" size="3" maxlength="3" name="config_default_dir_mode" value="<?= $config_default_dir_mode; ?>"/>
						</td>
					</tr>
					<tr>
						<td>{{Image File Permissions
							<span class=\'help\'>These are the permissions set for system generated image files and directories</span>}}
						</td>
						<td>
							<label for="image_file_mode">{{Image File Permissions}}</label>
							<input id="image_file_mode" type="text" size="3" maxlength="3" name="config_image_file_mode" value="<?= $config_image_file_mode; ?>"/>
							<label for="_dir_mode">{{Image Directory Permissions}}</label>
							<input id="image_dir_mode" type="text" size="3" maxlength="3" name="config_image_dir_mode" value="<?= $config_image_dir_mode; ?>"/>
						</td>
					</tr>
					<tr>
						<td>{{Plugin File Permissions
							<span class=\'help\'>These are the permissions set for system generated plugin files and directories</span>}}
						</td>
						<td>
							<label for="plugin_file_mode">{{Plugin File Permissions}}</label>
							<input id="plugin_file_mode" type="text" size="3" maxlength="3" name="config_plugin_file_mode" value="<?= $config_plugin_file_mode; ?>"/>
							<label for="_dir_mode">{{Plugin Directory Permissions}}</label>
							<input id="plugin_dir_mode" type="text" size="3" maxlength="3" name="config_plugin_dir_mode" value="<?= $config_plugin_dir_mode; ?>"/>
						</td>
					</tr>
				</table>
			</div>

			<div id="tab-server">
				<table class="form">
					<tr>
						<td><?= _l("Turn on Global Debug:<span class=\"help\">Should be turned off for production sites.</span>"); ?></td>
						<td><?=
							build('select', array(
								'name'   => 'config_debug',
								'data'   => $data_yes_no,
								'select' => $config_debug
							)); ?></td>
					</tr>
					<tr>
						<td><?= _l("Use the jQuery CDN:<span class=\"help\">This will load jQuery and jQuery UI from the jQuery Content Delivery Network. Recommended for production sites</span>"); ?></td>
						<td><?=
							build('select', array(
								'name'   => 'config_jquery_cdn',
								'data'   => $data_yes_no,
								'select' => $config_jquery_cdn
							)); ?></td>
					</tr>
					<tr>
						<td><?= _l("Send Emails to third parties? <span class=\"help\">Emails sent to people other than the current user and the system emails</span>"); ?></td>
						<td><?=
							build('select', array(
								'name'   => 'config_debug_send_emails',
								'data'   => $data_yes_no,
								'select' => $config_debug_send_emails
							)); ?></td>
					</tr>
					<tr>
						<td><?= _l("Use SSL:<br /><span class=\"help\">To use SSL check with your host if a SSL certificate is installed and added the SSL URL to the catalog and admin config files.</span>"); ?></td>
						<td><? if ($config_use_ssl) { ?>
								<input type="radio" name="config_use_ssl" value="1" checked="checked"/>
								{{Yes}}
								<input type="radio" name="config_use_ssl" value="0"/>
								{{No}}
							<? } else { ?>
								<input type="radio" name="config_use_ssl" value="1"/>
								{{Yes}}
								<input type="radio" name="config_use_ssl" value="0" checked="checked"/>
								{{No}}
							<? } ?></td>
					</tr>
					<tr>
						<td><?= _l("Use SEO URL\'s:<br /><span class=\"help\">To use SEO URL\'s apache module mod-rewrite must be installed and you need to rename the htaccess.txt to .htaccess.</span>"); ?></td>
						<td><? if ($config_seo_url) { ?>
								<input type="radio" name="config_seo_url" value="1" checked="checked"/>
								{{Yes}}
								<input type="radio" name="config_seo_url" value="0"/>
								{{No}}
							<? } else { ?>
								<input type="radio" name="config_seo_url" value="1"/>
								{{Yes}}
								<input type="radio" name="config_seo_url" value="0" checked="checked"/>
								{{No}}
							<? } ?></td>
					</tr>
					<tr>
						<td><?= _l("Maintenance Mode:<br /><span class=\"help\">Prevents customers from browsing your store. They will instead see a maintenance message. If logged in as admin, you will see the store as normal.</span>"); ?></td>
						<td><? if ($config_maintenance) { ?>
								<input type="radio" name="config_maintenance" value="1" checked="checked"/>
								{{Yes}}
								<input type="radio" name="config_maintenance" value="0"/>
								{{No}}
							<? } else { ?>
								<input type="radio" name="config_maintenance" value="1"/>
								{{Yes}}
								<input type="radio" name="config_maintenance" value="0" checked="checked"/>
								{{No}}
							<? } ?></td>
					</tr>
					<tr>
						<td><?= _l("Image Resize Max Memory<span class=\"help\">The maximum allowed memory when resizing images for the cache. Must be in php memory format (eg: 128M, 512M, 1G, etc.)</span>"); ?></td>
						<td>
							<input type="text" name="config_image_max_mem" value="<?= $config_image_max_mem; ?>"/>
						</td>
					</tr>
					<tr>
						<td><?= _l("Encryption Key:<br /><span class=\"help\">Please provide a secret key that will be used to encrypt private information when processing orders.</span>"); ?></td>
						<td>
							<input type="text" name="config_encryption" value="<?= $config_encryption; ?>"/>
						</td>
					</tr>
					<tr>
						<td><?= _l("Output Compression Level:<br /><span class=\"help\">GZIP for more efficient transfer to requesting clients. Compression level must be between 0 - 9</span>"); ?></td>
						<td>
							<input type="text" name="config_compression" value="<?= $config_compression; ?>" size="3"/>
						</td>
					</tr>
					<tr>
						<td class="required"> {{Log Filename:}}</td>
						<td>
							<input type="text" name="config_log_filename" value="<?= $config_log_filename; ?>"/>
					</tr>
					<tr>
						<td class="required"> {{Error Log Filename:}}</td>
						<td>
							<input type="text" name="config_error_filename" value="<?= $config_error_filename; ?>"/>
					</tr>
					<tr>
						<td>{{Display Errors:}}</td>
						<td><?=
							build('radio', array(
								'name'   => 'config_error_display',
								'data'   => $data_yes_no,
								'select' => $config_error_display
							)); ?></td>
					</tr>
					<tr>
						<td>{{Log Errors:}}</td>
						<td><?=
							build('radio', array(
								'name'   => 'config_error_log',
								'data'   => $data_yes_no,
								'select' => $config_error_log
							)); ?></td>
					</tr>
					<tr>
						<td>
							{{Google Analytics}}
						</td>
						<td>
							<div class="ga-code">
								<input placeholder="{{GA Code}}" type="text" name="config_google_analytics" value="<?= $config_google_analytics; ?>"/>

								<div class="help"><?= _l("Login to your <a target=\"_blank\" href=\"http://www.google.com/analytics/\">Google Analytics</a> account and after creating your web site profile copy and paste the analytics code into this field."); ?></div>
							</div>
							<br/>
							<br/>

							<div class="ga-cross-domain">
								<h3>{{Use this section to enable GA Cross-domain analytics}}</h3>
								<span class="help">{{Cross-domain analytics is used to track several different
									<b>top-level</b> domains in the same place. (eg: myprimaydomain.com and myblogdomain.com)}}</span>
								<br/>

								<div class="ga-domains">
									<? foreach ($config_ga_domains as $row_id => $domain) { ?>
										<div class="domain" data-row="<?= $row_id; ?>">
											<input type="text" name="config_ga_domains[]" placeholder="example.com" value="<?= $domain; ?>"/>

											<div class="button remove">X</div>
										</div>
									<? } ?>
								</div>
								<div class="button add">{{Add Domain}}</div>
							</div>

							<br/>
							<br/>

							<div class="ga-click-tracking">
								<h3>{{Enable full page click tracking?}}</h3>
								<?= build('radio', array(
									'name'   => 'config_ga_click_tracking',
									'data'   => array(
										1 => _l("Yes"),
										0 => _l("No")
									),
									'select' => $config_ga_click_tracking,
								)); ?>
							</div>

							<br/>
							<br/>

							<div class="ga-demograhpics">
								<h3>{{Enable GA Demographics?}}</h3>
								<span class="help">{{This will track user age / gender / interests data. To enable go to your google analytics account and enable Demograhpics and Interest Reports, set this to enabled and Amplo MVC will insert the tracking code for you.}}</span>
								<?= build('radio', array(
									'name'   => 'config_ga_demographics',
									'data'   => array(
										1 => _l("Yes"),
										0 => _l("No")
									),
									'select' => $config_ga_demographics,
								)); ?>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							{{GA Experiments}}
						</td>
						<td>
							<div class="ga-experiment-id">
								<input placeholder="{{Experiment ID}}" type="text" name="config_ga_experiment_id" value="<?= $config_ga_experiment_id; ?>"/>
								<span class="help">{{(Leave blank if you do not have any experiments set up.)}}</span>
							</div>
							<br/>

							<h3>{{GA Experiment Variations}}</h3>
							<span class="help">{{Enter the number of variations you have setup for this experiment}}</span>

							<div class="ga-experiment-vars">
								<?= build('select', array(
									'name'   => 'config_ga_exp_vars',
									'data'   => range(0, 25),
									'select' => $config_ga_exp_vars,
								)); ?>
							</div>
						</td>
					</tr>
					<tr>
						<td><?= _l("Stat Counter Code:<span class=\"help\">Sign up at <a target=\"_blank\" href=\"http://www.statcounter.com/sign-up/\">Stat Counter</a> and copy and past the code in this field.</span>"); ?></td>
						<td>
							<label for="statcounter_project">{{Project ID}}</label>
							<br/>
							<input type="text" name="config_statcounter[project]" value="<?= $config_statcounter['project']; ?>"/>
							<br/>
							<br/>
							<label for="statcounter_project">{{Security Code}}</label>
							<br/>
							<input type="text" name="config_statcounter[security]" value="<?= $config_statcounter['security']; ?>"/>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</form>
</div>
</div>

<script type="text/javascript">
	$('.table.form .zone_select').ac_zoneselect({listen: '.table.form .country_select'});

	$('[name=config_theme]').change(function () {
		var url = $ac.admin_url + 'setting/setting/theme?theme=' + $(this).val();
		$('#theme').load(url);
	}).change();

	$('#generate-icons').click(function () {
		var $this = $(this);
		var icon = $('[name="config_icon[orig]"]').val();

		if (!icon) {
			return $('#icon-generator').ac_msg('error', "{{You must choose an icon PNG image file first}}");
		}

		$this.loading();
		$.post("<?= site_url('admin/settings/store/generate-icons'); ?>", {icon: icon}, function (json) {
			$this.loading('stop');

			var $gen = $('#icon-generator');

			for (var c in json) {
				input = $gen.find('[name="config_icon[' + c + ']"]').val(json[c].relpath);
				input.closest('.icon-file').find('img.thumb').attr('src', json[c].url);
			}
		}, 'json');
	});

	$('.icon-file .imageinput').ac_imageinput({width: 'auto', height: 'auto'});
	$('[name=config_admin_logo].imageinput').ac_imageinput({width: 'auto', height: 'auto'});
	$('.imageinput').ac_imageinput();


	var $ga_domains = $('.ga-domains');

	$('.ga-cross-domain .add').click(function () {
		var $domain_list = $('.ga-domains').ac_template('domain-list', 'add');
	});

	$ga_domains.find('.remove').click(function () {
		$(this).closest('.domain').remove();
	});

	$ga_domains.ac_template('domain-list');

	$('#tabs a').tabs();

	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>