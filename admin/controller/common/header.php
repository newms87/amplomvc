<?php
class Admin_Controller_Common_Header extends Controller
{
	public function index()
	{
		$this->template->load('common/header');
		$this->language->load('common/header');
		
		if ($this->config->get('config_debug') && !empty($_SESSION['debug'])) {
			$this->message->add('warning', html_dump($_SESSION['debug'], 'Session Debug', 0, -1, false));
			unset($_SESSION['debug']);
		}
		
		$this->data['title'] = $this->document->getTitle();
		
		$this->data['base'] = $this->url->is_ssl() ? SITE_SSL : SITE_URL;
		
		$this->data['theme'] = $this->config->get('config_theme');
		
		//Add Styles
		$this->document->addStyle(HTTP_THEME_STYLE . 'style.css');
		$this->document->addStyle(HTTP_JS . 'jquery/ui/themes/ui-lightness/jquery-ui.custom.css');
		
		//Add Scripts
		$this->document->addScript(HTTP_JS . 'jquery/jquery.js', 50);
		$this->document->addScript(HTTP_JS . 'jquery/ui/jquery-ui.js', 51);
		$this->document->addScript(HTTP_JS . 'common.js', 53);
		$this->document->addScript(HTTP_THEME_JS . 'common.js', 54);
		$this->document->localizeScript(DIR_APPLICATION . 'view/javascript/ac_vars.php');
		
		$this->data['messages'] = $this->message->fetch();
		
		$this->data['direction'] = $this->language->getInfo('direction');
  		$this->data['description'] = $this->document->getDescription();
		$this->data['keywords'] = $this->document->getKeywords();
		$this->data['canonical_link'] = $this->document->getCanonicalLink();
		$this->data['body_class'] = $this->tool->getSlug($this->url->getPath());
		
		$this->language->set('lang', $this->language->getInfo('code'));
		
		$this->data['admin_logo'] = $this->image->get($this->config->get('config_admin_logo'));
		
		if (!$this->user->isLogged()) {
			$this->data['logged'] = '';
			
			$this->data['home'] = $this->url->link('common/login');
		} else {
			$this->data['home'] = $this->url->link('common/home');
			
			$this->data['logged'] = $this->_('text_logged', $this->user->getUserName());
			
			$menu_items = array();
			
			$this->_('support', $this->config->get('config_email_support'));
			
			$this->data['store'] = SITE_URL;
			
			//Add Store Settings
			$stores = $this->Model_Setting_Store->getStores();
			
			$link_stores = array(
				'name' => 'system_settings_stores',
				'display_name' => $this->_('text_store_settings'),
				'parent' => 'system_settings',
				'sort_order' => 1,
			);
			
			$this->document->addLink('admin', $link_stores);
			
			foreach ($stores as $index => $store) {
				$link_store_setting = array(
					'name' => 'system_settings_stores_' . $this->tool->getSlug($store['name']),
					'display_name' => $store['name'],
					'href' => $this->url->link('setting/store/update', 'store_id=' . $store['store_id']),
					'parent' => 'system_settings_stores',
					'sort_order' => $index,
				);
				
				$this->document->addLink('admin', $link_store_setting);
			}
			
			//Add the Image Manager to the Main Menu if user has permissions
			if ($this->user->hasPermission('access','common/filemanager')) {
				$link_image_manager = array(
					'name' => $this->_('text_image_manager'),
					'sort_order' => 5,
					'attrs' => array('onclick' => 'image_manager();'),
				);
				
				$this->document->addLink('admin', $link_image_manager);
			}
			
			$this->data['links_admin'] = $this->document->getLinks('admin');
			
			//Store Fronts and Settings
			$link_stores = array(
				'name' => 'stores',
				'display_name' => $this->_('text_stores'),
				'sort_order' => 0,
			);
			
			$this->document->addLink('right', $link_stores);
			
			//Link to all of the stores under the stores top level navigation
			foreach ($stores as $store) {
				$link_store = array(
					'name' => 'store_' . $store['store_id'],
					'display_name' => $store['name'],
					'href' => $this->url->store($store['store_id'], 'common/home', ''),
					'parent' => 'stores',
					'target' => '_blank',
				);
				
				$this->document->addLink('right', $link_store);
			}
			
			//Logout link
			$link_logout = array(
				'name' => 'logout',
				'display_name' => $this->_('text_logout'),
				'href' => $this->url->link('common/logout'),
				'sort_order' => 10,
			);
			
			$this->document->addLink('right', $link_logout);
			
			$this->data['links_right'] = $this->document->getLinks('right');
		}
		
		
		$this->data['styles'] = $this->document->renderStyles();
		$this->data['scripts'] = $this->document->renderScripts();
		
		//Failed Email Messages warnings
		$failed_count = $this->Model_Mail_Error->total_failed_messages();
		
		if ($failed_count) {
			$view_mail_errors = $this->url->admin('mail/error');
			$this->message->system('warning', "There are <strong>$failed_count</strong> failed email messages! <a href=\"$view_mail_errors\">(view errors)</a>");
		}
		
		$this->render();
	}
}
