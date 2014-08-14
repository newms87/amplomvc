<?php
class Dev extends Library
{
	public function site_backup($file = null, $tables = null, $prefix = null)
	{
		$site_name = option('config_name');

		if (!$file) {
			if (!empty($tables)) {
				$table_string = count($tables) > 3 ? $tables[0] . '+' . $tables[1] . '+' . (count($tables) - 2) . '_more' : implode('+', $tables);
				$file         = DIR_DATABASE_BACKUP . "dump_" . $table_string . '_' . date('Y-m-d-G_i_s') . ".sql";
			} else {
				$file = DIR_DATABASE_BACKUP . "full_backup_" . date('Y-m-d-G_i_s') . ".sql";
			}
		}

		if ($this->db->dump($file, $tables, $prefix)) {
			message('success', "Successfully backed up $site_name!");

			return true;
		} else {
			message('warning', "There was a problem while backing up $site_name!");
		}

		return false;
	}

	public function site_restore($file, $sync_file = false)
	{
		$site_name = option('config_name');

		if (!is_file($file)) {
			message('warning', "Failed to restore $site_name from $file. The File was not found.");
			return false;
		}

		if ($sync_file) {
			$contents = preg_replace("/__AC_PREFIX__/", DB_PREFIX, file_get_contents($file));
			file_put_contents($file, $contents);
		}

		if ($this->db->executeFile($file)) {
			message('success', "Successfully restored $site_name from backup file $file!");
			return true;
		} else {
			message('warning', "There was a problem while restoring $site_name!");
		}

		return false;
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

		if ($response == 'SUCCESS') {
			return true;
		} elseif ($response == 'FAILURE') {
			message('warning', "Login Failed for $domain!");
		} else {
			message('warning', "There was an error while connecting to the server at $domain.");
		}

		return false;
	}

	public function request_table_sync($conn_info, $tables)
	{
		if (!$this->login_external_server($conn_info['domain'], $conn_info['username'], $conn_info['password'])) {
			return false;
		}

		$curl = curl_init($conn_info['domain'] . '/admin/dev/dev/request_table_data');

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
				message('warning', "There was an error returned from the server: $response");

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
				message('warning', $this->getError());
			}
		}

		message('warning', "There was a problem while synchronizing the requested tables from $conn_info[domain].");

		return false;
	}
}
