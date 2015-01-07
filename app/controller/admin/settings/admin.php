<?php

/**
 * Title: Admin Settings
 * Icon: admin.png
 *
 */
class App_Controller_Admin_Settings_Admin extends Controller
{
	static $icon_sizes = array(
		152,
		120,
		76,
	);

	public function index()
	{
		//Page Head
		set_page_info('title', _l("Admin Settings"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Settings"), site_url('admin/settings'));
		breadcrumb(_l("Admin Settings"), site_url('admin/settings/admin'));

		//Load Information
		$settings = $_POST;

		if (!IS_POST) {
			$settings = $this->config->loadGroup('admin');
		}

		$settings += App_Model_Settings::$admin_settings;

		//Template Data
		$settings['data_languages'] = $this->Model_Localisation_Language->getLanguages();

		$settings['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		$settings['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		//Website Icon Sizes
		if (!is_array($settings['admin_icon'])) {
			$settings['admin_icon'] = array();
		}

		$settings['admin_icon'] += array(
			'orig' => '',
			'ico'  => '',
		);

		foreach (self::$icon_sizes as $size) {
			$key = $size . 'x' . $size;

			if (!isset($settings['admin_icon'][$key])) {
				$settings['admin_icon'][$key] = '';
			}
		}

		foreach ($settings['admin_icon'] as &$icon) {
			$icon = array(
				'thumb' => $this->image->get($icon),
				'src'   => $icon,
			);
		}
		unset($icon);

		$settings['data_icon_sizes'] = self::$icon_sizes;

		//Render
		output($this->render('settings/admin', $settings));
	}

	public function save()
	{
		if ($this->Model_Settings->saveAdmin($_POST)) {
			message('success', _l("The Admin Settings have been saved!"));
		} else {
			message('error', $this->Model_Settings->getError());
		}

		if ($this->is_ajax) {
			output_json($this->message->fetch());
		} elseif ($this->message->has('error')) {
			post_redirect('admin/settings/admin');
		} else {
			redirect('admin/settings');
		}
	}
}
