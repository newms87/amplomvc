<?php

/**
 * @author  Daniel Newman
 * @date    3/20/2013
 * @package Amplo MVC
 * @link    http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */
class App_Model_File extends App_Model_Table
{
	protected $table = 'file', $primary_key = 'file_id';

	public function save($file_id, $file)
	{
		if (isset($file['path']) && !is_file($file['path'])) {
			$this->error['path'] = _l("File path not found");
		}

		if ($this->error) {
			return false;
		}

		if (!$file_id) {
			$file_id = $this->findRecord(array('path' => $file['path']));
		}

		$file['size'] = filesize($file['path']);

		$file += array(
			'user_id'     => user_info('user_id'),
			'customer_id' => customer_info('customer_id'),
		);

		$file['date_modified'] = $this->date->format(filemtime($file['path']));
		$file['date_updated']  = $this->date->now();

		if (!$file_id) {
			$file['date_added'] = $this->date->now();
		}

		return parent::save($file_id, $file);
	}

	public function remove($file_id)
	{
		$file = $this->getRecord($file_id);

		if ($file) {
			if ($file['customer_id'] == customer_info('customer_id') || user_can('w', 'admin/file')) {
				if (file_exists($file['path'])) {
					unlink($file['path']);
				}

				return parent::remove($file_id);
			} else {
				$this->error['permission'] = _l("You do not have permission to remove this file");
			}
		}

		return false;
	}

	public function getRecords($sort = array(), $filter = array(), $options = array(), $total = false)
	{
		$results = parent::getRecords($sort, $filter, $options, $total);

		$total ? $records = &$results[0] : $records = &$results;

		$customer_id = (int)customer_info('customer_id');
		$user_id     = (int)user_info('user_id');

		foreach ($records as &$record) {
			$record['user_id']     = empty($record['user_id']) ? false : $record['user_id'];
			$record['customer_id'] = empty($record['customer_id']) ? false : $record['customer_id'];

			$record['is_owner'] = $customer_id === $record['customer_id'] || $user_id === $record['user_id'];
		}
		unset($record);

		return $results;
	}

	public function createFolder($name, $folder = array())
	{
		$folder['name']          = $name;
		$folder['type']          = 'folder';
		$folder['mime_type']     = '';
		$folder['date_added']    = $this->date->now();
		$folder['date_modified'] = $this->date->now();
		$folder['date_updated']  = $this->date->now();
		$folder['size']          = 0;

		$folder += array(
			'user_id'     => user_info('user_id'),
			'customer_id' => customer_info('customer_id'),
			'title'       => $name,
		);

		return parent::save(null, $folder);
	}

	public function folderExists($name, $filter = array())
	{
		$filter['name'] = $name;
		$filter['type'] = 'folder';

		if (!isset($filter['user_id']) && !isset($filter['customer_id'])) {
			if (IS_ADMIN) {
				$filter['user_id'] = user_info('user_id');
			} else {
				$filter['customer_id'] = customer_info('customer_id');
			}
		}

		return parent::findRecord($filter);
	}

