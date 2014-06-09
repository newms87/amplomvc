<?php

class App_Controller_Admin_Common_Header extends Controller
{
	public function index($settings = array())
	{
		$data = $settings;

		$data['title'] = $this->document->getTitle();

		$data['base'] = site_url('admin');

		$data['theme'] = option('config_theme');

		//Add Styles
		$style = theme_dir('css/style.less');

		if ($style) {
			$style = $this->document->compileLess($style, $data['theme'] . '-' . option('store_id') . '-theme-style');
		} else {
			$style = theme_url('css/style.css');
		}

		$this->document->addStyle($style);

		$this->document->addStyle(URL_RESOURCES . 'js/jquery/ui/themes/ui-lightness/jquery-ui.custom.css');

		//Add jQuery from the CDN or locally
		if (option('config_jquery_cdn')) {
			$this->document->addScript("http://code.jquery.com/jquery-1.10.2.min.js", 50);
			$this->document->addScript("http://code.jquery.com/ui/1.10.3/jquery-ui.js", 51);
		} else {
			$this->document->addScript(URL_RESOURCES . 'js/jquery/jquery.js', 50);
			$this->document->addScript(URL_RESOURCES . 'js/jquery/ui/jquery-ui.js', 51);
		}

		$this->document->addScript(URL_RESOURCES . 'js/image_manager.js', 52);
		$this->document->addScript(URL_RESOURCES . 'js/common.js', 53);
		$this->document->addScript(theme_url('js/common.js'), 54);

		//TODO: Move this to admin Panel?
		$this->document->localizeVar('image_thumb_width', option('config_image_admin_thumb_width'));
		$this->document->localizeVar('image_thumb_height', option('config_image_admin_thumb_height'));
		$this->document->localizeVar('site_url', site_url());
		$this->document->localizeVar('admin_url', site_url('admin/'));
		$this->document->localizeVar('theme_url', theme_url());


		$data['direction']      = $this->language->info('direction');
		$data['description']    = $this->document->getDescription();
		$data['keywords']       = $this->document->getKeywords();
		$data['canonical_link'] = $this->document->getCanonicalLink();
		$data['body_class']     = $this->tool->getSlug($this->route->getPath());

		$data['lang'] = $this->language->info('code');

		$data['logged'] = $this->user->isLogged();

		if ($data['logged']) {
			//Add the Image Manager to the Main Menu if user has permissions
			if (user_can('access', 'filemanager/filemanager')) {
				$link_image_manager = array(
					'name'       => _l("Image Manager"),
					'sort_order' => 5,
					'attrs'      => array('onclick' => '$.ac_filemanager();'),
				);

				$this->document->addLink('admin', $link_image_manager);
			}

			$stores = $this->Model_Setting_Store->getStores();

			if (user_can('access', 'setting/store')) {
				//Store Front Settings

				$link_stores = array(
					'name'         => 'system_settings_stores',
					'display_name' => _l("Stores"),
					'parent'       => 'system_settings',
					'sort_order'   => 1,
				);

				$this->document->addLink('admin', $link_stores);

				foreach ($stores as $index => $store) {
					$link_store_setting = array(
						'name'         => 'system_settings_stores_' . $this->tool->getSlug($store['name']),
						'display_name' => $store['name'],
						'href'         => site_url('admin/setting/store/form', 'store_id=' . $store['store_id']),
						'parent'       => 'system_settings_stores',
						'sort_order'   => $index,
					);

					$this->document->addLink('admin', $link_store_setting);
				}
			}

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
					'href'         => $this->url->store($store['store_id'], 'common/home', ''),
					'parent'       => 'stores',
					'target'       => '_blank',
				);

				$this->document->addLink('right', $link_store);
			}

			//Logout link
			$link_logout = array(
				'name'         => 'logout',
				'display_name' => _l("Logout"),
				'href'         => site_url('admin/common/logout'),
				'sort_order'   => 10,
			);

			$this->document->addLink('right', $link_logout);
		}

		$data['styles']  = $this->document->renderStyles();
		$data['scripts'] = $this->document->renderScripts();

		//Failed Email Messages warnings
		$failed_count = $this->Model_Mail_Error->total_failed_messages();

		if ($failed_count) {
			$view_mail_errors = site_url('admin/mail/error');
			$this->message->system('warning', "There are <strong>$failed_count</strong> failed email messages! <a href=\"$view_mail_errors\">(view errors)</a>");
		}

		$data['user'] = $this->user->info();

		$this->render('common/header', $data);
	}
}
