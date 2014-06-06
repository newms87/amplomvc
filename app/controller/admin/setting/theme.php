<?php

/**
 * Title: Theme Settings
 * Icon: theme_settings.png
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

		$theme = $this->config->load($store_id, 'config', 'config_theme');

		if (!$theme) {
			$theme = 'fluid';
		}

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url());
		$this->breadcrumb->add(_l("Settings"), site_url('admin/setting/setting'));
		$this->breadcrumb->add(_l("Theme for %s", $store['name']), site_url('admin/setting/theme'));

		//Load Data or Defaults
		$settings = $_POST;

		$defaults = array(
			'configs' => array(),
		);

		$settings += $defaults;

		$settings['configs'] += $this->theme->getThemeConfigs($store_id, $theme);

		$settings['store']       = $store;
		$settings['data_stores'] = $this->Model_Setting_Store->getStores();

		//Actions
		$settings['save'] = site_url('admin/setting/theme/save');

		//Render
		$this->response->setOutput($this->render('setting/theme', $settings));
	}

	public function save()
	{
		$store_id = _get('store_id', 0);
		$theme    = $this->config->load($store_id, 'config', 'config_theme');

		//Save Settings
		$this->theme->saveThemeConfigs($store_id, $theme, $_POST['configs']);

		if ($this->theme->hasError()) {
			$this->message->add('error', $this->theme->getError());
		} else {
			$this->message->add('success', _l("You have successfully updated the Theme Settings!"));
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} else {
			redirect('admin/setting/store');
		}
	}
}
