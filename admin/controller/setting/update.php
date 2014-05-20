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
	static $can_modify = array(
		'index',
	   //'update',
	);

	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("System Update"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("System Update"), site_url('setting/update'));

		//Actions
		$data['action'] = site_url('setting/update/update');
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

	public function update()
	{
		if (!empty($_REQUEST['version'])) {
			if (!$this->System_Update->updateSystem($_POST['version'])) {
				$this->message->add('error', $this->System_Update->getError());
			} else {
				$this->message->add('success', _l("You have successfully updated to version %s", $_REQUEST['version']));
			}
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} else {
			redirect('setting/update');
		}
	}

	public function auto_update()
	{
		$this->config->save('system', 'auto_update', $_POST['auto_update']);

		if ($_POST['auto_update']) {
			$this->message->add('success', _l("Automatic system updates has been activated!"));
		} else {
			$this->message->add('notify', _l("You have deactivated automatic system updates!"));
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} else {
			redirect('setting/update');
		}
	}
}
