<?php
class App_Controller_Admin_Dev_Dev extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Development Console"));

		$data['url_sync']            = site_url("dev/dev/sync");
		$data['url_site_management'] = site_url("dev/dev/site_management");
		$data['url_backup_restore']  = site_url("dev/dev/backup_restore");
		$data['url_db_admin']        = site_url("dev/db_admin");

		$data['return'] = site_url('admin');

		output($this->render('dev/dev', $data));
	}

	public function sync()
	{
		$this->document->setTitle(_l("Synchronize Sites"));

		$dev_sites = $this->config->loadGroup('dev_sites');

		if (is_post() && $this->validate()) {
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

		$this->breadcrumb->add(_l("Synchronize Sites"), site_url('dev/dev/sync'));

		$data['request_sync_table'] = site_url('dev/dev/request_sync_table');

		$defaults = array(
			'tables' => '',
			'domain' => '',
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$data[$key] = $_POST[$key];
			} else {
				$data[$key] = $default;
			}
		}

		$data['data_sites'] = $dev_sites;

		$data['data_tables'] = $this->db->getTables();

		$data['return'] = site_url('admin');

		$this->content();

		output($this->render('dev/sync', $data));
	}

	public function site_management()
	{
		$this->document->setTitle(_l("Site Management"));

		$dev_sites = $this->config->loadGroup('dev_sites');

		if (is_post() && $this->validate()) {
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

			$this->config->saveGroup('dev_sites', $dev_sites, null, false);
		}

		$this->breadcrumb->add(_l("Site Management"), site_url('dev/dev/site_management'));

		$defaults = array(
			'domain'   => '',
			'username' => '',
			'status'   => 'live',
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$data[$key] = $_POST[$key];
			} else {
				$data[$key] = $default;
			}
		}

		$data['data_site_status'] = array(
			'live'     => _l("Live Site"),
			'dev'      => _l("Development Site"),
			'inactive' => _l("Inactive Site"),
		);

		$data['dev_sites'] = $dev_sites;

		$data['return'] = site_url('admin');

		$this->content();

		output($this->render('dev/site_management', $data));
	}

	public function backup_restore()
	{
		//Page Head
		$this->document->setTitle(_l("Backup & Restore"));

		//Handle POST
		if (is_post() && $this->validate()) {
			if (isset($_POST['backup_download'])) {
				if (!empty($_POST['backup_file'])) {
					$this->export->downloadFile($_POST['backup_file']);
				} else {
					$this->message->add('warning', _l("Please select a backup file to download."));
				}
			} elseif (isset($_POST['default_installation'])) {
				$this->dev->site_backup(DIR_SYSTEM . 'install/db.sql', $this->getDefaultInstallProfile(), '%__TABLE_PREFIX__%');
			} elseif (isset($_POST['site_backup'])) {
				$tables = isset($_POST['tables']) ? $_POST['tables'] : null;

				if (count($tables) == $this->db->countTables()) {
					$tables = null;
				}

				$this->dev->site_backup(null, $tables);
			} elseif (isset($_POST['site_restore'])) {
				$this->dev->site_restore($_POST['backup_file']);
			} elseif (isset($_POST['sync_file'])) {
				$sync_file = DIR_DOWNLOAD . 'sync_file-' . $this->date->now('m-d-y') . '.sql';
				$tables    = isset($_POST['tables']) ? $_POST['tables'] : null;

				$this->dev->site_backup($sync_file, $tables, '__AC_PREFIX__');

				$this->export->downloadFile($sync_file);
			} elseif (isset($_POST['execute_sync_file'])) {
				if (is_uploaded_file($_FILES['filename']['tmp_name'])) {
					if ($this->dev->site_restore($_FILES['filename']['tmp_name'], true)) {
						$this->message->add('success', "Successfully synchronized your site!");
					} else {
						$this->message->add('warning', "There was a problem while synchronizing from the sync file. ");
						$this->message->add('warning', $this->db->getError());
					}
				}
			} elseif (isset($_POST['execute_file'])) {
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

		$this->breadcrumb->add(_l("Backup & Restore"), site_url('dev/dev/backup_restore'));

		$defaults = array(
			'tables' => '',
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$data[$key] = $_POST[$key];
			} else {
				$data[$key] = $default;
			}
		}

		$backup_files = $this->Model_Dev_Dev->getBackupFiles();

		foreach ($backup_files as &$backup) {
			$backup['display_size'] = $this->tool->bytes2str($backup['size'], 2);
			$backup['display_date'] = $this->date->format($backup['date'], 'd M, Y');
		}
		unset($backup);

		$data['data_backup_files'] = $backup_files;

		$data['data_tables'] = $this->db->getTables();

		$data['return'] = site_url('admin');

		$this->content();

		output($this->render('dev/backup_restore', $data));
	}

	public function content()
	{
		$this->document->addStyle(URL_THEME . 'style/dev.css');

		$this->breadcrumb->add(_l("Home"), site_url('admin'), '', 0);
		$this->breadcrumb->add(_l("Development Console"), site_url('dev/dev'), '', 1);
	}

	public function request_table_data()
	{
		if (is_post() && isset($_POST['tables']) && $this->validate()) {
			$file = DIR_DOWNLOAD . 'tempsql.sql';

			$this->db->dump($file, $_POST['tables']);

			include($file);

			unlink($file);
		} else {
			echo _l("There was a problem while synchronizing from the server.");
		}

		exit;
	}

	private function validate()
	{
		if (!user_can('modify', 'dev/dev')) {
			$this->error['warning'] = _l("Warning: You do not have permission to use the development console!");
		}

		return empty($this->error);
	}

	private function getDefaultInstallProfile()
	{
		$tables = $this->db->getTables();

		//TODO: Setup DB Install Profile (or maybe make this accessible from Admin Panel?

		return $tables;
	}
}
