<?php

/**
 * Title: General Settings
 * Icon: admin.png
 *
 */
class App_Controller_Admin_Settings_General extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("General Settings"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Settings"), site_url('admin/settings/store'));
		breadcrumb(_l("General Settings"), site_url('admin/settings/setting'));

		//Load Information
		$config_data = $_POST;

		if (!IS_POST) {
			$config_data = $this->config->loadGroup('config');
		}

		$defaults = array(

			'config_owner'                 => '',
			'config_address'               => '',
			'config_email'                 => '',
			'config_telephone'             => '',
			'config_fax'                   => '',
			'config_title'                 => '',
			'config_meta_description'      => '',
			'config_default_layout_id'     => '',
			'config_theme'                 => '',
			'config_country_id'            => option('config_country_id'),
			'config_zone_id'               => option('config_zone_id'),
			'config_language'              => option('config_language'),
			'config_currency'              => option('config_currency'),
			'config_catalog_limit'         => '12',
			'config_customer_group_id'     => '',
			'config_customer_approval'     => '',
			'config_account_terms_page_id' => '',
			'config_logo'                  => '',
			'config_logo_srcset'           => 1,
			'config_icon'                  => null,
			'config_logo_width'            => 0,
			'config_logo_height'           => 0,
			'config_email_logo_width'      => 300,
			'config_email_logo_height'     => 0,
			'config_image_thumb_width'     => 228,
			'config_image_thumb_height'    => 228,
			'config_image_popup_width'     => 500,
			'config_image_popup_height'    => 500,
			'config_use_ssl'               => '',
			'config_contact_page_id'       => '',
		);

		$defaults = array(
			'site_name'                         => 'Amplo MVC',
			'url'                          => 'http://' . DOMAIN . SITE_BASE,
			'ssl'                          => 'https://' . DOMAIN . SITE_BASE,
			'config_name'                             => 'AmploCart',
			'config_owner'                            => 'Daniel Newman',
			'config_address'                          => '',
			'config_email'                            => 'info@' . DOMAIN,
			'config_email_support'                    => 'support@' . DOMAIN,
			'config_email_error'                      => 'error@' . DOMAIN,
			'config_telephone'                        => '',
			'config_fax'                              => '',
			'config_title'                            => 'AmploCart',
			'config_default_store'                    => '',
			'config_meta_description'                 => '',
			'config_debug'                            => 0,
			'config_cron_status'                      => 1,
			'config_allow_close_message'              => 1,
			'config_default_layout_id'                => '',
			'config_theme'                            => 'default',
			'config_address_format'                   => '',
			'config_country_id'                       => 223,
			'config_zone_id'                          => 8,
			'config_language'                         => 1,
			'config_admin_language'                   => 1,
			'config_use_macro_languages'              => 0,
			'config_currency'                         => '',
			'config_currency_auto'                    => '',
			'config_catalog_limit'                    => 10,
			'config_admin_limit'                      => 20,
			'config_autocomplete_limit'               => 10,
			'config_performance_log'                  => 0,
			'config_cache_ignore'                     => '',
			'config_customer_group_id'                => '',
			'config_customer_approval'                => 0,
			'config_account_terms_page_id'            => 0,
			'config_breadcrumb_display'               => 1,
			'config_breadcrumb_separator'             => ' / ',
			'config_breadcrumb_separator_admin'       => ' / ',
			'config_review_status'                    => 1,
			'config_share_status'                     => 1,
			'config_upload_allowed'                   => 1,
			'config_upload_images_allowed'            => '',
			'config_upload_images_mime_types_allowed' => '',
			'config_admin_bar'                        => 1,
			'config_admin_logo'                       => '',
			'config_icon'                             => '',
			'config_image_admin_thumb_width'          => 120,
			'config_image_admin_thumb_height'         => 120,
			'config_image_admin_list_width'           => 60,
			'config_image_admin_list_height'          => 60,
			'config_image_thumb_width'                => 120,
			'config_image_thumb_height'               => 120,
			'config_image_popup_width'                => 1024,
			'config_image_popup_height'               => 1024,
			'config_mail_protocol'                    => 'smtp',
			'config_mail_parameter'                   => '',
			'config_smtp_host'                        => '',
			'config_smtp_username'                    => '',
			'config_smtp_password'                    => '',
			'config_smtp_port'                        => 25,
			'config_smtp_timeout'                     => 5,
			'config_alert_mail'                       => '',
			'config_account_mail'                     => '',
			'config_alert_emails'                     => '',
			'config_mail_logging'                     => 1,
			'config_use_ssl'                          => 0,
			'config_seo_url'                          => 1,
			'config_maintenance'                      => 0,
			'config_image_max_mem'                    => '2G',
			'config_encryption'                       => '',
			'config_compression'                      => '',
			'config_jquery_cdn'                       => 0,
			'config_log_filename'                     => 'default.txt',
			'config_debug_send_emails'                => '',
			'config_error_display'                    => 0,
			'config_error_log'                        => 1,
			'config_error_filename'                   => 'error.txt',
			'config_google_analytics'                 => '',
			'config_ga_experiment_id'                 => '',
			'config_ga_exp_vars'                      => 0,
			'config_ga_domains'                       => array(),
			'config_ga_click_tracking'                => 0,
			'config_ga_demographics'                  => 0,
			'config_statcounter'                      => '',
			'config_default_file_mode'                => 644,
			'config_default_dir_mode'                 => 755,
			'config_image_file_mode'                  => 644,
			'config_image_dir_mode'                   => 755,
			'config_plugin_file_mode'                 => 644,
			'config_plugin_dir_mode'                  => 755,
		);

		$data = $config_data + $defaults;

		$octals = array(
			'config_default_file_mode',
			'config_default_dir_mode',
			'config_image_file_mode',
			'config_image_dir_mode',
			'config_plugin_file_mode',
			'config_plugin_dir_mode',
		);

		//convert octals in strings back to regular integers
		foreach ($octals as $oct) {
			$data[$oct] = intval($data[$oct]);
		}

		//Template Data
		$data['data_layouts']    = $this->Model_Design_Layout->getLayouts();
		$data['data_themes']     = $this->theme->getThemes();
		$data['stores']          = $this->Model_Setting_Store->getStores();
		$data['countries']       = $this->Model_Localisation_Country->getCountries();
		$data['languages']       = $this->Model_Localisation_Language->getLanguages();
		$data['currencies']      = $this->Model_Localisation_Currency->getCurrencies();
		$data['customer_groups'] = $this->Model_Customer->getCustomerGroups();
		$data['data_pages']      = array('' => _l(" --- None --- ")) + $this->Model_Page->getPages();

		$data['data_mail_protocols'] = array(
			'smtp' => "SMTP",
			'mail' => _l("PHP Mail"),
		);

		$data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		$data['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		//Website Icon Sizes
		if (!is_array($data['config_icon'])) {
			$data['config_icon'] = array(
				'orig' => '',
				'ico'  => '',
			);
		}

		$data['data_icon_sizes'] = array(
			'152' => array(
				152,
				152
			),
			'120' => array(
				120,
				120
			),
			'76'  => array(
				76,
				76
			),
		);

		foreach ($data['data_icon_sizes'] as $size) {
			$key = $size[0] . 'x' . $size[1];

			if (!isset($data['config_icon'][$key])) {
				$data['config_icon'][$key] = '';
			}
		}

		foreach ($data['config_icon'] as &$icon) {
			$icon = array(
				'thumb' => $this->image->get($icon),
				'src'   => $icon,
			);
		}
		unset($icon);

		//Domains AC Template
		$data['config_ga_domains']['__ac_template__'] = '';

		//Action Buttons
		$data['save']   = site_url('admin/settings/setting');
		$data['cancel'] = site_url('admin/settings/store');

		//Render
		output($this->render('settings/setting', $data));
	}

	public function theme()
	{
		if (empty($_GET['theme'])) {
			output('No Theme Requested.');
			return false;
		}

		$image = DIR_SITE . 'app/view/theme/' . $_GET['theme'] . '/' . $_GET['theme'] . '.png';

		$image = image($image);

		if (!$image) {
			$image = image('no_image', 300, 300);
		}

		output("<img src=\"$image\" class =\"theme_preview\" />");
	}

	public function save()
	{
		if (!$_POST['config_name']) {
			$this->error['config_name'] = _l("Store Name must be between 3 and 32 characters!");
		}

		if (!validate('text', _post('config_owner'), 2, 127)) {
			$this->error['config_owner'] = _l("Store Owner must be between 2 and 127 characters!");
		}

		if (!validate('email', _post('config_email'))) {
			$this->error['config_email'] = _l("E-Mail Address does not appear to be valid!");
		} else {
			if (!validate('email', _post('config_email_support'))) {
				$_POST['config_email_support'] = _post('config_email');
			}

			if (!validate('email', $_POST['config_email_error'])) {
				$_POST['config_email_error'] = $_POST['config_email'];
			}
		}

		if (!$_POST['config_title']) {
			$this->error['config_title'] = _l("Title must be between 3 and 32 characters!");
		}

		if (!$_POST['config_log_filename']) {
			$_POST['config_log_filename'] = 'default.txt';
		}

		if (!$_POST['config_error_filename']) {
			$_POST['config_error_filename'] = 'error.txt';
		}

		if (!$_POST['config_admin_limit']) {
			$_POST['config_admin_limit'] = 20;
		}

		if (!$_POST['config_catalog_limit']) {
			$_POST['config_catalog_limit'] = 20;
		}

		if (!empty($this->error)) {
			message('error', $this->error);
		} else {
			if ($this->config->saveGroup('config', $_POST)) {
				message('success', _l("Success: You have modified settings!"));
			} else {
				message('error', $this->config->getError());
			}
		}

		if (IS_AJAX) {
			output_json($this->message->fetch());
		} elseif ($this->message->has('error')) {
			post_redirect('admin/settings/cart');
		} else {
			redirect('admin/settings/store');
		}
	}
}
