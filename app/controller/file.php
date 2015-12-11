<?php

class App_Controller_File extends Controller
{
	public function __construct()
	{
		parent::__construct();

		switch ($this->router->getAction()->getMethod()) {
			case 'index':
				if (!is_logged()) {
					redirect('customer/login');
				}
				break;
		}
	}

	public function index($data = array())
	{
		if (!$this->is_ajax) {
			page_info('title', _l("Amplo File Manager"));
		}

		$data += array(
			'accept' => '.png',
		);

		//Render
		output($this->render('file/manager', $data));
	}

	public function listing($data = array())
	{
		$sort    = (array)_request('sort', array('title' => 'ASC'));
		$filter  = (array)_request('filter');
		$options = array(
			'index' => 'file_id',
			'limit' => (int)_request('limit', 20),
			'start' => (int)_request('start'),
		);

		list($files, $total) = $this->Model_File->getRecords($sort, $filter, $options, true);

		$data['files'] = $files;
		$data['total'] = $total;

		output_json($data);
	}

	public function upload()
	{
		$files = _files();

		$dir_path  = _post('path', '');
		$file_name = count($files) > 1 ? false : _post('name');

		$mime_types = false;
		$accept     = _post('accept');

		if (is_string($accept)) {
			$accept = explode(',', $accept);
		}

		if ($accept) {
			$mime_types = array();

			foreach ($accept as $a) {
				$a = preg_replace('/^\\./', '', $a);

				switch ($a) {
					case 'png':
						$mime_types[] = 'image/png';
						break;

					case 'gif':
						$mime_types[] = 'image/gif';
						break;

					case 'jpg':
					case'jpeg':
						$mime_types[] = 'image/jpeg';
						$mime_types[] = 'image/jpg';
						break;
				}
			}
		}

		$saved = array();

		foreach ($files as $file) {
			$name = $file_name ? $file_name : $file['name'];

			$path = ltrim(rtrim($dir_path, '/') . '/' . $name, '/');

			if (empty($file['error'])) {
				if ($mime_types) {
					if (!in_array($file['type'], $mime_types)) {
						message('error', _l("File mime type %s not allowed.", $file['type']));
						continue;
					}
				}

				if (_is_writable(dirname(DIR_DOWNLOAD . $path)) && move_uploaded_file($file['tmp_name'], DIR_DOWNLOAD . $path)) {
					$saved[$file['name']] = URL_DOWNLOAD . $path;
				} else {
					message('error', _l("There was a problem saving your file %s to the server. Please upload again.", $file['name']));
				}
			} else {
				message('error', _l("File upload failed for %s. Please try again.", $file['name']));
			}
		}

		message('data', $saved);

		output_message();
	}

	public function upload_iframe()
	{
		$files = _files();

		$path      = _post('path', '');
		$file_name = count($files) > 1 ? false : _post('name');

		$saved = array();

		foreach ($files as $file) {
			$name = $file_name ? $file_name : $file['name'];

			$path = ltrim(rtrim($path, '/') . '/' . $name, '/');

			if (empty($file['error'])) {
				if (_is_writable(dirname(DIR_DOWNLOAD . $path)) && move_uploaded_file($file['tmp_name'], DIR_DOWNLOAD . $path)) {
					$saved[$file['name']] = URL_DOWNLOAD . $path;
				} else {
					message('error', _l("There was a problem saving your file %s to the server. Please upload again.", $file['name']));
				}
			} else {
				message('error', _l("File upload failed for %s. Please try again.", $file['name']));
			}
		}

		message('data', $saved);

		output(json_encode($this->message->fetch()));
	}
}
