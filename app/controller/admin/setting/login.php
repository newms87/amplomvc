<?php
/**
 * Class App_Controller_Admin_Setting_Login
 * Title: Login Settings
 * Icon: login.png
 * Order: 5
 *
 */
class App_Controller_Admin_Setting_Login extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Login Settings"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("Settings"), site_url('admin/setting/setting'));
		$this->breadcrumb->add(_l("Login"), site_url('admin/setting/login'));

		//Load Data or Defaults
		$settings = $_POST;

		if (!$this->request->isPost()) {
			$settings = $this->config->loadGroup('login_settings', 0);

			if (!$settings) {
				$settings = array();
			}
		}

		$defaults = array(
			'google_plus' => array(
				'api_key'          => '',
				'client_id'        => '',
				'client_secret'    => '',
				'application_name' => '',
			),

			'facebook'    => array(
				'app_id'     => '',
				'app_secret' => '',
			),
		);

		$settings += $defaults;

		//Actions
		$settings['save']   = site_url('admin/setting/login/save');

		//Render
		$this->response->setOutput($this->render('setting/login', $settings));
	}

	public function save()
	{
		//No Data
		if (!$this->request->isPost()) {
			redirect('admin/setting/login');
		}

		//User Permissions
		if (!user_can('modify', 'setting/login')) {
			$this->message->add('warning', _l("You do not have permission to modify Login Settings."));
			redirect('admin/setting/store');
		}

		//Validate Settings
		$settings = $_POST;

		//Save Settings
		$this->config->saveGroup('login_settings', $settings, 0, false);

		if (!$this->config->hasError()) {
			$this->message->add('success', _l("You have successfully updated the Login Settings"));
			redirect('admin/setting/store');
		}

	}
}
