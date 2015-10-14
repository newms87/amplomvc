<?php

class App_Controller_Admin_Header extends Controller
{
	public function index($settings = array())
	{
		$settings += array(
			'user' => user_info(),
		);

		//Add Styles
		$style = $this->theme->getThemeStyle();

		if ($style) {
			$this->document->addStyle($style);
		}

		//Add jQuery from the CDN or locally
		if (option('config_jquery_cdn')) {
			$this->document->addScript("//code.jquery.com/jquery-1.10.2.min.js", 50);
			$this->document->addScript("//code.jquery.com/ui/1.10.3/jquery-ui.js", 51);
		} else {
			$this->document->addScript(DIR_RESOURCES . 'js/jquery/jquery.js', 50);
			$this->document->addScript(DIR_RESOURCES . 'js/jquery/ui/jquery-ui.js', 51);
		}

		$this->document->addScript(DIR_RESOURCES . 'js/jquery/colorbox/colorbox.js', 52);

		$this->document->addScript(DIR_JS . 'common.js', 54);
		$this->document->addScript(theme_dir('js/common.js'), 55);

		//TODO: Move this to admin Panel?
		js_var('image_thumb_width', option('admin_thumb_width'));
		js_var('image_thumb_height', option('admin_thumb_height'));
		js_var('site_url', site_url());
		js_var('admin_url', site_url('admin/'));
		js_var('theme_url', theme_url());
		js_var('show_msg_inline', option('show_msg_inline', false));
		js_var('defer_scripts', option('defer_scripts', true));

		if (is_logged()) {
			//Add the Image Manager to the Main Menu if user has permissions
			if (user_can('r', 'admin/filemanager')) {
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
				$dashboards = $this->Model_Dashboard->getUserDashboards();

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
					if (user_can('r', $widget['path'])) {
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
			}

			if (user_can('r', 'admin/site')) {
				$options = array(
					'index' => 'site_id',
					'cache' => true
				);

				$sites = $this->Model_Site->getRecords(null, null, $options);

				//Store Front Links
				$link_sites = array(
					'name'         => 'sites',
					'display_name' => _l("Sites"),
					'class'        => 'popup',
					'sort_order'   => 0,
				);

				$this->document->addLink('right', $link_sites);

				//Link to all of the stores under the stores top level navigation
				foreach ($sites as $site_id => $site) {
					$linke_site = array(
						'name'         => 'site_' . $site_id,
						'display_name' => $site['name'],
						'href'         => site_url('', '', null, $site_id),
						'parent'       => 'sites',
						'target'       => '_blank',
					);

					$this->document->addLink('right', $linke_site);
				}
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

		$this->document->addBodyClass(slug($this->route->getPath(), '-'));

		$this->render('header', $settings);
	}
}
