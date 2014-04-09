<?php

class Catalog_Controller_Common_Header extends Controller
{
	public function index()
	{
		$data = array(
			'title' => $this->document->getTitle(),
		);

		$data['base'] = $this->url->is_ssl() ? $this->config->get('config_ssl') : $this->config->get('config_url');

		//Add Styles
		if (is_file(DIR_THEME . 'css/style.less')) {
			$style = $this->document->compileLess(DIR_THEME . 'css/style.less', 'fluid.style.less');
		} else {
			$style = URL_THEME . 'css/style.css';
		}

		$this->document->addStyle($style);

		$this->document->addStyle(URL_RESOURCES . 'js/jquery/ui/themes/ui-lightness/jquery-ui.custom.css');
		$this->document->addStyle(URL_RESOURCES . 'js/jquery/colorbox/colorbox.css');

		//Add jQuery from the CDN or locally
		if ($this->config->get('config_jquery_cdn')) {
			$this->document->addScript("http://code.jquery.com/jquery-1.10.2.min.js", 50);
			$this->document->addScript("http://code.jquery.com/ui/1.10.3/jquery-ui.js", 51);
		} else {
			$this->document->addScript(URL_RESOURCES . 'js/jquery/jquery.js', 50);
			$this->document->addScript(URL_RESOURCES . 'js/jquery/ui/jquery-ui.js', 51);
		}

		$this->document->addScript(URL_RESOURCES . 'js/common.js', 53);

		//TODO: Move this to admin Panel?
		$this->document->localizeVar('image_thumb_width', $this->config->get('config_image_thumb_width'));
		$this->document->localizeVar('image_thumb_height', $this->config->get('config_image_thumb_height'));
		$this->document->localizeVar('url_add_to_cart', $this->url->link('cart/cart/add'));

		//Add Theme Scripts
		$this->document->addScript(URL_THEME_JS . 'common.js', 56);

		//Page Head
		$data['direction']      = $this->language->info('direction');
		$data['description']    = $this->document->getDescription();
		$data['keywords']       = $this->document->getKeywords();
		$data['canonical_link'] = $this->document->getCanonicalLink();
		$data['body_class']     = $this->tool->getSlug($this->url->getPath());

		$data['styles']  = $this->document->renderStyles();
		$data['scripts'] = $this->document->renderScripts();

		$data['lang'] = $this->language->info('code');

		$data['google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');
		$data['statcounter']      = $this->config->get('config_statcounter');

		$data['messages'] = $this->message->fetch();
		$data['name']     = $this->config->get('config_name');

		$logo_width  = $this->config->get('config_logo_width');
		$logo_height = $this->config->get('config_logo_height');

		$data['logo'] = $this->image->resize($this->config->get('config_logo'), $logo_width, $logo_height);

		$data['slogan'] = $this->config->get('config_slogan');

		//Icons
		$icons = $this->config->get('config_icon');

		if (!empty($icons)) {
			foreach ($icons as &$icon) {
				$icon = $this->image->get($icon);
			}
			unset($icon);

			$data['icons'] = $icons;
		}

		//Admin Bar
		if ($this->user->isLogged()) {
			$data['block_admin_bar'] = $this->block->render('widget/admin_bar');
		}

		//Navigation
		$data['links_primary']   = $this->document->getLinks('primary');
		$data['links_secondary'] = $this->document->getLinks('secondary');
		$data['links_account']   = $this->document->getLinks('account');
		$data['links_cart']      = $this->document->getLinks('cart');

		//Login Check & The Welcome Message
		$data['is_logged'] = $this->customer->isLogged();
		$data['customer']  = $this->customer->info();

		if (!$data['is_logged']) {
			$data['block_login'] = $this->block->render('account/login');
		}

		//Action Buttons
		$data['home']     = $this->url->link('common/home');
		$data['login']    = $this->url->link('account/login');
		$data['register'] = $this->url->link('account/register');
		$data['logout']   = $this->url->link('account/logout');


		$data['social_networks'] = $this->block->render('extras/social_media');

		if ($this->config->get('config_multi_language')) {
			$data['block_languages'] = $this->block->render('localisation/language');
		}

		if ($this->config->get('config_multi_currency')) {
			$data['block_currencies'] = $this->block->render('localisation/currency');
		}

		//Dependencies
		$this->children = array(
			'area/above',
		);

		//Render
		$this->render('common/header', $data);
	}
}
