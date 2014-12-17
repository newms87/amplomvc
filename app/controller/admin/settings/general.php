<?php

/**
 * Title: General Settings
 * Icon: admin.png
 *
 */
class App_Controller_Admin_Settings_General extends Controller
{
	static $icon_sizes = array(
		152,
		120,
		76,
	);

	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("General Settings"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Settings"), site_url('admin/settings'));
		breadcrumb(_l("General Settings"), site_url('admin/settings/general'));

		//Load Information
		$config_data = $_POST;

		if (!IS_POST) {
			$config_data = $this->config->loadGroup('config');
		}

		$defaults = array(
			'site_name'                               => 'Amplo MVC',
			'site_url'                                => 'http://' . DOMAIN . SITE_BASE,
			'site_ssl'                                => 'https://' . DOMAIN . SITE_BASE,
			'site_owner'                              => '',
			'site_address'                            => '',
			'site_email'                              => 'info@' . DOMAIN,
			'site_email_support'                      => 'support@' . DOMAIN,
			'site_email_error'                        => 'error@' . DOMAIN,
			'site_phone'                              => '',
			'config_fax'                              => '',
			'config_title'                            => 'Amplo MVC | Developer Friendly All Purpose Web Platform',
			'config_default_store'                    => '',
			'config_meta_description'                 => '',
			'config_debug'                            => 0,
			'config_cron_status'                      => 1,
			'config_allow_close_message'              => 1,
			'config_default_layout_id'                => '',
			'config_theme'                            => 'amplo',
			'site_address_format'                     => '',
			'config_country_id'                       => 223,
			'config_zone_id'                          => 8,
			'config_language'                         => 1,
			'config_use_macro_languages'              => 0,
			'config_currency'                         => '',
			'config_currency_auto'                    => '',
			'site_list_limit'                         => 10,
			'config_autocomplete_limit'               => 10,
			'config_performance_log'                  => 0,
			'config_cache_ignore'                     => '',
			'config_customer_group_id'                => '',
			'config_customer_approval'                => 0,
			'config_account_terms_page_id'            => '',
			'config_contact_page_id'                  => '',
			'config_breadcrumb_display'               => 1,
			'config_breadcrumb_separator'             => ' / ',
			'config_review_status'                    => 1,
			'config_share_status'                     => 1,
			'config_upload_allowed'                   => 1,
			'config_upload_images_allowed'            => '',
			'config_upload_images_mime_types_allowed' => '',
			'config_icon'                             => null,
			'config_logo'                             => '',
			'config_logo_srcset'                      => 1,
			'config_logo_width'                       => 0,
			'config_logo_height'                      => 0,
			'admin_icon'                              => null,
			'admin_bar'                               => 1,
			'admin_logo'                              => '',
			'admin_breadcrumb_separator'              => ' / ',
			'admin_language'                          => 1,
			'admin_list_limit'                        => 20,
			'admin_thumb_width'                       => 120,
			'admin_thumb_height'                      => 120,
			'admin_list_image_width'                  => 60,
			'admin_list_image_height'                 => 60,
			'site_email_logo_width'                   => 300,
			'site_email_logo_height'                  => 0,
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
			'config_maintenance'                      => 0,
			'config_image_max_mem'                    => '2G',
			'config_encryption'                       => '',
			'config_compression'                      => '',
			'config_jquery_cdn'                       => 0,
			'config_debug_send_emails'                => '',
			'config_error_display'                    => 0,
			'config_error_log'                        => 1,
			'config_google_analytics'                 => '',
			'config_ga_experiment_id'                 => '',
			'config_ga_exp_vars'                      => 0,
			'config_ga_domains'                       => array(),
			'config_ga_click_tracking'                => 0,
			'config_ga_demographics'                  => 0,
			'config_statcounter'                      => '',
		);

		$data = $config_data + $defaults;

		//Template Data
		$data['data_layouts']         = $this->Model_Design_Layout->getLayouts();
		$data['data_themes']          = $this->theme->getThemes();
		$data['data_countries']       = $this->Model_Localisation_Country->getCountries();
		$data['data_languages']       = $this->Model_Localisation_Language->getLanguages();
		$data['data_currencies']      = $this->Model_Localisation_Currency->getCurrencies();
		$data['data_customer_groups'] = $this->Model_Customer->getCustomerGroups();
		$data['data_pages']           = array('' => _l(" --- None --- ")) + $this->Model_Page->getPages();

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

		foreach (self::$icon_sizes as $size) {
			$key = $size . 'x' . $size;

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

		$data['data_icon_sizes'] = self::$icon_sizes;

		//Domains AC Template
		$data['config_ga_domains']['__ac_template__'] = '';

		//Render
		output($this->render('settings/general', $data));
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
		if ($this->Model_Settings->saveGeneral($_POST)) {
			message('success', _l("The General Settings have been saved!"));
		} else {
			message('error', $this->Model_Settings->getError());
		}

		if ($this->is_ajax) {
			output_json($this->message->fetch());
		} elseif ($this->message->has('error')) {
			post_redirect('admin/settings/general');
		} else {
			redirect('admin/settings');
		}
	}

	public function generate_icons()
	{
		if (!empty($_POST['icon'])) {
			$icon_files = array();

			foreach (self::$icon_sizes as $size) {
				$url = image_save($_POST['icon'], null, $size, $size);

				$icon_files[$size . 'x' . $size] = array(
					'url'     => $url,
					'relpath' => str_replace(URL_IMAGE, '', $url),
				);
			}

			$url = $this->image->ico($_POST['icon']);

			$icon_files['ico'] = array(
				'relpath' => str_replace(URL_IMAGE, '', $url),
				'url'     => $url,
			);

			output(json_encode($icon_files));
		}
	}
}
