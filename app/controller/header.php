<?php

class App_Controller_Header extends Controller
{
	public function index($settings = array())
	{
		if (!page_info('title')) {
			set_page_info('title', option('site_title'));
		}

		//Add Styles
		$this->document->addStyle($this->theme->getThemeStyle());

		//Add jQuery from the CDN or locally
		if (defined("AMPLO_PRODUCTION") && AMPLO_PRODUCTION) {
			if (option('config_jquery_cdn', true)) {
				$this->document->addScript("//code.jquery.com/jquery-1.10.2.min.js", 50);
				$this->document->addScript("//code.jquery.com/ui/1.10.3/jquery-ui.js", 51);
				$this->document->addScript(URL_RESOURCES . 'js/jquery/colorbox/colorbox.js', 52);
			} else {
				$this->document->addScript(URL_RESOURCES . 'js/core.js', 50);
			}
		} else {
			if (option('config_jquery_cdn', true)) {
				$this->document->addScript("//code.jquery.com/jquery-1.10.2.min.js", 50);
				$this->document->addScript("//code.jquery.com/ui/1.10.3/jquery-ui.js", 51);
			} else {
				$this->document->addScript(URL_RESOURCES . 'js/jquery/jquery.js', 50);
				$this->document->addScript(URL_RESOURCES . 'js/jquery/ui/jquery-ui.js', 51);
			}

			$this->document->addScript(URL_RESOURCES . 'js/jquery/colorbox/colorbox.js', 52);
		}

		$this->document->addScript(URL_RESOURCES . 'js/common.js', 53);

		//TODO: Move this to admin Panel?
		$this->document->localizeVar('image_thumb_width', option('config_image_thumb_width'));
		$this->document->localizeVar('image_thumb_height', option('config_image_thumb_height'));
		$this->document->localizeVar('site_url', site_url());
		$this->document->localizeVar('theme_url', theme_url());
		$this->document->localizeVar('defer_scripts', option('defer_scripts', true));

		//Body
		$this->document->addBodyClass(slug($this->route->getPath(), '-'));

		//Admin Bar
		$settings['show_admin_bar'] = $this->user->showAdminBar();

		//Render
		$this->render('header', $settings);
	}
}
