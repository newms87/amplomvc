<?php

/**
 * Title: Theme Settings
 * Icon: theme.png
 * Order: 3
 *
 */
class App_Controller_Admin_Settings_Theme extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("Theme Settings"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Settings"), site_url('admin/settings/setting'));
		breadcrumb(_l("Theme"), site_url('admin/settings/theme'));

		//Load Data or Defaults
		$theme_configs = $this->theme->getTheme();

		$settings = $_POST;

		if (!IS_POST) {
			$settings['stylesheet'] = $theme_configs['stylesheet'];
		}

		$defaults = array(
			'configs'    => array(),
			'stylesheet' => '',
		);

		$settings += $defaults;

		$settings['configs'] += $theme_configs['configs'];

		//Actions
		$settings['save'] = site_url('admin/settings/theme/save');

		//Render
		output($this->render('settings/theme', $settings));
	}

	public function save()
	{
		$theme    = $this->config->load('config', 'config_theme');

		if (!$theme) {
			$theme = AMPLO_DEFAULT_THEME;
		}

		//Save Settings
		$this->theme->saveTheme($theme, $_POST['configs'], $_POST['stylesheet']);

		if ($this->theme->hasError()) {
			message('error', $this->theme->getError());
		} else {
			message('success', _l("You have successfully updated the Theme Settings!"));
		}

		if ($this->is_ajax) {
			output_json($this->message->fetch());
		} else {
			redirect('admin/settingss');
		}
	}

	public function restore_defaults()
	{
		if ($this->theme->restore()) {
			message('success', _l("Store theme has been restored to the default values"));
		} else {
			message('error', $this->theme->getError());
		}

		if ($this->is_ajax) {
			output_json($this->message->fetch());
		} else {
			redirect('admin/settings/theme');
		}
	}
}