	public function upload($file, $options = array())
	{
		$options += array(
			'title'     => '',
			'name'      => '',
			'category'  => '',
			'accept'    => '',
			'path'      => null,
			'folder_id' => null,
		);

		if (!empty($options['name'])) {
			$file['name'] = $options['name'];
		}

		if (empty($file['name'])) {
			$this->error['name'] = _l("File name is required.");
		}

		if (!empty($file['error'])) {
			$this->error['file'] = _l("File upload failed for %s. Please try again.", $file['name']);
		}

		$mime_types = array(
			'image/png'  => 'png',
			'image/gif'  => 'gif',
			'image/jpeg' => 'jpg',
			'image/jpg'  => 'jpg',
		);

		if ($options['accept'] && is_string($options['accept'])) {
			$options['accept'] = explode(',', $options['accept']);
		}

		if ($options['accept']) {
			foreach ($options['accept'] as &$accept) {
				if ($accept === 'jpeg') {
					$accept = 'jpg';
				}

				$accept = preg_replace('/^\\./', '', $accept);
			}
			unset($accept);

			$accept_type = isset($mime_types[$file['type']]) ? $mime_types[$file['type']] : 'other';

			if (!in_array($accept_type, $options['accept'])) {
				$this->error['mime_type'] = _l("File mime type %s not allowed.", $file['type']);
			}
		}

		if ($this->error) {
			return false;
		}

		if (!isset($options['path'])) {
			if ($customer_id = customer_info('customer_id')) {
				$options['path'] = 'customer/' . $customer_id . '/';
			} else {
				$options['path'] .= 'upload/' . date('Y/m/d') . '/';
			}
		}

		//Resolve Type
		if (empty($options['type'])) {
			$type = 'other';

			$mimes = explode('/', $file['type']);

			if ($mimes) {
				if ($mimes[0] === 'image') {
					$type = 'image';
				} elseif (isset($mime[1])) {
					$type = $mime[1];
				}
			}
		} else {
			$type = $options['type'];
		}

		$path = ltrim(rtrim($options['path'], '/') . '/' . $file['name'], '/');

		if (!_is_writable(dirname(DIR_DOWNLOAD . $path)) || !move_uploaded_file($file['tmp_name'], DIR_DOWNLOAD . $path)) {
			$this->error['dir'] = _l("There was a problem saving your file %s to the server. Please upload again.", $file['name']);

			return false;
		}

		$data = array(
			'name'      => $file['name'],
			'category'  => $options['category'],
			'folder_id' => $options['folder_id'],
			'path'      => DIR_DOWNLOAD . $path,
			'url'       => URL_DOWNLOAD . $path,
			'type'      => $type,
			'mime_type' => $file['type'],
			'title'     => $options['title'] ? $options['title'] : pathinfo($path, PATHINFO_FILENAME),
		);

		return $this->save(null, $data);
	}

	public function zip($files, $options = array())
	{
		$options += array(
			'title'     => '',
			'name'      => 'archive',
			'category'  => '',
			'path'      => null,
			'folder_id' => null,
		);

		if (!isset($options['path'])) {
			if ($customer_id = customer_info('customer_id')) {
				$options['path'] = 'customer/' . $customer_id . '/';
			} else {
				$options['path'] .= 'upload/' . date('Y/m/d') . '/';
			}
		}

		$path = ltrim(rtrim($options['path'], '/') . '/' . preg_replace("/\\.zip$/", '', sanitize_filename($options['name'])), '/') . '.zip';

		if (!_is_writable(dirname(DIR_SITE . 'system/temp/' . $path))) {
			$this->error['temp_dir'] = _l("The system temp directory was not writable");
		} elseif (!_is_writable(dirname(DIR_DOWNLOAD . $path))) {
			$this->error['dir'] = _l("The directory %s was not writable. Please change the permissions for this directory in the download path.", dirname($path));
		}

		if ($this->error) {
			return false;
		}

		$zip = new ZipArchive;

		//Write to temp directory in case download directory is on external server
		touch(DIR_SITE . 'system/temp/' . $path);

		$code = $zip->open(DIR_SITE . 'system/temp/' . $path, ZipArchive::OVERWRITE);

		if ($code !== true) {
			$this->error['open'] = _l("Zip Error (%s): There was a problem creating the zip archive. Please try again.", $code);

			return false;
		}

		foreach ($files as $f => $url) {
			$file = str_replace(URL_DOWNLOAD, DIR_DOWNLOAD, $url);

			if (is_file($file)) {
				$entry_name = slug(basename($file), '-');

				if (!$zip->addFromString($entry_name, file_get_contents($file))) {
					if (!$zip->addFile($file, $entry_name)) {
						$this->error['file-' . $f] = _l("Failed to add %s to archive", $url);
					}
				}
			}
		}

		$zip->close();

		$zip_contents = file_get_contents(DIR_SITE . 'system/temp/' . $path);

		if (!strlen($zip_contents)) {
			$this->error['filesize'] = _l("There was a problem creating the archive. The requested files to archive may have been corrupted.");

			return false;
		} else {
			$path = get_unique_file($path, DIR_DOWNLOAD);

			if (file_put_contents(DIR_DOWNLOAD . $path, $zip_contents) === false) {
				$this->error['rename'] = _l("Unable to move the archive to the download directory.");

				return false;
			}
		}

		$data = array(
			'name'      => $options['name'],
			'category'  => $options['category'],
			'folder_id' => $options['folder_id'],
			'path'      => DIR_DOWNLOAD . $path,
			'url'       => URL_DOWNLOAD . $path,
			'type'      => 'zip',
			'mime_type' => 'application/zip',
			'title'     => $options['title'] ? $options['title'] : pathinfo($path, PATHINFO_FILENAME),
		);

		return $this->save(null, $data);
	}
}
