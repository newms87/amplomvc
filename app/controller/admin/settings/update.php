<?php

/**
 * Class App_Controller_Admin_Settings_Update
 *
 * Title: System Update
 * Icon: system-update.png
 * Order: 1
 *
 */
class App_Controller_Admin_Settings_Update extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("System Update"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("System Update"), site_url('admin/settings/update'));

		//Actions
		$data['action'] = site_url('admin/settings/update/update');
		$data['cancel'] = site_url('admin/settings');

		//Data
		$update_info = array();

		if (!IS_POST) {
			$update_info = $this->config->loadGroup('system');
		}

		$defaults = array(
			'version'     => AMPLO_VERSION,
			'auto_update' => 0,
		);

		$data += $update_info + $defaults;

		//Template Data
		$version_list = $this->System_Update->getVersions();

		$versions = array();

		foreach ($version_list as $version => $file) {
			$versions[$version] = _l("AmploCart Version %s", $version);
		}

		$data['data_versions'] = $versions;

		//Render
		output($this->render('settings/update', $data));
	}

	public function update()
	{
		if (!empty($_REQUEST['version'])) {
			if (!$this->System_Update->updateSystem($_POST['version'])) {
				message('error', $this->System_Update->getError());
			} else {
				message('success', _l("You have successfully updated to version %s", $_REQUEST['version']));
			}
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/settings/update');
		}
	}

	public function auto_update()
	{
		$this->config->save('system', 'auto_update', $_POST['auto_update']);

		if ($_POST['auto_update']) {
			message('success', _l("Automatic system updates has been activated!"));
		} else {
			message('notify', _l("You have deactivated automatic system updates!"));
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/settings/update');
		}
	}
}
