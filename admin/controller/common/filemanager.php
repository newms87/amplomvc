<?php
class Admin_Controller_Common_Filemanager extends Controller
{
	public function index()
	{
		$this->view->load('common/filemanager');

		$this->data['base'] = URL_SITE;

		$dir = '';

		$this->data['elfinder_root_dir'] = '';

		_is_writable(DIR_IMAGE . 'data/' . $dir, AMPLOCART_DIR_MODE);

		$_SESSION['elfinder_root_dir']  = $dir;
		$_SESSION['elfinder_dir_mode']  = AMPLOCART_DIR_MODE;
		$_SESSION['elfinder_file_mode'] = AMPLOCART_FILE_MODE;

		$this->response->setOutput($this->render());
	}

	public function ckeditor()
	{
		//Template and Language
		$this->view->load('common/ckeditor');
		$this->data['base'] = URL_SITE;

		$this->data['directory'] = URL_IMAGE . 'data/';

		$defaults = array(
			'field'           => '',
			'CKEditorFuncNum' => false,
		);

		foreach ($defaults as $key => $default) {
			if (isset($_GET[$key])) {
				$this->data[$key] = $_GET[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		$this->response->setOutput($this->render());
	}

	public function image()
	{
		if (isset($_GET['image'])) {
			$width  = isset($_GET['image_width']) ? (int)$_GET['image_width'] : $this->config->get('config_image_admin_thumb_width');
			$height = isset($_GET['image_height']) ? (int)$_GET['image_height'] : $this->config->get('config_image_admin_thumb_height');

			$this->response->setOutput($this->image->resize(str_replace('\\', '/', html_entity_decode($_GET['image'], ENT_QUOTES, 'UTF-8')), $width, $height));
		}
	}

	public function directory()
	{
		$json = array();

		if (isset($_POST['directory'])) {
			$directories = glob(rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $_POST['directory']), '/') . '/*', GLOB_ONLYDIR);

			if ($directories) {
				$i = 0;

				foreach ($directories as $directory) {
					$json[$i]['data']                    = basename($directory);
					$json[$i]['attributes']['directory'] = substr($directory, strlen(DIR_IMAGE . 'data/'));

					$children = glob(rtrim($directory, '/') . '/*', GLOB_ONLYDIR);

					if ($children) {
						$json[$i]['children'] = ' ';
					}

					$i++;
				}
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function files()
	{
		$json = array();

		if (!empty($_POST['directory'])) {
			$directory = DIR_IMAGE . 'data/' . str_replace('../', '', $_POST['directory']);
		} else {
			$directory = DIR_IMAGE . 'data/';
		}

		$allowed = explode(',', strtolower($this->config->get('config_upload_images_allowed')));
		foreach ($allowed as &$a) {
			$a = trim($a);
		}

		$files = glob(rtrim($directory, '/') . '/*');

		if ($files) {
			foreach ($files as $file) {
				if (is_file($file)) {
					$ext = strrchr($file, '.');
					$ext = $ext ? substr($ext, 1) : '';
				} else {
					$ext = '';
				}

				if (in_array(strtolower($ext), $allowed)) {
					$size = filesize($file);

					$i = 0;

					$suffix = array(
						'B',
						'KB',
						'MB',
						'GB',
						'TB',
						'PB',
						'EB',
						'ZB',
						'YB'
					);

					while (($size / 1024) > 1) {
						$size = $size / 1024;
						$i++;
					}

					$json[] = array(
						'filename' => basename($file),
						'file'     => substr($file, strlen(DIR_IMAGE . 'data/')),
						'size'     => round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i]
					);
				}
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function create()
	{
		$json = array();

		if (isset($_POST['directory'])) {
			if (isset($_POST['name']) || $_POST['name']) {
				$directory = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $_POST['directory']), '/');

				if (!is_dir($directory)) {
					$json['error'] = _l("Warning: Please select a directory!");
				}

				if (file_exists($directory . '/' . str_replace('../', '', $_POST['name']))) {
					$json['error'] = _l("Warning: A file or directory with the same name already exists!");
				}
			} else {
				$json['error'] = _l("Warning: Please enter a new name!");
			}
		} else {
			$json['error'] = _l("Warning: Please select a directory!");
		}

		if (!$this->user->can('modify', 'common/filemanager')) {
			$json['error'] = _l("Warning: Permission Denied!");
		}

		if (!isset($json['error'])) {
			mkdir($directory . '/' . str_replace('../', '', $_POST['name']), 0777);

			$json['success'] = _l("Success: Directory created!");
		}

		$this->response->setOutput(json_encode($json));
	}

	public function delete()
	{
		$json = array();

		if (isset($_POST['path'])) {
			$path = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', html_entity_decode($_POST['path'], ENT_QUOTES, 'UTF-8')), '/');

			if (!file_exists($path)) {
				$json['error'] = _l("Warning: Please select a directory or file!");
			}

			if ($path == rtrim(DIR_IMAGE . 'data/', '/')) {
				$json['error'] = _l("Warning: You can not delete this directory!");
			}
		} else {
			$json['error'] = _l("Warning: Please select a directory or file!");
		}

		if (!$this->user->can('modify', 'common/filemanager')) {
			$json['error'] = _l("Warning: Permission Denied!");
		}

		if (!isset($json['error'])) {
			if (is_file($path)) {
				unlink($path);
			} elseif (is_dir($path)) {
				$this->recursiveDelete($path);
			}

			$json['success'] = _l("Success: Your file or directory has been deleted!");
		}

		$this->response->setOutput(json_encode($json));
	}

	protected function recursiveDelete($directory)
	{
		if (is_dir($directory)) {
			$handle = opendir($directory);
		}

		if (!$handle) {
			return false;
		}

		while (false !== ($file = readdir($handle))) {
			if ($file != '.' && $file != '..') {
				if (!is_dir($directory . '/' . $file)) {
					unlink($directory . '/' . $file);
				} else {
					$this->recursiveDelete($directory . '/' . $file);
				}
			}
		}

		closedir($handle);

		rmdir($directory);

		return true;
	}

	public function move()
	{
		$json = array();

		if (isset($_POST['from']) && isset($_POST['to'])) {
			$from = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', html_entity_decode($_POST['from'], ENT_QUOTES, 'UTF-8')), '/');

			if (!file_exists($from)) {
				$json['error'] = _l("Warning: File or directory does not exist!");
			}

			if ($from == DIR_IMAGE . 'data') {
				$json['error'] = _l("Warning: Can not alter your default directory!");
			}

			$to = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', html_entity_decode($_POST['to'], ENT_QUOTES, 'UTF-8')), '/');

			if (!file_exists($to)) {
				$json['error'] = _l("Warning: Move to directory does not exists!");
			}

			if (file_exists($to . '/' . basename($from))) {
				$json['error'] = _l("Warning: A file or directory with the same name already exists!");
			}
		} else {
			$json['error'] = _l("Warning: Please select a directory!");
		}

		if (!$this->user->can('modify', 'common/filemanager')) {
			$json['error'] = _l("Warning: Permission Denied!");
		}

		if (!isset($json['error'])) {
			rename($from, $to . '/' . basename($from));

			$json['success'] = _l("Success: Your file or directory has been moved!");
		}

		$this->response->setOutput(json_encode($json));
	}

	public function copy()
	{
		$json = array();

		if (isset($_POST['path']) && isset($_POST['name'])) {
			if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 255)) {
				$json['error'] = _l("Warning: Filename must be a between 3 and 255!");
			}

			$old_name = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', html_entity_decode($_POST['path'], ENT_QUOTES, 'UTF-8')), '/');

			if (!file_exists($old_name) || $old_name == DIR_IMAGE . 'data') {
				$json['error'] = _l("Warning: Can not copy this file or directory!");
			}

			if (is_file($old_name)) {
				$ext = strrchr($old_name, '.');
			} else {
				$ext = '';
			}

			$new_name = dirname($old_name) . '/' . str_replace('../', '', html_entity_decode($_POST['name'], ENT_QUOTES, 'UTF-8') . $ext);

			if (file_exists($new_name)) {
				$json['error'] = _l("Warning: A file or directory with the same name already exists!");
			}
		} else {
			$json['error'] = _l("Warning: Please select a directory or file!");
		}

		if (!$this->user->can('modify', 'common/filemanager')) {
			$json['error'] = _l("Warning: Permission Denied!");
		}

		if (!isset($json['error'])) {
			if (is_file($old_name)) {
				copy($old_name, $new_name);
			} else {
				$this->recursiveCopy($old_name, $new_name);
			}

			$json['success'] = _l("Success: Your file or directory has been copied!");
		}

		$this->response->setOutput(json_encode($json));
	}

	function recursiveCopy($source, $destination)
	{
		$directory = opendir($source);

		@mkdir($destination);

		while (false !== ($file = readdir($directory))) {
			if (($file != '.') && ($file != '..')) {
				if (is_dir($source . '/' . $file)) {
					$this->recursiveCopy($source . '/' . $file, $destination . '/' . $file);
				} else {
					copy($source . '/' . $file, $destination . '/' . $file);
				}
			}
		}

		closedir($directory);
	}

	public function folders()
	{
		$this->response->setOutput($this->recursiveFolders(DIR_IMAGE . 'data/'));
	}

	protected function recursiveFolders($directory)
	{
		$output = '';

		$output .= '<option value="' . substr($directory, strlen(DIR_IMAGE . 'data/')) . '">' . substr($directory, strlen(DIR_IMAGE . 'data/')) . '</option>';

		$directories = glob(rtrim(str_replace('../', '', $directory), '/') . '/*', GLOB_ONLYDIR);

		foreach ($directories as $directory) {
			$output .= $this->recursiveFolders($directory);
		}

		return $output;
	}

	public function rename()
	{
		$json = array();

		if (isset($_POST['path']) && isset($_POST['name'])) {
			if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 255)) {
				$json['error'] = _l("Warning: Filename must be a between 3 and 255!");
			}

			$old_name = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', html_entity_decode($_POST['path'], ENT_QUOTES, 'UTF-8')), '/');

			if (!file_exists($old_name) || $old_name == DIR_IMAGE . 'data') {
				$json['error'] = _l("Warning: Can not rename this directory!");
			}

			if (is_file($old_name)) {
				$ext = strrchr($old_name, '.');
			} else {
				$ext = '';
			}

			$new_name = dirname($old_name) . '/' . str_replace('../', '', html_entity_decode($_POST['name'], ENT_QUOTES, 'UTF-8') . $ext);

			if (file_exists($new_name)) {
				$json['error'] = _l("Warning: A file or directory with the same name already exists!");
			}
		}

		if (!$this->user->can('modify', 'common/filemanager')) {
			$json['error'] = _l("Warning: Permission Denied!");
		}

		if (!isset($json['error'])) {
			rename($old_name, $new_name);

			$json['success'] = _l("Success: Your file or directory has been renamed!");
		}

		$this->response->setOutput(json_encode($json));
	}

