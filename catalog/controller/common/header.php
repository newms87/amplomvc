<?php
class ControllerCommonHeader extends Controller 
{
	protected function index()
	{
		$this->template->load('common/header');

		
		if ($this->config->get('config_debug') && isset($_SESSION['debug'])) {
			html_dump($_SESSION['debug'], 'debug');
			unset($_SESSION['debug']);
		}
		
		$this->data['title'] = $this->document->getTitle();
		
		$this->data['base'] = $this->url->is_ssl() ? $this->config->get('config_ssl') : $this->config->get('config_url');
		
		//Add Styles
		$this->document->addStyle(HTTP_THEME_STYLE . 'style.css');
		$this->document->addStyle(HTTP_THEME_STYLE . 'module_styles.css');
		$this->document->addStyle(HTTP_JS . 'jquery/ui/themes/ui-lightness/jquery-ui-1.9.2.custom.css');
		$this->document->addStyle(HTTP_JS . 'jquery/colorbox/colorbox.css');
		
		//Add Scripts
		$this->document->addScript(HTTP_JS . 'jquery/jquery-1.7.1.min.js');
		$this->document->addScript(HTTP_JS . 'jquery/ui/jquery-ui-1.9.2.custom.min.js');
		$this->document->addScript(HTTP_JS . 'jquery/ui/external/jquery.bgiframe-2.1.2.js');
		$this->document->addScript(HTTP_JS . 'jquery/ui/external/jquery.cookie.js');
		$this->document->addScript(HTTP_JS . 'jquery/tabs.js');
		$this->document->addScript(HTTP_JS . 'jquery/colorbox/jquery.colorbox.min.js');
		
		//Add Theme Scripts
		$this->document->addScript(HTTP_THEME_JS . 'common.js');
		
		
		
		$this->data['direction'] = $this->language->getInfo('direction');
		
		$this->data['description'] = $this->document->getDescription();
		$this->data['keywords'] = $this->document->getKeywords();
		
		$this->data['canonical_link'] = $this->document->getCanonicalLink();
		
		$this->data['css_styles'] = $this->document->getStyles();
		$this->data['js_scripts'] = $this->document->getScripts();
		
		$this->language->set('lang', $this->language->getInfo('code'));
		
		$this->data['google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');
		$this->data['statcounter'] = $this->config->get('config_statcounter');
		
		$this->language->load('common/header');
		
		$this->data['messages'] = $this->message->fetch();
		
		if ($this->config->get('config_seo_url')) {
			$this->data['pretty_url'] = $this->url->get_pretty_url();
		}
		
		$this->data['icon'] = $this->image->get($this->config->get('config_icon'));
		
		$this->data['name'] = $this->config->get('config_name');
		
		$this->data['logo'] = $this->image->get($this->config->get('config_logo'));
		
		$this->data['page_header'] = $this->model_design_page_headers->getPageHeader();
		
		//Navigation
		$this->data['links_primary'] = $this->document->getLinks('primary');
		$this->data['links_secondary'] = $this->document->getLinks('secondary');
		
		
		//If the customer is logged in, build the account menu
		if ($this->customer->isLogged()) {
			$this->data['is_logged'] = true;
			//The Welcome Message
			$link_logged = array(
				'name' => 'logged',
				'display_name' => $this->language->format('text_logged', $this->customer->info('firstname') . ' ' . $this->customer->info('lastname')),
				'sort_order' => 0,
			);
			
			$this->document->addLink('account', $link_logged);
			
			//Account Link
			$link_account = array(
				'name' => 'account',
				'display_name' => $this->_('text_account'),
				'href' => $this->url->link('account/account'),
				'sort_order' => 1,
			);
			
			$this->document->addLink('account', $link_account);
			
			//Cart Link
			$link_cart = array(
				'name' => 'cart',
				'display_name' => $this->_('text_shopping_cart'),
				'href' => $this->url->link('cart/cart', "redirect=" . preg_replace('/redirect=[^&]*/','',$this->url->here())),
				'sort_order' => 2,
			);
			
			$this->document->addLink('account', $link_cart);
			
			//Include the Checkout navigation link only if there are products in the cart
			if ($this->cart->hasProducts()) {
				$link_checkout= array(
					'name' => 'checkout',
					'display_name' => $this->_('text_checkout'),
					'href' => $this->url->link('checkout/checkout'),
					'sort_order' => 3,
				);
			
				$this->document->addLink('account', $link_checkout);
			}
			
			$link_logout = array(
				'name' => 'logout',
				'display_name' => $this->_('text_logout'),
				'href' => $this->url->link('account/logout'),
				'sort_order' => 4,
			);
			
			$this->document->addLink('account', $link_logout);
			
			$this->data['links_account'] = $this->document->getLinks('account');
		}
		else {
			$this->data['is_logged'] = false;
			
			$this->data['block_login'] = $this->getBlock('account', 'login', array('header'));
			
			
			if (!$this->cart->isEmpty()) {
				$link_cart = array(
					'name' => 'cart',
					'display_name' => $this->_('text_shopping_cart'),
					'href' => $this->url->link('cart/cart', "redirect=" . preg_replace('/redirect=[^&]*/','',$this->url->here())),
					'sort_order' => 2,
				);
				
				$this->document->addLink('guest', $link_cart);
				
				$link_checkout= array(
					'name' => 'checkout',
					'display_name' => $this->_('text_checkout'),
					'href' => $this->url->link('checkout/checkout'),
					'sort_order' => 3,
				);
			
				$this->document->addLink('guest', $link_checkout);
			}
			
			$this->data['links_guest'] = $this->document->getLinks('guest');
		}
		
		
		$this->data['home'] = $this->url->link('common/home');
		
		$this->data['social_networks'] = $this->getBlock('extras', 'social_media');
		
		$this->children = array(
			'module/language',
			'module/currency',
			'module/cart',
			'common/above_content',
		);

		$this->render();
	}
}