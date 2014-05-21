<?php

class App_Controller_Common_Header extends Controller
{
	public function index($settings = array())
	{
		$data = $settings;

		$data['title'] = $this->document->getTitle();

		$data['base'] = $this->request->isSSL() ? HTTPS_SITE : HTTP_SITE;

		//Add Styles
		if (is_file(DIR_THEME . 'css/style.less')) {
			$style = $this->document->compileLess(DIR_THEME . 'css/style.less', 'fluid.style.less');
		} else {
			$style = theme_url('css/style.css');
		}

		$this->document->addStyle($style);

		$this->document->addStyle(URL_RESOURCES . 'js/jquery/ui/themes/ui-lightness/jquery-ui.custom.css');
		$this->document->addStyle(URL_RESOURCES . 'js/jquery/colorbox/colorbox.css');

		//Add jQuery from the CDN or locally
		if (true || option('config_jquery_cdn')) {
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
		$data['direction']      = $this->language->info('direction');
		$data['description']    = $this->document->getDescription();
		$data['keywords']       = $this->document->getKeywords();
		$data['canonical_link'] = $this->document->getCanonicalLink();
		$data['body_class']     = $this->tool->getSlug($this->route->getPath());

		$data['styles']  = $this->document->renderStyles();
		$data['scripts'] = $this->document->renderScripts();

		$data['lang'] = $this->language->info('code');

		$data['google_analytics'] = html_entity_decode(option('config_google_analytics'), ENT_QUOTES, 'UTF-8');
		$data['statcounter']      = option('config_statcounter');

		$data['name'] = option('config_name');

		$data['logo'] = option('config_logo');

		$data['slogan'] = option('config_slogan');

		//Icons
		$data['icons'] = option('config_icon');

		//Login Check & The Welcome Message
		$data['is_logged'] = $this->customer->isLogged();
		$data['customer']  = $this->customer->info();

		//Admin Bar
		$data['show_admin_bar'] = $this->user->showAdminBar();

		//Internationalization
		$data['multi_language'] = option('config_multi_language');
		$data['multi_currency'] = option('config_multi_currency');

		//Render
		$this->render('common/header', $data);
	}
}
