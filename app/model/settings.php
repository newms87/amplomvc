<?php

/**
 * @author  Daniel Newman
 * @date    3/20/2013
 * @package Amplo MVC
 * @link    http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */
class App_Model_Settings extends Model
{
	static $icon_sizes = array(
		152,
		120,
		76,
	);

	static $admin_settings = array(
		'admin_title'                => 'Amplo MVC | Developer Friendly All Purpose Web Platform',
		'admin_icon'                 => array(
			'orig' => 'A-icon.png',
		),
		'admin_path'                 => 'admin',
		'admin_bar'                  => 1,
		'admin_logo'                 => 'amplo-logo.png',
		'admin_logo_srcset'          => 3,
		'admin_show_breadcrumbs'     => 1,
		'admin_breadcrumb_separator' => ' / ',
		'admin_language'             => 1,
		'admin_list_limit'           => 20,
		'admin_thumb_width'          => 120,
		'admin_thumb_height'         => 120,
		'admin_logo_width'           => 0,
		'admin_logo_height'          => 80,
		'admin_list_image_width'     => 60,
		'admin_list_image_height'    => 60,
	);

	static $general_settings = array(
		'site_name'                               => 'Amplo MVC',
		'site_owner'                              => '',
		'site_address'                            => '',
		'site_email'                              => 'hello@amploweb.com',
		'site_email_support'                      => 'help@amploweb.com',
		'site_email_error'                        => 'error@amploweb.com',
		'site_phone'                              => '',
		'config_fax'                              => '',
		'homepage_path'                           => 'index',
		'error_404_path'                          => 'error/not_found',
		'site_title'                              => 'Amplo MVC | Developer Friendly All Purpose Web Platform',
		'config_default_store'                    => '',
		'site_meta_description'                   => '',
		'config_debug'                            => 0,
		'cron_status'                             => 1,
		'config_allow_close_message'              => 1,
		'config_default_layout_id'                => '',
		'site_theme'                              => AMPLO_DEFAULT_THEME,
		'site_address_format'                     => '',
		'site_international'                      => 0,
		'config_country_id'                       => 223,
		'config_zone_id'                          => 8,
		'config_language'                         => 1,
		'config_use_macro_languages'              => 0,
		'config_currency'                         => '',
		'config_currency_auto'                    => '',
		'site_list_limit'                         => 10,
		'config_autocomplete_limit'               => 10,
		'config_performance_log'                  => 0,
		'default_customer_role_id'                => '',
		'config_customer_approval'                => 0,
		'terms_agreement_page_id'                 => '',
		'terms_agreement_date'                    => '',
		'config_contact_page_id'                  => '',
		'show_breadcrumbs'                        => 1,
		'breadcrumb_separator'                    => ' / ',
		'config_review_status'                    => 1,
		'config_share_status'                     => 1,
		'config_upload_allowed'                   => 1,
		'config_upload_images_allowed'            => '',
		'config_upload_images_mime_types_allowed' => '',
		'site_icon'                               => array(
			'orig' => 'A-icon.png',
		),
		'site_logo'                               => 'amplo-logo.png',
		'site_logo_srcset'                        => 3,
		'site_logo_width'                         => 0,
		'site_logo_height'                        => 80,
		'site_email_logo_width'                   => 300,
		'site_email_logo_height'                  => 0,
		'config_image_category_width'             => 120,
		'config_image_category_height'            => 120,
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
		'error_notification_email'                => 1,
		'error_logging'                           => 1,
		'ga_code'                                 => '',
		'ga_experiment_id'                        => '',
		'ga_exp_vars'                             => 0,
		'ga_domains'                              => array(),
		'ga_click_tracking'                       => 0,
		'ga_demographics'                         => 0,
		'config_statcounter'                      => array(
			'project'  => '',
			'security' => '',
		),
	);

