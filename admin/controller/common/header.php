<?php

class Admin_Controller_Common_Header extends Controller
{
	public function index($settings = array())
	{
		$data = $settings;

		$data['title'] = $this->document->getTitle();

		$data['base'] = URL_SITE;

		$data['theme'] = $this->config->get('config_theme');

		//Add Styles
		if (is_file(DIR_THEME . 'css/style.less')) {
			$style = $this->document->compileLess(DIR_THEME . 'css/style.less', 'default.style');
		} else {
			$style = URL_THEME . 'css/style.css';
		}

		$this->document->addStyle($style);

		$this->document->addStyle(URL_RESOURCES . 'js/jquery/ui/themes/ui-lightness/jquery-ui.custom.css');

		//Add jQuery from the CDN or locally
		if ($this->config->get('config_jquery_cdn')) {
			$this->document->addScript("http://code.jquery.com/jquery-1.10.2.min.js", 50);
			$this->document->addScript("http://code.jquery.com/ui/1.10.3/jquery-ui.js", 51);
		} else {
			$this->document->addScript(URL_RESOURCES . 'js/jquery/jquery.js', 50);
			$this->document->addScript(URL_RESOURCES . 'js/jquery/ui/jquery-ui.js', 51);
		}

		$this->document->addScript(URL_RESOURCES . 'js/image_manager.js', 52);
		$this->document->addScript(URL_RESOURCES . 'js/common.js', 53);
		$this->document->addScript(URL_THEME_JS . 'common.js', 54);

		//TODO: Move this to admin Panel?
		$this->document->localizeVar('image_thumb_width', $this->config->get('config_image_admin_thumb_width'));
		$this->document->localizeVar('image_thumb_height', $this->config->get('config_image_admin_thumb_height'));
		$this->document->localizeVar('url_site', URL_SITE);

		$data['messages'] = $this->message->fetch();

		$data['direction']      = $this->language->info('direction');
		$data['description']    = $this->document->getDescription();
		$data['keywords']       = $this->document->getKeywords();
		$data['canonical_link'] = $this->document->getCanonicalLink();
		$data['body_class']     = $this->tool->getSlug($this->url->getPath());

		$data['lang'] = $this->language->info('code');

		$data['admin_logo'] = $this->image->get($this->config->get('config_admin_logo'));

		if (!$this->user->isLogged()) {
			$data['logged'] = '';

			$data['home'] = $this->url->link('common/login');
		} else {
			$data['home'] = $this->url->link('common/home');

			$data['logged'] = _l("You are logged in as <span>%s</span>", $this->user->info('username'));

			$data['support'] = _l("<a href=\"mailto:%s?subject=Support%%20Request\" target=\"_blank\">Support</a>", $this->config->get('config_email_support'));

			$data['store'] = URL_SITE;

			//Add Store Settings
			$stores = $this->Model_Setting_Store->getStores();

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
					'href'         => $this->url->link('setting/store/update', 'store_id=' . $store['store_id']),
					'parent'       => 'system_settings_stores',
					'sort_order'   => $index,
				);

				$this->document->addLink('admin', $link_store_setting);
			}

			//Add the Image Manager to the Main Menu if user has permissions
			if ($this->user->can('access', 'common/filemanager')) {
				$link_image_manager = array(
					'name'       => _l("Image Manager"),
					'sort_order' => 5,
					'attrs'      => array('onclick' => '$.ac_filemanager();'),
				);

				$this->document->addLink('admin', $link_image_manager);
			}

			$data['links_admin'] = $this->document->getLinks('admin');

			//Store Fronts and Settings
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
				'href'         => $this->url->link('common/logout'),
				'sort_order'   => 10,
			);

			$this->document->addLink('right', $link_logout);

			$data['links_right'] = $this->document->getLinks('right');
		}


		$data['styles']  = $this->document->renderStyles();
		$data['scripts'] = $this->document->renderScripts();

		//Failed Email Messages warnings
		$failed_count = $this->Model_Mail_Error->total_failed_messages();

		if ($failed_count) {
			$view_mail_errors = $this->url->admin('mail/error');
			$this->message->system('warning', "There are <strong>$failed_count</strong> failed email messages! <a href=\"$view_mail_errors\">(view errors)</a>");
		}

		$this->render('common/header', $data);
	}
}
