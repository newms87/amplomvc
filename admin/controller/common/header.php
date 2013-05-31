<?php
class Admin_Controller_Common_Header extends Controller 
{
	protected function index()
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
		$this->document->addStyle(HTTP_JS . 'jquery/ui/themes/ui-lightness/jquery-ui-1.9.2.custom.css');
		
		//Add Scripts
		$this->document->addScript(HTTP_JS . 'jquery/jquery-1.7.1.min.js');
		$this->document->addScript(HTTP_JS . 'jquery/ui/jquery-ui-1.9.2.custom.min.js');
		$this->document->addScript(HTTP_JS . 'jquery/tabs.js');
		$this->document->addScript(HTTP_THEME_JS . 'common.js');
		
		$this->data['messages'] = $this->message->fetch();
		
		$this->data['direction'] = $this->language->getInfo('direction');
		
  		$this->data['description'] = $this->document->getDescription();
		$this->data['keywords'] = $this->document->getKeywords();
		
		$this->data['canonical_link'] = $this->document->getCanonicalLink();
		
		$this->language->set('lang', $this->language->getInfo('code'));
		
		if ($this->config->get('config_seo_url')) {
			$this->data['pretty_url'] = $this->url->get_pretty_url();
		}
		
		$this->data['admin_logo'] = $this->image->get($this->config->get('config_admin_logo'));
		
		if (!$this->user->isLogged()) {
			$this->data['logged'] = '';
			
			$this->data['home'] = $this->url->link('common/login');
		} else {
			$this->data['home'] = $this->url->link('common/home');
			
			$this->data['logged'] = $this->language->format('text_logged', $this->user->getUserName());
			
			$menu_items = array();
			if ($this->user->isDesigner()) {
				$this->language->format('support',"mailto:" . $this->config->get('config_email'));
				$menu_items = array(
					'product'=>'catalog/product','product_insert'=>'catalog/product/insert',
					'home'=>'common/home',
					'designers'=>'catalog/manufacturer',
					'logout'=>'common/logout'
				);
				$this->data['user_info'] = $this->url->link('user/user/update','user_id='.$this->user->getId());
			}
			else {
				$this->language->format('support', $this->config->get('config_email_support'));
				
				$this->data['store'] = SITE_URL;
				
				//Add the Image Manager to the Main Menu if user has permissions
				if ($this->user->hasPermission('access','common/filemanager')) {
					$link_image_manager = array(
						'name' => $this->_('text_image_manager'),
						'sort_order' => 3,
						'attrs' => array('onclick' => 'image_manager();'),
					);
					
					$this->document->addLink('admin', $link_image_manager);
				}
			}
			
			$this->data['links_admin'] = $this->document->getLinks('admin');
			
			/*
			* Right Side Navigation Menu
			*/
			
			//Store Navigation
			$link_stores = array(
				'name' => 'stores',
				'display_name' => $this->_('text_stores'),
			);
			
			$this->document->addLink('right', $link_stores);
			
			//Link to all of the stores under the stores top level navigation
			$stores = $this->Model_Setting_Store->getStores();
			
			foreach ($stores as $store) {
				$link_store = array(
					'name' => 'store_' . $store['store_id'],
					'display_name' => $store['name'],
					'href' => $this->url->store($store['store_id'], 'common/home', ''),
					'parent' => 'stores',
					'attrs' => array('target'=>'_blank')
				);
				
				$this->document->addLink('right', $link_store);
			}
			
			//Logout link
			$link_logout = array(
				'name' => 'logout',
				'display_name' => $this->_('text_logout'),
				'href' => $this->url->link('common/logout'),
			);
			
			$this->document->addLink('right', $link_logout);
			
			$this->data['links_right'] = $this->document->getLinks('right');
		}
		
		
		$this->data['css_styles'] = $this->document->getStyles();
		$this->data['js_scripts'] = $this->document->getScripts();
		
		
		//Failed Email Messages warnings
		$failed_count = $this->Model_Mail_Error->total_failed_messages();
		
		if ($failed_count) {
			$view_mail_errors = $this->url->admin('mail/error');
			$this->message->add('warning', "There are <strong>$failed_count</strong> failed email messages! <a href=\"$view_mail_errors\">(view errors)</a>");
		}
		
		$this->render();
	}
}
