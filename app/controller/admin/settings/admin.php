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
		$this->document->setTitle(_l("Admin Settings"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Settings"), site_url('admin/settings'));
		breadcrumb(_l("General Settings"), site_url('admin/settings/admin'));

		//Load Information
		$config_data = $_POST;

		if (!IS_POST) {
			$config_data = $this->config->loadGroup('admin');
		}

		$defaults = array(
			'admin_title'                            => 'Amplo MVC | Developer Friendly All Purpose Web Platform',
			'admin_icon'                              => null,
			'admin_bar'                               => 1,
			'admin_logo'                              => '',
			'admin_breadcrumb_separator'              => ' / ',
			'admin_language'                          => 1,
			'admin_list_limit'                        => 20,
			'admin_thumb_width'                       => 120,
			'admin_thumb_height'                      => 120,
			'admin_list_image_width'                  => 60,
			'admin_list_image_height'                 => 60,
		);

		$data = $config_data + $defaults;

		//Template Data
		$data['data_languages']       = $this->Model_Localisation_Language->getLanguages();

		$data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		$data['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		//Website Icon Sizes
		if (!is_array($data['config_icon'])) {
			$data['config_icon'] = array(
				'orig' => '',
				'ico'  => '',
			);
		}

		foreach (self::$icon_sizes as $size) {
			$key = $size . 'x' . $size;

			if (!isset($data['config_icon'][$key])) {
				$data['config_icon'][$key] = '';
			}
		}

		foreach ($data['config_icon'] as &$icon) {
			$icon = array(
				'thumb' => $this->image->get($icon),
				'src'   => $icon,
			);
		}
		unset($icon);

		$data['data_icon_sizes'] = self::$icon_sizes;

		//Render
		output($this->render('settings/general', $data));
	}

	public function theme()
	{
		if (empty($_GET['theme'])) {
			output('No Theme Requested.');
			return false;
		}

		$image = DIR_SITE . 'app/view/theme/' . $_GET['theme'] . '/' . $_GET['theme'] . '.png';

		$image = image($image);

		if (!$image) {
			$image = image('no_image', 300, 300);
		}

		output("<img src=\"$image\" class =\"theme_preview\" />");
	}

	public function save()
	{
		if ($this->Model_Settings->saveGeneral($_POST)) {
			message('success', _l("The General Settings have been saved!"));
		} else {
			message('error', $this->Model_Settings->getError());
		}

		if ($this->is_ajax) {
			output_json($this->message->fetch());
		} elseif ($this->message->has('error')) {
			post_redirect('admin/settings/general');
		} else {
			redirect('admin/settings');
		}
	}

	public function generate_icons()
	{
		if (!empty($_POST['icon'])) {
			$icon_files = array();

			foreach (self::$icon_sizes as $size) {
				$url = image_save($_POST['icon'], null, $size, $size);

				$icon_files[$size . 'x' . $size] = array(
					'url'     => $url,
					'relpath' => str_replace(URL_IMAGE, '', $url),
				);
			}

			$url = $this->image->ico($_POST['icon']);

			$icon_files['ico'] = array(
				'relpath' => str_replace(URL_IMAGE, '', $url),
				'url'     => $url,
			);

			output(json_encode($icon_files));
		}
	}
}
