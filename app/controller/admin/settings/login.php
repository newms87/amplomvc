<?php

/**
 * @author  Daniel Newman
 * @date    3/20/2013
 * @package Amplo MVC
 * @link    http://amplomvc.com/
 *
 * Class App_Controller_Admin_Settings_Login
 * Title: Login Settings
 * Icon: login.png
 * Order: 5
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */
class App_Controller_Admin_Settings_Login extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("Login Settings"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Settings"), site_url('admin/settings/setting'));
		breadcrumb(_l("Login"), site_url('admin/settings/login'));

		//Load Data or Defaults
		$settings = $_POST;

		if (!IS_POST) {
			$settings = $this->config->loadGroup('login_settings');

			if (!$settings) {
				$settings = array();
			}
		}

		$defaults = array(
			'status'      => 1,
			'google_plus' => array(
				'active'           => '',
				'api_key'          => '',
				'client_id'        => '',
				'client_secret'    => '',
				'application_name' => '',
			),

			'facebook' => array(
				'active'     => '',
				'app_id'     => '',
				'app_secret' => '',
			),
		);

		$settings += $defaults;

		//Template Data
		$settings['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		$settings['data_active'] = array(
			1 => _l("Active"),
			0 => _l("Inactive"),
		);

		//Actions
		$settings['save'] = site_url('admin/settings/login/save');

		//Render
		output($this->render('settings/login', $settings));
	}

	public function save()
	{
		//No Data
		if (!IS_POST) {
			redirect('admin/settings/login');
		}

		//User Permissions
		if (!user_can('w', 'admin/settings/login')) {
			message('warning', _l("You do not have permission to modify Login Settings."));
			redirect('admin/settings');
		}

		//Validate Settings
		$settings = $_POST;

		//Save Settings
		$this->config->saveGroup('login_settings', $settings, false);

		if (!$this->config->hasError()) {
			message('success', _l("You have successfully updated the Login Settings"));
			redirect('admin/settings');
		}

	}
}
