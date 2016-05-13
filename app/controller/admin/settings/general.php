<?php

/**
 * @author  Daniel Newman
 * @date    3/20/2013
 * @package Amplo MVC
 * @link    http://amplomvc.com/
 *
 * Title: General Settings
 * Icon: admin.png
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
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
		set_page_info('title', _l("General Settings"));

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
		$data = array(
			'layouts'    => $this->Model_Layout->getRecords(null, null, array('cache' => true)),
			'themes'     => $this->theme->getThemes(),
			'countries'  => $this->Model_Localisation_Country->getCountries(),
			'languages'  => $this->Model_Localisation_Language->getRecords(null, null, array('cache' => true)),
			'currencies' => $this->Model_Localisation_Currency->getCurrencies(),
			'user_roles' => $this->Model_UserRole->getRecords(null, null, array('cache' => true)),
			'pages'      => array('' => _l(" --- None --- ")) + $this->Model_Page->getRecords(null, null, array('cache' => true)),

			'mail_protocols' => array(
				'smtp' => "SMTP",
				'mail' => "PHP Mail",
			),
		);

		//Website Icon Sizes
		if (!is_array($settings['site_icon'])) {
			$settings['site_icon'] = array();
		}

		$settings['site_icon'] += array(
			'orig' => '',
			'ico'  => '',
		);

		foreach (self::$icon_sizes as $size) {
			$key = $size . 'x' . $size;

			if (!isset($settings['site_icon'][$key])) {
				$settings['site_icon'][$key] = '';
			}
		}

		foreach ($settings['site_icon'] as &$icon) {
			$icon = array(
				'thumb' => $this->image->get($icon),
				'src'   => $icon,
			);
		}
		unset($icon);

		$data['icon_sizes'] = self::$icon_sizes;

		$settings['data'] = $data;

		//Domains AC Template
		$settings['ga_domains']['__ac_template__'] = '';

		//Render
		output($this->render('settings/general', $settings));
	}

	public function theme()
	{
		$theme = _get('theme');

		if ($theme) {
			$image = image(DIR_SITE . 'app/view/theme/' . $theme . '/preview.png');

			if (!$image) {
				$image = image(theme_dir('no-preview.png'), 300, 300);
			}

			output(img($image, array('#class' => 'theme-preview')));
		}
	}

	public function save()
	{
		if ($this->Model_Settings->saveGeneral($_POST)) {
			message('success', _l("The General Settings have been saved!"));
		} else {
			message('error', $this->Model_Settings->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
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
