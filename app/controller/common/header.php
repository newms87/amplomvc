<?php

class App_Controller_Common_Header extends Controller
{
	public function index($settings = array())
	{
		$settings['title'] = $this->document->getTitle();

		$settings['base'] = IS_SSL ? HTTPS_SITE : HTTP_SITE;

		$settings['name']   = option('config_name');
		$settings['logo']   = option('config_logo');
		$settings['slogan'] = option('config_slogan');
		$settings['theme']  = option('config_theme');

		//Add Styles
		$style = $this->theme->getStoreThemeStyle(option('store_id'), $settings['theme']);

		if ($style) {
			$this->document->addStyle($style);
		}

		$this->document->addStyle(URL_RESOURCES . 'js/jquery/ui/themes/ui-lightness/jquery-ui.custom.css');
		$this->document->addStyle(URL_RESOURCES . 'js/jquery/colorbox/colorbox.css');

		//Add jQuery from the CDN or locally
		if (option('config_jquery_cdn', true)) {
			$this->document->addScript("http://code.jquery.com/jquery-1.10.2.min.js", 50);
			$this->document->addScript("http://code.jquery.com/ui/1.10.3/jquery-ui.js", 51);
		} else {
			$this->document->addScript(URL_RESOURCES . 'js/jquery/jquery.js', 50);
			$this->document->addScript(URL_RESOURCES . 'js/jquery/ui/jquery-ui.js', 51);
		}

		$this->document->addScript(URL_RESOURCES . 'js/common.js', 53);

		//TODO: Move this to admin Panel?
		$this->document->localizeVar('image_thumb_width', option('config_image_thumb_width'));
		$this->document->localizeVar('image_thumb_height', option('config_image_thumb_height'));
		$this->document->localizeVar('site_url', site_url());
		$this->document->localizeVar('theme_url', theme_url());

		//Add Theme Scripts
		$this->document->addScript(theme_url('js/common.js'), 56);

		//Page Head
		$settings['direction']      = $this->language->info('direction');
		$settings['description']    = $this->document->getDescription();
		$settings['keywords']       = $this->document->getKeywords();
		$settings['canonical_link'] = $this->document->getCanonicalLink();
		$settings['body_class']     = slug($this->route->getPath(), '-');

		$settings['styles']  = $this->document->renderStyles();
		$settings['scripts'] = $this->document->renderScripts();

		$settings['google_analytics'] = html_entity_decode(option('config_google_analytics'), ENT_QUOTES, 'UTF-8');
		$settings['statcounter']      = option('config_statcounter');

		//Icons
		$settings['icons'] = option('config_icon');

		//Login Check & The Welcome Message
		$settings['is_logged'] = $this->customer->isLogged();
		$settings['customer']  = $this->customer->info();

		//Admin Bar
		$settings['show_admin_bar'] = $this->user->showAdminBar();

		//Internationalization
		$settings['lang']           = $this->language->info('code');
		$settings['multi_language'] = option('config_multi_language');
		$settings['multi_currency'] = option('config_multi_currency');

		//Render
		$this->render('common/header', $settings);
	}
}
