<?php

class App_Controller_Common extends Controller
{
	public function file_upload()
	{
		$files = _files();

		$dir_path  = _post('path', '');
		$file_name = count($files) > 1 ? false : _post('name');

		$saved = array();

		foreach ($files as $file) {
			$name = $file_name ? $file_name : $file['name'];

			$path = ltrim(rtrim($dir_path, '/') . '/' . $name, '/');

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

		output_message();
	}

	public function file_upload_iframe()
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
