<?php

class App_Controller_Admin_Header extends Controller
{
	public function index($settings = array())
	{
		$settings += array(
			'body_class' => slug($this->route->getPath(), '-'),
			'user'       => user_info(),
		);

		//Add Styles
		$style = $this->theme->getThemeStyle(option('config_admin_theme', 'admin'));

		if ($style) {
			$this->document->addStyle($style);
		}

		$this->document->addStyle(URL_RESOURCES . 'js/jquery/ui/themes/ui-lightness/jquery-ui.custom.css');
		$this->document->addStyle(URL_RESOURCES . 'js/jquery/colorbox/colorbox.css');

		//Add jQuery from the CDN or locally
		if (option('config_jquery_cdn')) {
			$this->document->addScript("//code.jquery.com/jquery-1.10.2.min.js", 50);
			$this->document->addScript("//code.jquery.com/ui/1.10.3/jquery-ui.js", 51);
		} else {
			$this->document->addScript(URL_RESOURCES . 'js/jquery/jquery.js', 50);
			$this->document->addScript(URL_RESOURCES . 'js/jquery/ui/jquery-ui.js', 51);
		}

		$this->document->addScript(URL_RESOURCES . 'js/jquery/colorbox/colorbox.js', 52);

		$this->document->addScript(URL_RESOURCES . 'js/image_manager.js', 53);
		$this->document->addScript(URL_RESOURCES . 'js/common.js', 54);
		$this->document->addScript(theme_url('js/common.js'), 55);

		//TODO: Move this to admin Panel?
		$this->document->localizeVar('image_thumb_width', option('admin_thumb_width'));
		$this->document->localizeVar('image_thumb_height', option('admin_thumb_height'));
		$this->document->localizeVar('site_url', site_url());
		$this->document->localizeVar('admin_url', site_url('admin/'));
		$this->document->localizeVar('theme_url', theme_url());

		if (is_logged()) {
			//Add the Image Manager to the Main Menu if user has permissions
			if (user_can('r', 'filemanager')) {
				$link_image_manager = array(
					'name'       => _l("Image Manager"),
					'sort_order' => 5,
					'href'       => 'admin/filemanager',
					'#data-ajax' => 'iframe',
					//'attrs'      => array('onclick' => '$.ac_filemanager();'),
				);

				$this->document->addLink('admin', $link_image_manager);
			}

			if (user_can('r', 'admin/dashboards')) {
				$dashboards = $this->Model_Dashboard->getDashboards(true);

				foreach ($dashboards as $dashboard) {
					if (!$this->document->hasLink('admin', 'dashboards')) {
						$dashboards_link = array(
							'name'         => 'dashboards',
							'display_name' => _l("Dashboards"),
						);

						$this->document->addLink('admin', $dashboards_link);
					}

					$dashboard_link = array(
						'name'         => 'dashboards_dash-' . $dashboard['dashboard_id'],
						'display_name' => strip_tags($dashboard['title']),
						'href'         => site_url('admin/dashboard/view', 'dashboard_id=' . $dashboard['dashboard_id']),
						'parent'       => 'dashboards',
					);

					$this->document->addLink('admin', $dashboard_link);
				}
			}

			if (user_can('r', 'admin/settings')) {
				$widgets = $this->Model_Settings->getWidgets();

				foreach ($widgets as $widget) {
					$link_widget = array(
						'parent'       => 'system_settings',
						'name'         => slug($widget['title']),
						'display_name' => "<img class=\"icon\" src=\"$widget[icon]\" />" . $widget['title'],
						'href'         => $widget['url'],
						'sort_order'   => $widget['sort_order'],
					);

					$this->document->addLink('admin', $link_widget);
				}
			}

			$stores = $this->Model_Setting_Store->getStores();

			//Store Front Links
			$link_stores = array(
				'name'         => 'stores',
				'display_name' => _l("Stores"),
				'sort_order'   => 0,
			);

			$this->document->addLink('right', $link_stores);

			//Link to all of the stores under the stores top level navigation
			foreach ($stores as $store) {
				$link_store = array(
					'name'         => 'store_' . $store['store_id'],
					'display_name' => $store['name'],
					'href'         => site_url('', '', null, $store['store_id']),
					'parent'       => 'stores',
					'target'       => '_blank',
				);

				$this->document->addLink('right', $link_store);
			}

			//Logout link
			$link_logout = array(
				'name'         => 'logout',
				'display_name' => _l("Logout"),
				'href'         => site_url('admin/user/logout'),
				'sort_order'   => 10,
			);

			$this->document->addLink('right', $link_logout);
		}

		//Failed Email Messages warnings
		$failed_count = $this->Model_Mail_Error->total_failed_messages();

		if ($failed_count) {
			$view_mail_errors = site_url('admin/mail/error');
			$this->message->system('warning', "There are <strong>$failed_count</strong> failed email messages! <a href=\"$view_mail_errors\">(view errors)</a>");
		}

		$this->render('header', $settings);
	}
}
