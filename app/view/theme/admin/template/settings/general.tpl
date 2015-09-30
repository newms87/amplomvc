<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<form action="<?= site_url('admin/settings/general/save'); ?>" method="post" enctype="multipart/form-data" class="box ctrl-save">
		<div class="heading">
			<h1>
				<img src="<?= theme_url('image/setting.png'); ?>" alt=""/>
				{{General Settings}}
			</h1>

			<div class="buttons">
				<button>{{Save}}</button>
				<a href="<?= site_url('admin/settings'); ?>" class="button cancel">{{Cancel}}</a>
			</div>
		</div>
		<div class="section">
			<div id="tabs" class="htabs">
				<a href="#tab-general">{{Settings}}</a>
				<a href="#tab-site">{{Site}}</a>
				<a href="#tab-local">{{Local}}</a>
				<a href="#tab-option">{{Option}}</a>
				<a href="#tab-image">{{Image}}</a>
				<a href="#tab-mail">{{Mail}}</a>
				<a href="#tab-server">{{Server}}</a>
			</div>

			<div id="tab-general">
				<table class="form">
					<tr>
						<td class="required"> {{Site Name:}}</td>
						<td>
							<input type="text" name="site_name" value="<?= $site_name; ?>" size="40"/>
						</td>
					</tr>
					<tr>
						<td class="required"> {{Site Owner:}}</td>
						<td>
							<input type="text" name="site_owner" value="<?= $site_owner; ?>" size="40"/>
						</td>
					</tr>
					<tr>
						<td class="required"> {{Address:}}</td>
						<td>
							<textarea name="site_address" cols="40" rows="5"><?= $site_address; ?></textarea>
						</td>
					</tr>
					<tr>
						<td class="required"> {{E-Mail:}}</td>
						<td>
							<input type="text" name="site_email" value="<?= $site_email; ?>" size="40"/>
						</td>
					</tr>
					<tr>
						<td class="required">{{Support Email:}}
							<span class="help">{{Please specify an email to send support requests to.}}</span>
						</td>
						<td>
							<input type="text" name="site_email_support" value="<?= $site_email_support; ?>" size="40"/>
						</td>
					</tr>
					<tr>
						<td class="required">{{Error Email:}}
							<span class="help">{{Please specify an email to notify when a critical system error has occurred.}}</span>
						</td>
						<td>
							<input type="text" name="site_email_error" value="<?= $site_email_error; ?>" size="40"/>
						</td>
					</tr>
					<tr>
						<td class="required"> {{Phone:}}</td>
						<td>
							<input type="text" name="site_phone" value="<?= $site_phone; ?>"/>
						</td>
					</tr>
					<tr>
						<td>{{Fax:}}</td>
						<td>
							<input type="text" name="config_fax" value="<?= $config_fax; ?>"/>
						</td>
					</tr>
				</table>
			</div>
			<div id="tab-site">
				<table class="form">
					<tr>
						<td class="required">
							{{Default Title:}}
							<span class="help">{{The title will show up in search results and the browser tab / window}}</span>
						</td>
						<td>
							<input type="text" name="site_title" value="<?= $site_title; ?>"/>
						</td>
					</tr>
					<tr>
						<td>
							{{Default Meta Description:}}
							<span class="help">{{The Meta Description will show up in search results, and is used for Search Engine Optimization (SEO).}}</span>
						</td>
						<td>
							<textarea name="site_meta_description" cols="40" rows="5"><?= $site_meta_description; ?></textarea>
						</td>
					</tr>
					<tr>
						<td>{{Theme:}}</td>
						<td>
							<?=
							build(array(
								'type'   => 'select',
								'name'   => 'site_theme',
								'data'   => $data_themes,
								'select' => $site_theme,
								'value'  => 'name',
								'label'  => 'name',
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
							build(array(
								'type'   => 'select',
								'name'   => 'config_default_layout_id',
								'data'   => $data_layouts,
								'select' => $config_default_layout_id,
								'value'  => 'layout_id',
								'label'  => 'name',
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
								{{Insertables:}}
								<br/>
								{name}, {company}, {address}, {address_2}, {postcode}, {zone}, {zone_code}, {country}.
								<br/>
								<br/>
								{{Can be individually set under System > Localisation > Countries}}
							</span>
						</td>
						<td>
							<textarea name="site_address_format" cols="40" rows="5"><?= $site_address_format; ?></textarea>
						</td>
					</tr>
					<tr>
						<td>
							{{International Site}}
							<span class="help">{{Include Country in address forms / format}}</span>
						</td>
						<td>
							<?= build(array(
								'type'   => 'radio',
								'name'   => 'site_international',
								'data'   => array(
									1 => "{{Yes}}",
									0 => "{{No}}",
								),
								'select' => $site_international,
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{Country:}}</td>
						<td>
							<?=
							build(array(
								'type'   => 'select',
								'name'   => 'config_country_id',
								'data'   => $data_countries,
								'select' => $config_country_id,
								'value'  => 'country_id',
								'label'  => 'name',
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{Region / State:}}</td>
						<td>
							<select name="config_zone_id" class="zone-select" data-zone_id="<?= $config_zone_id; ?>"></select>
						</td>
					</tr>
					<tr>
						<td>{{Language:}}</td>
						<td>
							<?= build(array(
								'type'   => 'select',
								'name'   => 'config_language',
								'data'   => $data_languages,
								'select' => $config_language,
								'value'  => 'code',
								'label'  => 'name',
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{Use Macro Languages (experimental):}}
							<span class="help">{{Attempt to resolve languages by country specific macro codes}}</span>
						</td>
						<td><?=
							build(array(
								'type'   => 'select',
								'name'   => 'config_use_macro_languages',
								'data'   => $data_yes_no,
								'select' => $config_use_macro_languages
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{Currency:}}
							<span class="help">{{Change the default currency. Clear your browser cache to see the change and reset your existing cookie.}}</span>
						</td>
						<td>
							<?= build(array(
								'type'   => 'select',
								'name'   => 'config_currency',
								'data'   => $data_currencies,
								'select' => $config_currency,
								'value'  => 'code',
								'label'  => 'title',
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{Auto Update Currency:}}
							<span class="help">{{Set your site to automatically update currencies daily.}}</span>
						</td>
						<td>
							<?= build(array(
								'type'   => 'radio',
								'name'   => 'config_currency_auto',
								'data'   => $data_yes_no,
								'select' => $config_currency_auto,
							)); ?>
						</td>
					</tr>
				</table>
			</div>
			<div id="tab-option">
				<table class="form">
					<tr>
						<td>
							{{Automated Tasks}}
							<span class="help">{{Highly recommended to leave this on!}}</span>
						</td>
						<td><?=
							build(array(
								'type'   => 'radio',
								'name'   => 'cron_status',
								'data'   => $data_yes_no,
								'select' => $cron_status
							)); ?></td>
					</tr>
					<tr>
						<td class="required">{{Display Breadcrumbs?}}
							<span class="help">{{Display breadcrumbs in the sitefront? (breadcrumbs will still display in the admin panel)}}</span>
						</td>
						<td><?=
							build(array(
								'type'   => 'select',
								'name'   => "show_breadcrumbs",
								'data'   => $data_yes_no,
								'select' => $show_breadcrumbs
							)); ?></td>
					</tr>
					<tr>
						<td class="required"> {{Breadcrumb Separator:}}</td>
						<td>
							<input type="text" style="font-size:20px" name="breadcrumb_separator" value="<?= $breadcrumb_separator; ?>" size="1"/>
						</td>
					</tr>
					<tr>
						<td class="required">{{Default Items Per Page (Catalog):}}
							<span class="help">{{Determines how many catalog items are shown per page (products, categories, etc)}}</span>
						</td>
						<td>
							<input type="text" name="site_list_limit" value="<?= $site_list_limit; ?>" size="3"/>
						</td>
					</tr>
					<tr>
						<td class="required">{{Default Autocomplete Limit:}}
							<span class="help">{{Determines how many autocomplete items are retrieved at a time}}</span>
						</td>
						<td>
							<input type="text" name="config_autocomplete_limit" value="<?= $config_autocomplete_limit; ?>" size="3"/>
						</td>
					</tr>
					<tr>
						<td>{{Performance Logging:}}</td>
						<td><?=
							build(array(
								'type'   => 'select',
								'name'   => 'config_performance_log',
								'data'   => $data_statuses,
								'select' => $config_performance_log
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{Allow Customers to Close Notification Messages?}}
							<span class="help">{{These are popups that display warning, success and alert/notify messages}}</span>
						</td>
						<td>
							<?= build(array(
								'type'   => 'radio',
								'name'   => 'config_allow_close_message',
								'data'   => $data_yes_no,
								'select' => $config_allow_close_message
							)); ?></td>
					</tr>
					<tr>
						<td>{{Customer Role:}}
							<span class="help">{{Default Customer Role sets permissions on what a customer is allowed to do.}}</span>
						</td>
						<td>
							<?= build(array(
								'type'   => 'select',
								'name'   => 'default_customer_role_id',
								'data'   => $data_user_roles,
								'select' => $default_customer_role_id,
								'value'  => 'user_role_id',
								'label'  => 'name',
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{Approve New Customers:}}
							<span class="help">{{Don't allow new customer to login until their account has been approved.}}</span
						</td>
						<td>
							<?= build(array(
								'type'   => 'radio',
								'name'   => 'config_customer_approval',
								'data'   => $data_yes_no,
								'select' => $config_customer_approval
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{Account Terms:}}
							<span class="help">{{Forces people to agree to terms before an account can be created.}}</span>
						</td>
						<td>
							<?= build(array(
								'type'   => 'select',
								'name'   => 'terms_agreement_page_id',
								'data'   => $data_pages,
								'select' => $terms_agreement_page_id,
								'value'  => 'page_id',
								'label'  => 'title',
							)); ?>
						</td>
					</tr>

					<tr>
						<td>{{Allow Social Sharing:}}</td>
						<td>
							<?= build(array(
								'type'   => 'select',
								'name'   => "config_share_status",
								'data'   => $data_yes_no,
								'select' => $config_share_status
							)); ?>
						</td>
					</tr>

					<tr>
						<td>{{Allowed Upload File Extensions:}}
							<span class="help">{{Add which file extensions are allowed to be uploaded. Use comma separated values.}}</span>
						</td>
						<td>
							<textarea name="config_upload_allowed" cols="40" rows="5"><?= $config_upload_allowed; ?></textarea>
						</td>
					</tr>
					<tr>
						<td>{{Allowed Upload Image Extensions:}}
							<span class="help">{{Add which image file extensions are allowed to be uploaded. Use comma separated values.}}</span>
						</td>
						<td>
							<textarea name="config_upload_images_allowed" cols="40" rows="5"><?= $config_upload_images_allowed; ?></textarea>
						</td>
					</tr>
					<tr>
						<td>{{Allowed Upload Image Mime Types:}}
							<span class="help">{{Add which image Mime Types are allowed to be uploaded. Use comma separated values.}}</span>
						</td>
						<td>
							<textarea name="config_upload_images_mime_types_allowed" cols="40" rows="5"><?= $config_upload_images_mime_types_allowed; ?></textarea>
						</td>
					</tr>

				</table>
			</div>
			<div id="tab-image">
				<table class="form">
					<tr>
						<td>{{Store Logo:}}</td>
						<td>
							<input type="text" class="imageinput" name="site_logo" data-thumb="<?= image($site_logo, $site_logo_width, $site_logo_height); ?>" value="<?= $site_logo; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="required">{{Logo Size}}</td>
						<td>
							<div class="store-logo-size">
								<input type="text" name="site_logo_width" value="<?= $site_logo_width; ?>" size="3"/>
								x
								<input type="text" name="site_logo_height" value="<?= $site_logo_height; ?>" size="3"/>
							</div>
							<br/>

							<div class="store-logo-x">
								<label>{{Image srcset X}}</label>
								<?= build(array(
									'type'   => 'select',
									'name'   => 'site_logo_srcset',
									'data'   => array(
										1 => '1x',
										2 => '2x',
										3 => '3x'
									),
									'select' => $site_logo_srcset,
								)); ?>
								<span class="help">{{If greater than 1x, sets img srcset attribute and scales down from original image. (eg. if 3x, then 1x size set for src will be 1/3 of original image)}}</span>
							</div>
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
										<input type="text" class="imageinput" name="site_icon[orig]" value="<?= $site_icon['orig']['src']; ?>" data-thumb="<?= $site_icon['orig']['thumb']; ?>"/>

										<div class="icon-label">
											<a id="generate-icons" data-loading="{{Generating...}}" class="button">{{Generate Icon Files}}</a>
										</div>
									</div>
								</div>
								<div class="icon-files left">
									<div class="icon-file icon-ico">
										<input type="text" class="imageinput" name="site_icon[ico]" value="<?= $site_icon['ico']['src']; ?>" data-thumb="<?= $site_icon['ico']['thumb']; ?>"/>

										<div class="icon-label">{{ICO File}}</div>
									</div>
									<? foreach ($data_icon_sizes as $size) { ?>
										<div class="icon-file icon-size">
											<? $key = $size . 'x' . $size; ?>
											<input type="text" class="imageinput" name="site_icon[<?= $key; ?>]" value="<?= $site_icon[$key]['src']; ?>" data-thumb="<?= $site_icon[$key]['thumb']; ?>" data-width="<?= $size; ?>" data-height="<?= $size; ?>"/>

											<div class="icon-label"><?= _l("%s X %s Icon", $size, $size); ?></div>
										</div>
									<? } ?>
								</div>
							</div>
						</td>
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
						<td>{{Mail Protocol:}}
							<span class="help">{{Only choose Mail unless your host has disabled the php mail function.}}
						</td>
						<td>
							<?= build(array(
								'type'   => 'select',
								'name'   => "config_mail_protocol",
								'data'   => $data_mail_protocols,
								'select' => $config_mail_protocol
							)); ?></td>
					</tr>
					<tr>
						<td>{{Mail Parameters:}}
							<span class="help">{{When using Mail, additional mail parameters can be added here (e.g. '-femail@siteaddress.com'.}}
						</td>
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
						<td>{{New Order Alert Mail:}}
							<span class="help">{{Send a email to the site owner when a new order is created.}}</span>
						</td>
						<td>
							<?= build(array(
								'type'   => 'radio',
								'name'   => 'config_alert_mail',
								'data'   => $data_yes_no,
								'select' => $config_alert_mail
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{New Account Alert Mail:}}
							<span class="help">{{Send a email to the site owner when a new account is registered.}}</span>
						</td>
						<td>
							<?= build(array(
								'type'   => 'radio',
								'name'   => 'config_account_mail',
								'data'   => $data_yes_no,
								'select' => $config_account_mail
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{Additional Alert E-Mails:}}
							<span class="help">{{Any additional emails you want to receive the alert email, in addition to the main site email. (comma separated)}}</span>
						</td>
						<td>
							<textarea name="config_alert_emails" cols="40" rows="5"><?= $config_alert_emails; ?></textarea>
						</td>
					</tr>
					<tr>
						<td>{{Enable Mail Logging}}</td>
						<td>
							<?= build(array(
								'type'   => 'radio',
								'name'   => 'config_mail_logging',
								'data'   => $data_yes_no,
								'select' => $config_mail_logging
							)); ?>
						</td>
					</tr>
				</table>
			</div>
			<div id="tab-server">
				<table class="form">
					<tr>
						<td>{{Turn on Global Debug:}}
							<span class="help">Should be turned off for production sites.</span>
						</td>
						<td>
							<?= build(array(
								'type'   => 'select',
								'name'   => 'config_debug',
								'data'   => $data_yes_no,
								'select' => $config_debug
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{Log Errors:}}</td>
						<td>
							<?= build(array(
								'type'   => 'radio',
								'name'   => 'error_logging',
								'data'   => $data_yes_no,
								'select' => $error_logging
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{Error Notification Email:}}</td>
						<td>
							<?= build(array(
								'type'   => 'radio',
								'name'   => 'error_notification_email',
								'data'   => $data_yes_no,
								'select' => $error_notification_email,
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{Use the jQuery CDN:}}
							<span class="help">{{This will load jQuery and jQuery UI from the jQuery Content Delivery Network. Recommended for production sites}}</span>
						</td>
						<td>
							<?= build(array(
								'type'   => 'select',
								'name'   => 'config_jquery_cdn',
								'data'   => $data_yes_no,
								'select' => $config_jquery_cdn
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{Send Emails to third parties?}}
							<span class="help">{{Emails sent to people other than the current user and the system emails}}</span>
						</td>
						<td>
							<?= build(array(
								'type'   => 'select',
								'name'   => 'config_debug_send_emails',
								'data'   => $data_yes_no,
								'select' => $config_debug_send_emails
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{Use SSL:}}
							<span class="help">{{To use SSL check with your host if a SSL certificate is installed and added the SSL URL to the catalog and admin config files.}}</span>
						</td>
						<td>
							<?= build(array(
								'type'   => 'radio',
								'name'   => 'config_use_ssl',
								'data'   => $data_yes_no,
								'select' => $config_use_ssl,
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{Maintenance Mode:}}
							<span class="help">{{Prevents customers from browsing your site. They will instead see a maintenance message. If logged in as admin, you will see the site as normal.}}</span>
						</td>
						<td>
							<?= build(array(
								'type'   => 'radio',
								'name'   => 'config_maintenance',
								'data'   => $data_yes_no,
								'select' => $config_maintenance,
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{Image Resize Max Memory}}
							<span class="help">{{The maximum allowed memory when resizing images for the cache. Must be in php memory format (eg: 128M, 512M, 1G, etc.)}}</span>
						</td>
						<td>
							<input type="text" name="config_image_max_mem" value="<?= $config_image_max_mem; ?>"/>
						</td>
					</tr>
					<tr>
						<td>{{Encryption Key:}}
							<span class="help">{{Please provide a secret key that will be used to encrypt private information when processing orders.}}</span>
						</td>
						<td>
							<input type="text" name="config_encryption" value="<?= $config_encryption; ?>"/>
						</td>
					</tr>
					<tr>
						<td>{{Output Compression Level:}}
							<span class="help">{{GZIP for more efficient transfer to requesting clients. Compression level must be between 0 - 9}}</span>
						</td>
						<td>
							<input type="text" name="config_compression" value="<?= $config_compression; ?>" size="3"/>
						</td>
					</tr>
					<tr>
						<td>
							{{Google Analytics}}
						</td>
						<td>
							<div class="ga-code">
								<input placeholder="{{GA Code}}" type="text" name="ga_code" value="<?= $ga_code; ?>"/>

								<div class="help">
									{{Login to your
									<a target="_blank" href="http://www.google.com/analytics/">Google Analytics</a>
									account and after creating your web site profile copy and paste the analytics code into this field.}}
								</div>
							</div>
							<br/>
							<br/>

							<div class="ga-cross-domain">
								<h3>{{Use this section to enable GA Cross-domain analytics}}</h3>
								<span class="help">
									{{Cross-domain analytics is used to track several different
									<b>top-level</b> domains in the same place. (eg: myprimaydomain.com and myblogdomain.com)}}
									<br/>
									<a target="_blank" href="https://support.google.com/analytics/answer/1034342">{{Learn more about Cross-domain Tracking}}</a>
								</span>
								<br/>

								<div class="ga-domains">
									<? foreach ($ga_domains as $row_id => $domain) { ?>
										<div class="domain" data-row="<?= $row_id; ?>">
											<input type="text" name="ga_domains[]" placeholder="example.com" value="<?= $domain; ?>"/>

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
								<?= build(array(
									'type'   => 'radio',
									'name'   => 'ga_click_tracking',
									'data'   => $data_yes_no,
									'select' => $ga_click_tracking,
								)); ?>
							</div>

							<br/>
							<br/>

							<div class="ga-demograhpics">
								<h3>{{Enable GA Demographics?}}</h3>
								<span class="help">{{This will track user age / gender / interests data. To enable go to your google analytics account and enable Demograhpics and Interest Reports, set this to enabled and Amplo MVC will insert the tracking code for you.}}</span>
								<?= build(array(
									'type'   => 'radio',
									'name'   => 'ga_demographics',
									'data'   => $data_yes_no,
									'select' => $ga_demographics,
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
								<input placeholder="{{Experiment ID}}" type="text" name="ga_experiment_id" value="<?= $ga_experiment_id; ?>"/>
								<span class="help">{{(Leave blank if you do not have any experiments set up.)}}</span>
							</div>
							<br/>

							<h3>{{GA Experiment Variations}}</h3>
							<span class="help">{{Enter the number of variations you have setup for this experiment}}</span>

							<div class="ga-experiment-vars">
								<?= build(array(
									'type'   => 'select',
									'name'   => 'ga_exp_vars',
									'data'   => range(0, 25),
									'select' => $ga_exp_vars,
								)); ?>
							</div>
						</td>
					</tr>
					<tr>
						<td>{{Stat Counter Code:}}
							<span class="help">{{Sign up at}}<a target="_blank" href="http://www.statcounter.com/sign-up/">Stat Counter</a>{{and copy and past the code in this field.}}</span>
						</td>
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
	$('.table.form .zone_select').ac_zoneselect({listen: '.table.form [name=config_country_id]'});

	$('[name=site_theme]').change(function () {
		var url = "<?= site_url('admin/settings/general/theme'); ?>" + '?theme=' + $(this).val();
		$('#theme').load(url);
	}).change();

	$('[name=site_logo]').ac_imageinput({width: '<?= $site_logo_width; ?>', height: '<?= $site_logo_height; ?>'});

	$('[name=site_logo_width],[name=site_logo_height]').change(function () {
		var w = $('[name=site_logo_width]').val();
		var h = $('[name=site_logo_height]').val();
		$('[name=site_logo]').siblings('.thumb').css({
			width:  (!w || w == '0') ? 'auto' : w,
			height: (!h || h == '0') ? 'auto' : h
		});
	});

	$('#generate-icons').click(function () {
		var $this = $(this);
		var icon = $('[name="site_icon[orig]"]').val();

		if (!icon) {
			return $('#icon-generator').show_msg('error', "{{You must choose an icon PNG image file first}}");
		}

		$this.loading();
		$.post("<?= site_url('admin/settings/general/generate-icons'); ?>", {icon: icon}, function (json) {
			$this.loading('stop');

			var $gen = $('#icon-generator');

			for (var c in json) {
				input = $gen.find('[name="site_icon[' + c + ']"]').val(json[c].relpath);
				input.closest('.icon-file').find('img.thumb').attr('src', json[c].url);
			}
		}, 'json');
	});

	$('.icon-file .imageinput').ac_imageinput({width: 'auto', height: 'auto'});

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


</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