	public function upload()
	{
		$json = array();

		if (isset($_POST['directory'])) {
			if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
				$filename = basename(html_entity_decode($_FILES['image']['name'], ENT_QUOTES, 'UTF-8'));

				if ((strlen($filename) < 3) || (strlen($filename) > 255)) {
					$json['error'] = _l("Warning: Filename must be a between 3 and 255!");
				}

				$directory = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $_POST['directory']), '/');

				if (!is_dir($directory)) {
					$json['error'] = _l("Warning: Please select a directory!");
				}

				if ($_FILES['image']['size'] > 30000000) {
					$json['error'] = _l("Warning: File too big please keep below 30mb!");
				}

				$allowed = explode(',', $this->config->get('config_upload_images_mime_types_allowed'));
				foreach ($allowed as &$a) {
					$a = trim($a);
				}
				if (!in_array($_FILES['image']['type'], $allowed)) {
					$json['error'] = _l("Warning: Incorrect file type!");
				}

				$allowed = explode(',', $this->config->get('config_upload_images_allowed'));
				foreach ($allowed as &$a) {
					$a = trim($a);
				}

				if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
					$json['error'] = _l("Warning: Incorrect file type!");
				}

				if ($_FILES['image']['error'] != UPLOAD_ERR_OK) {
					$json['error'] = 'error_upload_' . $_FILES['image']['error'];
				}
			} else {
				$json['error'] = _l("Warning: Please select a file!");
			}
		} else {
			$json['error'] = _l("Warning: Please select a directory!");
		}

		if (!$this->user->can('modify', 'common/filemanager')) {
			$json['error'] = _l("Warning: Permission Denied!");
		}

		if (!isset($json['error'])) {
			if (@move_uploaded_file($_FILES['image']['tmp_name'], $directory . '/' . $filename)) {
				$json['success'] = _l("Success: Your file has been uploaded!");
			} else {
				$json['error'] = _l("Warning: File could not be uploaded for an unknown reason!");
			}
		}

		$this->response->setOutput(json_encode($json));
	}
}
