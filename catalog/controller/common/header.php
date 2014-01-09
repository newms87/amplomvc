<?php

class Catalog_Controller_Common_Header extends Controller
{
	public function index()
	{
		$this->template->load('common/header');

		//TODO: Probably dont need this anymore...
		if ($this->config->get('config_debug') && isset($_SESSION['debug'])) {
			html_dump($_SESSION['debug'], 'debug');
			unset($_SESSION['debug']);
		}

		$this->data['title'] = $this->document->getTitle();

		$this->data['base'] = $this->url->is_ssl() ? $this->config->get('config_ssl') : $this->config->get('config_url');

		//Add Styles
		$this->document->addStyle(HTTP_THEME_STYLE . 'style.css');
		$this->document->addStyle(HTTP_JS . 'jquery/ui/themes/ui-lightness/jquery-ui.custom.css');
		$this->document->addStyle(HTTP_JS . 'jquery/colorbox/colorbox.css');

		//Add jQuery from the CDN or locally
		if ($this->config->get('config_jquery_cdn')) {
			$this->document->addScript("http://code.jquery.com/jquery-1.10.2.min.js", 50);
			$this->document->addScript("http://code.jquery.com/ui/1.10.3/jquery-ui.js", 51);
		} else {
			$this->document->addScript(HTTP_JS . 'jquery/jquery.js', 50);
			$this->document->addScript(HTTP_JS . 'jquery/ui/jquery-ui.js', 51);
		}

		$this->document->addScript(HTTP_JS . 'common.js', 53);

		//TODO: Move this to admin Panel?
		$this->document->localizeVar('image_thumb_width', $this->config->get('config_image_thumb_width'));
		$this->document->localizeVar('image_thumb_height', $this->config->get('config_image_thumb_height'));
		$this->document->localizeVar('url_add_to_cart', $this->url->link('cart/cart/add'));

		//Add Theme Scripts
		$this->document->addScript(HTTP_THEME_JS . 'common.js', 56);

		//Page Head
		$this->data['direction']      = $this->language->getInfo('direction');
		$this->data['description']    = $this->document->getDescription();
		$this->data['keywords']       = $this->document->getKeywords();
		$this->data['canonical_link'] = $this->document->getCanonicalLink();
		$this->data['body_class']     = $this->tool->getSlug($this->url->getPath());

		$this->data['styles']  = $this->document->renderStyles();
		$this->data['scripts'] = $this->document->renderScripts();

		$this->language->set('lang', $this->language->getInfo('code'));

		$this->data['google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');
		$this->data['statcounter']      = $this->config->get('config_statcounter');

		$this->language->load('common/header');

		$this->data['messages'] = $this->message->fetch();
		$this->data['icon']     = $this->image->get($this->config->get('config_icon'));
		$this->data['name']     = $this->config->get('config_name');

		$logo_width  = $this->config->get('config_logo_width');
		$logo_height = $this->config->get('config_logo_height');

		$this->data['logo']        = $this->image->resize($this->config->get('config_logo'), $logo_width, $logo_height);

		//Admin Bar
		if ($this->user->isLogged()) {
			$this->data['admin_bar'] = $this->config->get('config_admin_bar');
			$this->data['admin_link'] = $this->url->admin();
			$this->data['clock_time'] = $this->date->now('datetime_long');
			$time_inc = 3600 * 24;
			$this->data['sim_forward'] = $this->url->here('sim_time=' . $time_inc);
			$this->data['sim_back'] = $this->url->here('sim_time=-' . $time_inc);
			$this->data['sim_reset'] = $this->url->here('sim_time=reset');
		}

		//Navigation
		$this->data['links_primary']   = $this->document->getLinks('primary');
		$this->data['links_secondary'] = $this->document->getLinks('secondary');
		$this->data['links_account']   = $this->document->getLinks('account');
		$this->data['links_cart']      = $this->document->getLinks('cart');

		//Login Check & The Welcome Message
		$this->data['is_logged'] = $this->customer->isLogged();
		$this->_('text_logged', $this->customer->info('firstname'));

		if (!$this->data['is_logged']) {
			$this->data['block_login'] = $this->getBlock('account/login');

			$this->data['login'] = $this->url->link('account/login');
		}

		$this->data['home'] = $this->url->link('common/home');

		$this->data['social_networks'] = $this->getBlock('extras/social_media');

		if ($this->config->get('config_multi_language')) {
			$this->data['block_languages'] = $this->getBlock('localisation/language');
		}

		if ($this->config->get('config_multi_currency')) {
			$this->data['block_currencies'] = $this->getBlock('localisation/currency');
		}

		//Dependencies
		$this->children = array(
			'common/above_content',
		);

		//Render
		$this->render();
	}
}
