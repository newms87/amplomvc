<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

class App_Controller_Common extends Controller
{
	public function file_upload()
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

	public function maintenance()
	{
		//Page Head
		set_page_info('title', _l("Maintenance"));

		$this->document->setLinks('primary', array());
		$this->document->setLinks('account', array());
		$this->document->setLinks('footer', array());

		//Render
		output($this->render('common/maintenance'));
	}
}
