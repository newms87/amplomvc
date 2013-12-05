<?php
class Admin_Controller_Dev_Dev extends Controller
{
	public function index()
	{
		$this->template->load('dev/dev');
		$this->language->load('dev/dev');

		$this->document->setTitle($this->_('head_title'));

		$this->data['url_sync']            = $this->url->link("dev/dev/sync");
		$this->data['url_site_management'] = $this->url->link("dev/dev/site_management");
		$this->data['url_backup_restore']  = $this->url->link("dev/dev/backup_restore");
		$this->data['url_db_admin']        = $this->url->link("dev/db_admin");

		$this->content();
	}

	public function sync()
	{
		$this->template->load('dev/sync');

		$this->language->load('dev/dev');

		$this->document->setTitle($this->_('text_sync'));

		$dev_sites = $this->System_Model_Setting->getSetting('dev_sites');

		if ($this->request->isPost() && $this->validate()) {
			if (isset($_POST['sync_site'])) {
				if (!isset($_POST['tables'])) {
					$this->message->add('warning', "You must select at least 1 table to sync.");
				} else {
					$key = array_search($_POST['domain'], $dev_sites);
					foreach ($dev_sites as $site) {
						if ($_POST['domain'] == $site['domain']) {
							$dev_sites[$key]['password'] = $_POST['password'];

							$this->dev->request_table_sync($dev_sites[$key], $_POST['tables']);

							break;
						}
					}
				}
			}
		}

		$this->breadcrumb->add($this->_('text_sync'), $this->url->link('dev/dev/sync'));

		$this->data['request_sync_table'] = $this->url->link('dev/dev/request_sync_table');

		$defaults = array(
			'tables' => '',
			'domain' => '',
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		$this->data['data_sites'] = $dev_sites;

		$this->data['data_tables'] = $this->db->getTables();

		$this->content();
	}

	public function site_management()
	{
		$this->template->load('dev/site_management');

		$this->language->load('dev/dev');

		$this->document->setTitle($this->_('text_site_management'));

		$dev_sites = $this->System_Model_Setting->getSetting('dev_sites');

		if ($this->request->isPost() && $this->validate()) {
			if (isset($_POST['add_site'])) {
				unset($_POST['add_site']);
				$dev_sites[] = $_POST;
			} elseif (isset($_POST['delete_site'])) {
				foreach ($dev_sites as $key => $site) {
					if ($_POST['domain'] == $site['domain']) {
						unset($dev_sites[$key]);
					}
				}
			}

			unset($_POST);

			$this->System_Model_Setting->editSetting('dev_sites', $dev_sites, null, false);
		}

		$this->breadcrumb->add($this->_('text_site_management'), $this->url->link('dev/dev/site_management'));

		$defaults = array(
			'domain'   => '',
			'username' => '',
			'status'   => 'live',
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		$this->data['dev_sites'] = $dev_sites;

		$this->content();
	}

	public function backup_restore()
	{
		//Template and Language
		$this->template->load('dev/backup_restore');
		$this->language->load('dev/dev');

		//Page Head
		$this->document->setTitle($this->_('text_backup_restore'));

		//Handle POST
		if ($this->request->isPost() && $this->validate()) {
			if (isset($_POST['backup_download'])) {
				if (!empty($_POST['backup_file'])) {
					$this->export->downloadFile($_POST['backup_file']);
				} else {
					$this->message->add('warning', $this->_('error_download_backup_file'));
				}
			}
			elseif (isset($_POST['default_installation'])) {
				$this->dev->site_backup(DIR_SYSTEM . 'install/db.sql', $this->getDefaultInstallProfile(), '%__TABLE_PREFIX__%');
			}
			elseif (isset($_POST['site_backup'])) {
				$tables = isset($_POST['tables']) ? $_POST['tables'] : null;

				if (count($tables) == $this->db->countTables()) {
					$tables = null;
				}

				$this->dev->site_backup(null, $tables);
			}
			elseif (isset($_POST['site_restore'])) {
				$this->dev->site_restore($_POST['backup_file']);
			}
			elseif (isset($_POST['sync_file'])) {
				$sync_file = DIR_DOWNLOAD . 'sync_file-' . $this->date->now(AC_DATE_STRING, 'm-d-y') . '.sql';
				$tables = isset($_POST['tables']) ? $_POST['tables'] : null;

				$this->dev->site_backup($sync_file, $tables, '__AC_PREFIX__');

				$this->export->downloadFile($sync_file);
			}
			elseif (isset($_POST['execute_sync_file'])) {
				if (is_uploaded_file($_FILES['filename']['tmp_name'])) {
					if ($this->dev->site_restore($_FILES['filename']['tmp_name'], true)) {
						$this->message->add('success', "Successfully synchronized your site!");
					} else {
						$this->message->add('warning', "There was a problem while synchronizing from the sync file. ");
						$this->message->add('warning', $this->db->getError());
					}
				}
			}
			elseif (isset($_POST['execute_file'])) {
				if (is_uploaded_file($_FILES['filename']['tmp_name'])) {
					$filename = $_FILES['filename']['name'];
					if ($this->db->executeFile($_FILES['filename']['tmp_name'])) {
						$this->message->add('success', _l("Successfully executed the contents of $filename!"));
					} else {
						$this->message->add('warning', _l("There was a problem while executing $filename. "));
						$this->message->add('warning', $this->db->getError());
					}
				}
			}
		}

		$this->breadcrumb->add($this->_('text_backup_restore'), $this->url->link('dev/dev/backup_restore'));

		$defaults = array(
			'tables' => '',
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		$backup_files = $this->Model_Dev_Dev->getBackupFiles();

		foreach ($backup_files as &$backup) {
			$backup['display_size'] = $this->tool->bytes2str($backup['size'], 2);
			$backup['display_date'] = $this->date->format($backup['date'], 'd M, Y');
		}

		$this->data['data_backup_files'] = $backup_files;

		$this->data['data_tables'] = $this->db->getTables();

		$this->content();
	}

	public function content()
	{
		$this->document->addStyle(HTTP_THEME_STYLE . 'dev.css');

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'), '', 0);
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('dev/dev'), '', 1);

		$this->data['return'] = $this->url->link('common/home');


		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	public function request_table_data()
	{
		$this->language->load('dev/dev');

		if ($this->request->isPost() && isset($_POST['tables']) && $this->validate()) {
			$file = DIR_DOWNLOAD . 'tempsql.sql';

			$this->db->dump($file, $_POST['tables']);

			include($file);

			unlink($file);
		} else {
			echo $this->_('error_sync_table');
		}

		exit;
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'dev/dev')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		return $this->error ? false : true;
	}

	private function getDefaultInstallProfile()
	{
		$tables = $this->db->getTables();

		//TODO: Setup DB Install Profile (or maybe make this accessible from Admin Panel?

		return $tables;
	}
}
