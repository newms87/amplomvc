<?php
/**
 * Class Admin_Controller_Setting_Login
 * Title: Login Settings
 * Icon: login_settings.png
 * Order: 5
 *
 */
class Admin_Controller_Setting_Login extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Login Settings"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Settings"), site_url('setting/setting'));
		$this->breadcrumb->add(_l("Login"), site_url('setting/login'));

		//Load Data or Defaults
		if (!$this->request->isPost()) {
			$settings = $this->config->loadGroup('login_settings', 0);

			if (!$settings) {
				$settings = array();
			}
		} else {
			$settings = $_POST;
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

		$data += $settings + $defaults;

		//Action Buttons
		$data['save']   = site_url('setting/login/save');
		$data['cancel'] = site_url('setting/store');

		//Render
		$this->response->setOutput($this->render('setting/login', $data));
	}

	public function save()
	{
		//No Data
		if (!$this->request->isPost()) {
			redirect('setting/login');
		}

		//User Permissions
		if (!$this->user->can('modify', 'setting/login')) {
			$this->message->add('warning', _l("You do not have permission to modify Login Settings."));
			redirect('setting/store');
		}

		//Validate Settings
		$settings = $_POST;

		//Save Settings
		$this->config->saveGroup('login_settings', $settings, 0, false);

		if (!$this->config->hasError()) {
			$this->message->add('success', _l("You have successfully updated the Login Settings"));
			redirect('setting/store');
		}

	}
}