	public function saveGeneral($settings)
	{
		if (empty($settings['site_name']) || !validate('text', $settings['site_name'], 2, 128)) {
			$this->error['site_name'] = _l("Site Name must be between 2 and 128 characters!");
		}

		if (empty($settings['site_email']) || !validate('email', $settings['site_email'])) {
			$this->error['site_email'] = _l("The Site Email does not appear to be valid!");
		} else {
			if (empty($settings['site_email_support'])) {
				$settings['site_email_support'] = $settings['site_email'];
			} elseif (!validate('email', $settings['site_email_support'])) {
				$this->error['site_email_support'] = _l("The Support Email %s does not appear to be valid.", $settings['site_email_support']);
			}

			if (empty($settings['site_email_error'])) {
				$settings['site_email_error'] = $settings['site_email'];
			} elseif (!validate('email', $settings['site_email_error'])) {
				$this->error['site_email_error'] = _l("The Error Email %s does not appear to be valid.", $settings['site_email_error']);
			}
		}

		if ($this->error) {
			return false;
		}

		if (empty($settings['site_title'])) {
			$settings['site_title'] = $settings['site_name'];
		}

		if (!empty($settings['site_icon']['orig']) && empty($settings['site_icon']['ico'])) {
			$icon_files = $this->generateIconFiles($settings['site_icon']['orig']);

			foreach ($icon_files as $icon => $icon_file) {
				if (empty($settings['site_icon'][$icon])) {
					$settings['site_icon'][$icon] = $icon_file['relpath'];
				}
			}
		}

		//Load defaults
		$settings += self::$general_settings;

		$settings['site_list_limit'] = max(0, (int)$settings['site_list_limit']);

		$result = $this->config->saveGroup('general', $settings);

		if (!$result) {
			$this->error = $this->config->fetchError();
		}

		return $result;
	}

	public function saveAdmin($settings)
	{
		if (empty($settings['admin_title'])) {
			$settings['admin_title'] = 'Amplo MVC Admin';
		}

		$settings += self::$admin_settings;

		$settings['admin_list_limit'] = max(0, (int)$settings['admin_list_limit']);

		if (!empty($settings['admin_icon']['orig']) && empty($settings['admin_icon']['ico'])) {
			$icon_files = $this->generateIconFiles($settings['admin_icon']['orig']);

			foreach ($icon_files as $icon => $icon_file) {
				if (empty($settings['admin_icon'][$icon])) {
					$settings['admin_icon'][$icon] = $icon_file['relpath'];
				}
			}
		}

		$result = $this->config->saveGroup('admin', $settings);

		if (!$result) {
			$this->error = $this->config->fetchError();
		}

		return $result;
	}

	public function getWidgets()
	{
		$dir = DIR_SITE . 'app/controller/admin/settings/';

		$widgets = cache('settings.widgets', null, false, filemtime($dir . '.'));

		if (!$widgets) {
			$widgets = array();

			$files = glob($dir . '*');

			if ($files) {
				$order = 0;

				foreach ($files as $file) {
					$directives = get_comment_directives($file);

					if (empty($directives['title'])) {
						continue;
					}

					$widget = array(
						'title'      => _l($directives['title']),
						'path'       => !empty($directives['path']) ? $directives['path'] : 'admin/settings/' . str_replace('.php', '', basename($file)),
						'query'      => !empty($directives['query']) ? $directives['query'] : '',
						'icon'       => $this->theme->getUrl(!empty($directives['icon']) ? 'image/settings/' . $directives['icon'] : 'image/settings/admin.png'),
						'sort_order' => isset($directives['order']) ? (float)$directives['order'] : $order++,
					);

					$widget['url'] = site_url($widget['path'], $widget['query']);

					$widgets[] = $widget;
				}
			}

			usort($widgets, function ($a, $b) {
				return $a['sort_order'] > $b['sort_order'];
			});

			cache('settings.widgets', $widgets);
		}

		return $widgets;
	}

	public function restoreDefaults()
	{
		$this->config->saveGroup('admin', self::$admin_settings);
		$this->config->saveGroup('general', self::$general_settings);
	}

	public function generateIconFiles($icon)
	{
		$icon_files = array();

		foreach (self::$icon_sizes as $size) {
			$url = image_save($icon, null, $size, $size);

			$icon_files[$size . 'x' . $size] = array(
				'url'     => $url,
				'relpath' => str_replace(URL_IMAGE, '', $url),
			);
		}

		$url = $this->image->ico($icon);

		$icon_files['ico'] = array(
			'relpath' => str_replace(URL_IMAGE, '', $url),
			'url'     => $url,
		);

		return $icon_files;
	}
}
