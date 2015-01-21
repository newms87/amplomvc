<?php

class App_Controller_Header extends Controller
{
	public function index($settings = array())
	{
		//Add Styles
		$style = $this->theme->getThemeStyle();

		if ($style) {
			$this->document->addStyle($style);
		}

		$this->document->addStyle(URL_RESOURCES . 'js/jquery/ui/themes/ui-lightness/jquery-ui.custom.css');
		$this->document->addStyle(URL_RESOURCES . 'js/jquery/colorbox/colorbox.css');

		//Add jQuery from the CDN or locally
		if (option('config_jquery_cdn', true)) {
			$this->document->addScript("//code.jquery.com/jquery-1.10.2.min.js", 50);
			$this->document->addScript("//code.jquery.com/ui/1.10.3/jquery-ui.js", 51);
		} else {
			$this->document->addScript(URL_RESOURCES . 'js/jquery/jquery.js', 50);
			$this->document->addScript(URL_RESOURCES . 'js/jquery/ui/jquery-ui.js', 51);
		}

		$this->document->addScript(URL_RESOURCES . 'js/jquery/colorbox/colorbox.js', 52);

		$this->document->addScript(URL_RESOURCES . 'js/common.js', 53);

		//TODO: Move this to admin Panel?
		$this->document->localizeVar('image_thumb_width', option('config_image_thumb_width'));
		$this->document->localizeVar('image_thumb_height', option('config_image_thumb_height'));
		$this->document->localizeVar('site_url', site_url());
		$this->document->localizeVar('theme_url', theme_url());

		//Body
		$this->document->addBodyClass(slug($this->route->getPath(), '-'));
		$settings['body_class'] = $this->document->getBodyClass();

		//Admin Bar
		$settings['show_admin_bar'] = $this->user->showAdminBar();

		//Render
		$this->render('header', $settings);
	}
}
