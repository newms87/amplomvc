<?php

class App_Model_Settings extends Model
{
	static $admin_settings = array(
		'admin_title'                => 'Amplo MVC | Developer Friendly All Purpose Web Platform',
		'admin_icon'                 => null,
		'admin_bar'                  => 1,
		'admin_logo'                 => '',
		'admin_logo_srcset'          => '',
		'admin_show_breadcrumbs'     => 1,
		'admin_breadcrumb_separator' => ' / ',
		'admin_language'             => 1,
		'admin_list_limit'           => 20,
		'admin_thumb_width'          => 120,
		'admin_thumb_height'         => 120,
		'admin_logo_width'           => 0,
		'admin_logo_height'          => 0,
		'admin_list_image_width'     => 60,
		'admin_list_image_height'    => 60,
	);

	static $general_settings = array(
		'site_name'                               => 'Amplo MVC',
		'site_owner'                              => '',
		'site_address'                            => '',
		'site_email'                              => '',
		'site_email_support'                      => '',
		'site_email_error'                        => '',
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

	public function saveGeneral($settings)
	{
		if (empty($settings['site_name']) || !validate('text', $settings['site_name'], 2, 128)) {
			$this->error['site_name'] = _l("Site Name must be between 2 and 128 characters!");
		}

		if (empty($settings['site_email']) || !validate('email', $settings['site_email'])) {
			$this->error['site_email'] = _l("The Site Email does not appear to be valid!");
		}

		if (isset($settings['site_email_support']) && !validate('email', $settings['site_email_support'])) {
			$this->error['site_email_support'] = _l("The Support Email %s does not appear to be valid.", $settings['site_email_support']);
		}

		if (isset($settings['site_email_error']) && !validate('email', $settings['site_email_error'])) {
			$this->error['site_email_error'] = _l("The Error Email %s does not appear to be valid.", $settings['site_email_error']);
		}

		if ($this->error) {
			return false;
		}

		if (empty($settings['site_title'])) {
			$settings['site_title'] = $settings['site_name'];
		}

		$settings['site_list_limit']  = max(0, (int)$settings['site_list_limit']);

		$result = $this->config->saveGroup('general', $settings);

		if (!$result) {
			$this->error = $this->config->getError();
		} else {
			$this->theme->install($settings['config_theme']);
		}

		return $result;
	}

	public function saveAdmin($settings)
	{
		if (empty($settings['site_title'])) {
			$settings['site_title'] = 'Amplo MVC Admin';
		}

		$settings['admin_list_limit'] = max(0, (int)$settings['admin_list_limit']);

		$result = $this->config->saveGroup('admin', $settings);

		if (!$result) {
			$this->error = $this->config->getError();
		}

		return $result;
	}

	public function getWidgets()
	{
		$widgets = array();

		$files = glob(DIR_SITE . 'app/controller/admin/settings/*');

		if ($files) {
			$order = 0;

			foreach ($files as $file) {
				$directives = get_comment_directives($file);

				if (empty($directives['title'])) {
					continue;
				}

				$widget['title'] = _l($directives['title']);

				if (!empty($directives['icon'])) {
					$widget['icon'] = $this->theme->getUrl('image/settings/' . $directives['icon']);
				}

				if (empty($widget['icon'])) {
					$widget['icon'] = $this->theme->getUrl('image/settings/admin.png');
				}

				if (!empty($directives['path'])) {
					$query         = !empty($directives['query']) ? $directives['query'] : '';
					$widget['url'] = site_url($directives['path'], $query);
				} else {
					$widget['url'] = site_url('admin/settings/' . str_replace('.php', '', basename($file)));
				}

				$widget['sort_order'] = isset($directives['order']) ? (float)$directives['order'] : $order++;

				$widgets[] = $widget;
			}
		}

		usort($widgets, function ($a, $b) {
			return $a['sort_order'] > $b['sort_order'];
		});

		return $widgets;
	}

	public function restoreDefaults()
	{
		$this->config->saveGroup('admin', self::$admin_settings);
		$this->config->saveGroup('general', self::$general_settings);
	}
}