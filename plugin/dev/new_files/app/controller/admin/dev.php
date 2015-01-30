<?php

class App_Controller_Admin_Dev extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("Development Console"));

		$data['url_sync']            = site_url("admin/dev/sync");
		$data['url_site_management'] = site_url("admin/dev/site_management");
		$data['url_backup_restore']  = site_url("admin/dev/backup_restore");
		$data['url_db_admin']        = site_url("admin/dev/db_admin");

		$data['return'] = site_url('admin');

		output($this->render('dev/dev', $data));
	}

	public function sync()
	{
		set_page_info('title', _l("Synchronize Sites"));

		$dev_sites = $this->config->loadGroup('dev_sites');

		if (IS_POST && $this->validate()) {
			if (isset($_POST['sync_site'])) {
				if (!isset($_POST['tables'])) {
					message('warning', "You must select at least 1 table to sync.");
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

		breadcrumb(_l("Synchronize Sites"), site_url('admin/dev/sync'));

		$data['request_sync_table'] = site_url('admin/dev/request-sync-table');

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
		set_page_info('title', _l("Site Management"));

		$dev_sites = $this->config->loadGroup('dev_sites');

		if (IS_POST && $this->validate()) {
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

			$this->config->saveGroup('dev_sites', $dev_sites, false);
		}

		breadcrumb(_l("Site Management"), site_url('admin/dev/site-management'));

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
		set_page_info('title', _l("Backup & Restore"));

		//Handle POST
		if (IS_POST && $this->validate()) {
			if (isset($_POST['backup_download'])) {
				if (!empty($_POST['backup_file'])) {
					$this->csv->downloadFile($_POST['backup_file']);
				} else {
					message('warning', _l("Please select a backup file to download."));
				}
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

				$this->csv->downloadFile($sync_file);
			} elseif (isset($_POST['execute_sync_file'])) {
				if (is_uploaded_file($_FILES['filename']['tmp_name'])) {
					if ($this->dev->site_restore($_FILES['filename']['tmp_name'], true)) {
						message('success', "Successfully synchronized your site!");
					} else {
						message('warning', "There was a problem while synchronizing from the sync file. ");
						message('warning', $this->db->getError());
					}
				}
			} elseif (isset($_POST['execute_file'])) {
				if (is_uploaded_file($_FILES['filename']['tmp_name'])) {
					$filename = $_FILES['filename']['name'];
					if ($this->db->executeFile($_FILES['filename']['tmp_name'])) {
						message('success', _l("Successfully executed the contents of $filename!"));
					} else {
						message('warning', _l("There was a problem while executing $filename. "));
						message('warning', $this->db->getError());
					}
				}
			}
		}

		breadcrumb(_l("Backup & Restore"), site_url('admin/dev/backup-restore'));

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

		$data['data_backup_files'] = $this->dev->getBackupFiles();
		$data['data_tables']       = $this->db->getTables();

		$this->content();

		output($this->render('dev/backup_restore', $data));
	}

	public function content()
	{
		$this->document->addStyle(theme_url('style/dev.css'));

		breadcrumb(_l("Home"), site_url('admin'), '', 0);
		breadcrumb(_l("Development Console"), site_url('admin/dev'), '', 1);
	}

	public function request_table_data()
	{
		if (IS_POST && isset($_POST['tables']) && $this->validate()) {
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
		if (!user_can('w', 'admin/dev')) {
			$this->error['warning'] = _l("Warning: You do not have permission to use the development console!");
		}

		return empty($this->error);
	}

	public function default_install()
	{
		if ($this->dev->site_backup(DIR_SYSTEM . 'install/db.sql', $this->getDefaultInstallProfile(), DB_PREFIX, true)) {
			message('success', _l("Default Installation has been updated"));
		} else {
			message('error', $this->dev->getError());
		}

		redirect("admin/dev/backup_restore");
	}

	private function getDefaultInstallProfile()
	{
		$tables = $this->db->getTables();

		//TODO: Setup DB Install Profile (or maybe make this accessible from Admin Panel?

		return $tables;
	}

	public function db_admin()
	{
		if (!user_can('w', 'admin/dev')) {
			message('warning', _l("You do not have permission use the Database Administration Console"));
			redirect();
		}

		//Page Head
		set_page_info('title', _l("Database Administration"));
		$this->document->addStyle(theme_url('style/dev.css'));

		breadcrumb(_l("Home"), site_url('admin'), '', 0);
		breadcrumb(_l("Development Console"), site_url('admin/dev'), '', 1);
		breadcrumb(_l("Database Administration"), site_url('dev/db-admin'));

		$data = array();

		//Check for post data
		if (IS_POST) {
			if (!empty($_POST['query'])) {
				$results = $this->db->queryRows(html_entity_decode($_POST['query'], ENT_QUOTES, 'UTF-8'));

				$data['results'] = $results;
			}
		}

		$defaults = array(
			'query' => '',
		);

		$data += $_POST + $defaults;

		$data['data_tables'] = $this->db->getTables();

		$data['return'] = site_url('admin');

		//Render
		output($this->render('dev/db_admin', $data));
	}
}
