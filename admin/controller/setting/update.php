<?php
/**
 * Class Admin_Controller_Setting_Update
 *
 * Title: System Update
 * Icon: system_update.png
 * Order: 1
 *
 */
class Admin_Controller_Setting_Update extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("System Update"));

		//Handle POST
		if ($this->request->isPost() && $this->validate()) {
			if (!empty($_POST['version'])) {
				$this->System_Update->updateSystem($_POST['version']);
				$this->message->add('success', _l("You have successfully updated to version %s", $_POST['version']));
			} elseif (isset($_POST['auto_update'])) {
				$this->config->save('system', 'auto_update', $_POST['auto_update']);

				if ($_POST['auto_update']) {
					$this->message->add('success', _l("Automatic system updates has been activated!"));
				} else {
					$this->message->add('notify', _l("You have deactivated automatic system updates!"));
				}
			}

			if (!$this->message->has('error', 'warning')) {
				redirect('setting/update');
			}
		}

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("System Update"), site_url('setting/update'));

		//Actions
		$data['action'] = site_url('setting/update');
		$data['cancel'] = site_url('setting/store');

		//Data
		$update_info = array();

		if (!$this->request->isPost()) {
			$update_info = $this->config->loadGroup('system');
		}

		$defaults = array(
			'version'     => AC_VERSION,
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
		$this->response->setOutput($this->render('setting/update', $data));
	}

	public function validate()
	{
		if (!$this->user->can('modify', 'setting/update')) {
			$this->error['permission'] = _l("You do not have permission to run the System Update");
		}

		return empty($this->error);
	}
}
