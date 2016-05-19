<?php

/**
 * @author  Daniel Newman
 * @date    3/20/2013
 * @package Amplo MVC
 * @link    http://amplomvc.com/
 *
 * Title: Admin Settings
 * Icon: admin.png
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */
class App_Controller_Admin_Settings_Admin extends Controller
{
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
		$settings['data_languages'] = $this->Model_Localisation_Language->getRecords(null, null, array('cache' => true));

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

		foreach (App_Model_Settings::$icon_sizes as $size) {
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

		$settings['data_icon_sizes'] = App_Model_Settings::$icon_sizes;

		//Render
		output($this->render('settings/admin', $settings));
	}

	public function save()
	{
		if ($this->Model_Settings->saveAdmin($_POST)) {
			message('success', _l("The Admin Settings have been saved!"));
		} else {
			message('error', $this->Model_Settings->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} elseif ($this->message->has('error')) {
			post_redirect('admin/settings/admin');
		} else {
			redirect('admin/settings');
		}
	}
}
