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
				$this->document->addScript(DIR_RESOURCES . 'js/jquery/colorbox/colorbox.js', 52);
			} else {
				$this->document->addScript(DIR_JS . 'core.js', 50);
			}
		} else {
			if (option('config_jquery_cdn', true)) {
				$this->document->addScript("//code.jquery.com/jquery-1.10.2.min.js", 50);
				$this->document->addScript("//code.jquery.com/ui/1.10.3/jquery-ui.js", 51);
			} else {
				$this->document->addScript(DIR_RESOURCES . 'js/jquery/jquery.js', 50);
				$this->document->addScript(DIR_RESOURCES . 'js/jquery/ui/jquery-ui.js', 51);
			}

			$this->document->addScript(DIR_RESOURCES . 'js/jquery/colorbox/colorbox.js', 52);
		}

		$this->document->addScript(DIR_JS . 'common.js', 53);

		//TODO: Move this to admin Panel?
		js_var('image_thumb_width', option('config_image_thumb_width'));
		js_var('image_thumb_height', option('config_image_thumb_height'));
		js_var('site_url', site_url());
		js_var('theme_url', theme_url());
		js_var('show_msg_inline', option('show_msg_inline', false));
		js_var('show_msg_delay', option('show_msg_delay', 8000));
		js_var('defer_scripts', option('defer_scripts', true));

		//Body
		$this->document->addBodyClass(slug($this->route->getPath(), '-'));

		//Admin Bar
		$settings['show_admin_bar'] = $this->user->showAdminBar();

		//Terms Agreement
		if (option('show_terms_agreement')) {
			$settings['terms_page'] = $this->Model_Page->getRecord(option('terms_agreement_page_id'));
		}

		//Render
		$this->render('header', $settings);
	}
}
