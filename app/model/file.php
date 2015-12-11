<?php

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

		$file['user_id']     = user_info('user_id');
		$file['customer_id'] = customer_info('customer_id');

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
			if ($file['customer_id'] === customer_info('customer_id') || user_can('w', 'admin/file')) {
				if (file_exists($file['path'])) {
					unlink($file['path']);
				}

				return parent::remove($file_id);
			}
		}

		return false;
	}

	public function upload($file, $options = array())
	{
		$options += array(
			'title'     => '',
			'name'      => '',
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
			'folder_id' => $options['folder_id'],
			'path'      => DIR_DOWNLOAD . $path,
			'url'       => URL_DOWNLOAD . $path,
			'type'      => $type,
			'mime_type' => $file['type'],
			'title'     => $options['title'] ? $options['title'] : pathinfo($path, PATHINFO_FILENAME),
		);

		return $this->save(null, $data);
	}
}
