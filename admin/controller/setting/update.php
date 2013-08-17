<?php
class Admin_Controller_Setting_Update extends Controller
{
	public function index()
	{
		$this->template->load('setting/update');
		$this->language->load('setting/update');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && $this->validate()) {
			if (!empty($_POST['version'])) {
				$this->System_Update->update($_POST['version']);
				$this->message->add('success', $this->_('text_success', $_POST['version']));
			} elseif (isset($_POST['auto_update'])) {
				$this->config->save('system', 'auto_update', $_POST['auto_update']);

				if ($_POST['auto_update']) {
					$this->message->add('success', $this->_('text_auto_update_active'));
				} else {
					$this->message->add('notify', $this->_('text_auto_update_inactive'));
				}
			}

			if (!$this->message->error_set()) {
				$this->url->redirect($this->url->link('setting/update'));
			}
		}

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('setting/update'));

		$this->data['action'] = $this->url->link('setting/update');
		$this->data['cancel'] = $this->url->link('common/home');

		if (!$this->request->isPost()) {
			$update_info = $this->Model_Setting_Setting->getSetting('system');
		}

		$defaults = array(
			'version'     => AC_VERSION,
			'auto_update' => 0,
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($update_info[$key])) {
				$this->data[$key] = $update_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		$version_list = $this->System_Update->getVersions();

		$versions = array();

		foreach ($version_list as $version => $file) {
			$versions[$version] = $this->_('text_version', $version);
		}

		$this->data['data_versions'] = $versions;

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	public function validate()
	{
		if (!$this->user->hasPermission('modify', 'setting/update')) {
			$this->error['permission'] = $this->_('error_permission');
		}

		return $this->error ? false : true;
	}
}
