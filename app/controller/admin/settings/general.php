<?php

/**
 * Title: General Settings
 * Icon: admin.png
 *
 */
class App_Controller_Admin_Settings_General extends Controller
{
	static $icon_sizes = array(
		152,
		120,
		76,
	);

	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("General Settings"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Settings"), site_url('admin/settings'));
		breadcrumb(_l("General Settings"), site_url('admin/settings/general'));

		//Load Information
		$settings = $_POST;

		if (!IS_POST) {
			$settings = $this->config->loadGroup('config');
		}

		$settings += App_Model_Settings::$general_settings;

		//Template Data
		$settings['data_layouts']         = $this->Model_Design_Layout->getLayouts();
		$settings['data_themes']          = $this->theme->getThemes();
		$settings['data_countries']       = $this->Model_Localisation_Country->getCountries();
		$settings['data_languages']       = $this->Model_Localisation_Language->getLanguages();
		$settings['data_currencies']      = $this->Model_Localisation_Currency->getCurrencies();
		$settings['data_customer_groups'] = $this->Model_Customer->getCustomerGroups();
		$settings['data_pages']           = array('' => _l(" --- None --- ")) + $this->Model_Page->getPages();

		$settings['data_mail_protocols'] = array(
			'smtp' => "SMTP",
			'mail' => _l("PHP Mail"),
		);

		$settings['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		$settings['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		//Website Icon Sizes
		if (!is_array($settings['config_icon'])) {
			$settings['config_icon'] = array(
				'orig' => '',
				'ico'  => '',
			);
		}

		foreach (self::$icon_sizes as $size) {
			$key = $size . 'x' . $size;

			if (!isset($settings['config_icon'][$key])) {
				$settings['config_icon'][$key] = '';
			}
		}

		foreach ($settings['config_icon'] as &$icon) {
			$icon = array(
				'thumb' => $this->image->get($icon),
				'src'   => $icon,
			);
		}
		unset($icon);

		$settings['data_icon_sizes'] = self::$icon_sizes;

		//Domains AC Template
		$settings['config_ga_domains']['__ac_template__'] = '';

		//Render
		output($this->render('settings/general', $settings));
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
