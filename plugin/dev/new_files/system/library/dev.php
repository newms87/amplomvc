<?php

class Dev extends Library
{
	public function site_backup($file = null, $tables = null, $prefix = null, $remove_prefix = false)
	{
		$site_name = option('site_name');

		if (!$file) {
			if (!empty($tables)) {
				$table_string = count($tables) > 3 ? $tables[0] . '+' . $tables[1] . '+' . (count($tables) - 2) . '_more' : implode('+', $tables);
				$file         = DIR_DATABASE_BACKUP . "dump_" . $table_string . '_' . date('Y-m-d-G_i_s') . ".sql";
			} else {
				$file = DIR_DATABASE_BACKUP . "full_backup_" . date('Y-m-d-G_i_s') . ".sql";
			}
		}

		if (!$this->db->dump($file, $tables, $prefix, $remove_prefix)) {
			$this->error['dump']   = $this->db->fetchError();
			$this->error['failed'] = _l("Database backup failed.");
			return false;
		}

		return true;
	}

	public function site_restore($file, $sync_file = false)
	{
		$site_name = option('site_name');

		if (!is_file($file)) {
			$this->error['file'] = "Failed to restore $site_name from $file. The File was not found.";
			return false;
		}

		if ($sync_file) {
			$contents = preg_replace("/__AC_PREFIX__/", DB_PREFIX, file_get_contents($file));
			file_put_contents($file, $contents);
		}

		if (!$this->db->executeFile($file)) {
			$this->error['restore'] = "There was a problem while restoring $site_name!";
			return false;
		}

		return true;
	}

	public function login_external_server($domain, $username, $password)
	{
		$request = 'username=' . $username;
		$request .= '&password=' . $password;

		$curl = curl_init($domain . '/admin/user/login?response=1');

		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_COOKIEJAR, 'token');
		curl_setopt($curl, CURLOPT_COOKIEFILE, 'token');
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		$response = curl_exec($curl);

		if ($response === 'FAILURE') {
			$this->error['login'] = "Login Failed for $domain!";
		} elseif ($response !== 'SUCCESS') {
			$this->error['response'] = "There was an error while connecting to the server at $domain.";
			return false;
		}

		return true;
	}

	public function request_table_sync($conn_info, $tables)
	{
		if (!$this->login_external_server($conn_info['domain'], $conn_info['username'], $conn_info['password'])) {
			return false;
		}

		$curl = curl_init($conn_info['domain'] . '/admin/dev/request_table_data');

		$request = http_build_query(array('tables' => $tables));

		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_COOKIEJAR, 'token');
		curl_setopt($curl, CURLOPT_COOKIEFILE, 'token');
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		$response = curl_exec($curl);

		if (!$response) {
			trigger_error('Dev::request_table_sync(): Curl Failed -  ' . curl_error($curl) . '(' . curl_errno($curl) . ')');
		} else {
			if (preg_match("/^ERROR/i", $response) || preg_match("/^WARNING/i", $response) || preg_match("/^NOTICE/i", $response)) {
				$this->error['response'] = "There was an error returned from the server: $response";
				return false;
			}

			//First always backup the Database before making changes
			$this->site_backup(null, $tables);

			//Save the database sync file in a temp file
			$file = DIR_DOWNLOAD . 'tempdbs.txt';

			file_put_contents($file, $response);

			//Execute the database sync file
			if ($this->db->executeFile($file)) {
				message('success', "Successfully synchronized the requested tables from $conn_info[domain]!");

				return true;
			} else {
				message('warning', $this->fetchError());
			}
		}

		message('warning', "There was a problem while synchronizing the requested tables from $conn_info[domain].");

		return false;
	}

	public function getBackupFiles()
	{
		$exts = array(
			'txt',
			'sql'
		);

		$file_list = get_files(DIR_DATABASE_BACKUP, $exts, FILELIST_STRING);

		$files = array();

		foreach ($file_list as $file) {
			$files[] = array(
				'name' => basename($file),
				'date' => filemtime($file),
				'path' => str_replace('\\', '/', $file),
				'size' => (int)filesize($file),
			);
		}

		usort($files, function ($a, $b) {
			return $a['date'] > $b['date'];
		});

		return $files;
	}

	public function performance()
	{
		global $__start, $profile;

		$file = $this->theme->getFile('common/amplo_profile', AMPLO_DEFAULT_THEME);

		if ($file) {
			if (DB_PROFILE) {
				$db_profile = $this->db->getProfile();

				$db_time = 0;

				usort($db_profile, function ($a, $b) {
					return $a['time'] < $b['time'];
				});

				foreach ($db_profile as $p) {
					$db_time += $p['time'];
				}

				$db_time = round($db_time, 6) . ' seconds';
			}

			$run_time = round(microtime(true) - $__start, 6);

			$mb          = 1024 * 1024;
			$memory      = round(memory_get_peak_usage() / $mb, 2) . " MB";
			$real_memory = round(memory_get_peak_usage(true) / $mb, 2) . " MB";

			$file_list       = get_included_files();
			$total_file_size = 0;

			foreach ($file_list as &$f) {
				$total_file_size += filesize($f);

				$f = array(
					'name' => $f,
					'size' => filesize($f),
				);
			}
			unset($f);

			$total_file_size = round($total_file_size / 1024, 2) . ' KB';

			sort_by($file_list, 'size');

			foreach ($file_list as &$f) {
				$f['size'] = round($f['size'] / 1024, 2) . " KB";
			}
			unset($f);

			//Cached Files
			$cache_files      = $this->cache->getLoadedFiles();
			$total_cache_size = 0;

			foreach ($cache_files as $ckey => &$c) {
				if (!is_file($c['file'])) {
					$c['size'] = 0;
				} else {
					$fsize = filesize($c['file']);
					$total_cache_size += $fsize;
					$c['size'] = $fsize;
				}
			}
			unset($c);

			sort_by($cache_files, 'size');

			foreach ($cache_files as &$c) {
				$c['size'] = $c['size'] ? round($c['size'] / 1024, 2) . " KB" : "{DELETED}";
				if (!empty($c['data'])) {
					$this->escapeHtmlR($c['data']);
				}
			}
			unset($c);

			$total_cache_size = round($total_cache_size / 1024, 2) . ' KB';

			ob_start();
			include(_mod($file));
			$html = ob_get_clean();

			$output = $this->response->getOutput();
			$output = str_replace("</body>", $html . "</body>", $output);
			output($output);
		}
	}

	public function escapeHtmlR(&$array)
	{
		if (!is_array($array)) {
			return $array = is_string($array) ? htmlentities($array) : $array;
		}

		foreach ($array as &$a) {
			$this->escapeHtmlR($a);
		}
		unset($a);
	}
}
