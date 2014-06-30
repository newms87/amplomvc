<?php

/**
 * Title: Theme Settings
 * Icon: theme.png
 * Order: 3
 *
 */
class App_Controller_Admin_Setting_Theme extends Controller
{
	static $allow = array(
		'modify' => array(
			'save',
		),
	);

	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Theme Settings"));

		$store_id = _get('store_id', 0);

		$store = $this->Model_Setting_Store->getStore($store_id);

		if (!$store) {
			$store = array(
				'store_id' => 0,
				'name'     => _l("All Stores"),
			);
		}

		$theme = $this->config->load('config', 'config_theme', $store_id);

		if (!$theme) {
			$theme = 'fluid';
		}

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("Settings"), site_url('admin/setting/setting'));
		$this->breadcrumb->add(_l("Theme for %s", $store['name']), site_url('admin/setting/theme'));

		//Load Data or Defaults
		$theme_configs = $this->theme->getThemeConfigs($store_id, $theme);

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

		$settings['store']       = $store;
		$settings['data_stores'] = $this->Model_Setting_Store->getStores();

		//Actions
		$settings['save'] = site_url('admin/setting/theme/save', 'store_id=' . $store_id);

		//Render
		output($this->render('setting/theme', $settings));
	}

	public function save()
	{
		$store_id = _get('store_id', 0);
		$theme    = $this->config->load('config', 'config_theme', $store_id);

		if (!$theme) {
			$theme = 'fluid';
		}

		//Save Settings
		$this->theme->saveThemeConfigs($store_id, $theme, $_POST['configs'], $_POST['stylesheet']);

		if ($this->theme->hasError()) {
			message('error', $this->theme->getError());
		} else {
			message('success', _l("You have successfully updated the Theme Settings!"));
		}

		if (IS_AJAX) {
			output($this->message->toJSON());
		} else {
			redirect('admin/setting/store');
		}
	}
}
