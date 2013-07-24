<?php
class Admin_Controller_Common_Filemanager extends Controller
{
	
	public function index()
	{
		$this->template->load('common/filemanager');

		$this->data['base'] = $this->url->is_ssl() ? SITE_SSL : SITE_URL;
		
		$dir = '';
		
		$this->data['elfinder_root_dir'] = '';
		
		if ($this->user->isDesigner()) {
			$dir = 'user_uploads/user_' . $this->user->getUserName();
			$this->data['elfinder_root_dir'] = 'data/user_uploads/';
		}
		
		_is_writable(DIR_IMAGE.'data/'.$dir, $this->config->get('config_image_dir_mode'));
		
		$_SESSION['elfinder_root_dir'] = $dir;
		$_SESSION['elfinder_dir_mode'] = $this->config->get('config_image_dir_mode');
		$_SESSION['elfinder_file_mode'] = $this->config->get('config_image_file_mode');
		
		
		$this->response->setOutput($this->render());
	}
	
	public function ckeditor()
	{
		$this->template->load('common/ckeditor');

		$this->load->language('common/filemanager');
		
		$this->language->set('title', $this->_('heading_title'));
		
		$this->data['base'] = $this->url->is_ssl() ? SITE_SSL : SITE_URL;
		
		$this->data['directory'] = HTTP_IMAGE . 'data/';
		
		if (isset($_GET['field'])) {
			$this->data['field'] = $_GET['field'];
		} else {
			$this->data['field'] = '';
		}
		
		if (isset($_GET['CKEditorFuncNum'])) {
			$this->data['fckeditor'] = $_GET['CKEditorFuncNum'];
		} else {
			$this->data['fckeditor'] = false;
		}
		
		
		$this->response->setOutput($this->render());
	}
	
	public function image()
	{
		if (isset($_GET['image'])) {
			$width = isset($_GET['image_width']) ? (int)$_GET['image_width'] : $this->config->get('config_image_admin_thumb_width');
			$height = isset($_GET['image_height']) ? (int)$_GET['image_height'] : $this->config->get('config_image_admin_thumb_height');
			
			$this->response->setOutput($this->image->resize(html_entity_decode($_GET['image'], ENT_QUOTES, 'UTF-8'), $width, $height));
		}
	}
	
	public function directory()
	{
		$json = array();
		$restricted = $this->user->isDesigner();
		if ($restricted) {
			$dir = 'user_uploads/user_' . $this->user->getUserName();
			if(!is_dir(DIR_IMAGE . 'data/' . $dir))
				mkdir(DIR_IMAGE . 'data/' . $dir, 0777, true);
			$_POST['directory'] = $dir;
		}
		if (isset($_POST['directory'])) {
			$directories = glob(rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $_POST['directory']), '/') . '/*', GLOB_ONLYDIR);
			
			if ($directories) {
				$i = 0;
			
				foreach ($directories as $directory) {
					$json[$i]['data'] = basename($directory);
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
		
		$restricted = $this->user->isDesigner();
		$restrict = '';
		if ($restricted) {
			$dir = 'user_uploads/user_' . $this->user->getUserName();
			if(!is_dir(DIR_IMAGE . 'data/' . $dir))
				mkdir(DIR_IMAGE . 'data/' . $dir, 0777, true);
			$restrict = !empty($_POST['directory'])?$_POST['directory']:$dir . '/';
			$_POST['directory'] = '';
		}
		
		if (!empty($_POST['directory'])) {
			$directory = DIR_IMAGE . 'data/' . str_replace('../', '', $_POST['directory']);
		} else {
			$directory = DIR_IMAGE . 'data/' . $restrict;
		}
		
		$allowed = explode(',',strtolower($this->config->get('config_upload_images_allowed')));
		foreach($allowed as &$a)
			$a = trim($a);
		
		$files = glob(rtrim($directory, '/') . '/*');
		
		if ($files) {
			foreach ($files as $file) {
				if (is_file($file)) {
					$ext = strrchr($file, '.');
					$ext = $ext?substr($ext,1):'';
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
						'file'	=> substr($file, strlen(DIR_IMAGE . 'data/')),
						'size'	=> round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i]
					);
				}
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function create()
	{
		$this->load->language('common/filemanager');
				
		$json = array();
		
		if (isset($_POST['directory'])) {
			if (isset($_POST['name']) || $_POST['name']) {
				$restricted = $this->user->isDesigner();
				if ($restricted && empty($this->requst->post['directory'])) {
					$_POST['directory'] = 'user_uploads/user_' . $this->user->getUserName() . '/';
				}
				
				$directory = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $_POST['directory']), '/');
				
				if (!is_dir($directory)) {
					$json['error'] = $this->_('error_directory');
				}
				
				if (file_exists($directory . '/' . str_replace('../', '', $_POST['name']))) {
					$json['error'] = $this->_('error_exists');
				}
			} else {
				$json['error'] = $this->_('error_name');
			}
		} else {
			$json['error'] = $this->_('error_directory');
		}
		
		if (!$this->user->hasPermission('modify', 'common/filemanager')) {
				$json['error'] = $this->_('error_permission');
		}
		
		if (!isset($json['error'])) {
			mkdir($directory . '/' . str_replace('../', '', $_POST['name']), 0777);
			
			$json['success'] = $this->_('text_create');
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function delete()
	{
		$this->load->language('common/filemanager');
		
		$json = array();
		
		if (isset($_POST['path'])) {
			$path = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', html_entity_decode($_POST['path'], ENT_QUOTES, 'UTF-8')), '/');
			
			if (!file_exists($path)) {
				$json['error'] = $this->_('error_select');
			}
			
			if ($path == rtrim(DIR_IMAGE . 'data/', '/')) {
				$json['error'] = $this->_('error_delete');
			}
		} else {
			$json['error'] = $this->_('error_select');
		}
		
		if (!$this->user->hasPermission('modify', 'common/filemanager')) {
				$json['error'] = $this->_('error_permission');
		}
		
		if (!isset($json['error'])) {
			if (is_file($path)) {
				unlink($path);
			} elseif (is_dir($path)) {
				$this->recursiveDelete($path);
			}
			
			$json['success'] = $this->_('text_delete');
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
		$this->load->language('common/filemanager');
		
		$json = array();
		
		if (isset($_POST['from']) && isset($_POST['to'])) {
			$from = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', html_entity_decode($_POST['from'], ENT_QUOTES, 'UTF-8')), '/');
			
			if (!file_exists($from)) {
				$json['error'] = $this->_('error_missing');
			}
			
			if ($from == DIR_IMAGE . 'data') {
				$json['error'] = $this->_('error_default');
			}
			
			$to = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', html_entity_decode($_POST['to'], ENT_QUOTES, 'UTF-8')), '/');

			if (!file_exists($to)) {
				$json['error'] = $this->_('error_move');
			}
			
			if (file_exists($to . '/' . basename($from))) {
				$json['error'] = $this->_('error_exists');
			}
		} else {
			$json['error'] = $this->_('error_directory');
		}
		
		if (!$this->user->hasPermission('modify', 'common/filemanager')) {
				$json['error'] = $this->_('error_permission');
		}
		
		if (!isset($json['error'])) {
			rename($from, $to . '/' . basename($from));
			
			$json['success'] = $this->_('text_move');
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function copy()
	{
		$this->load->language('common/filemanager');
		
		$json = array();
		
		if (isset($_POST['path']) && isset($_POST['name'])) {
			if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 255)) {
				$json['error'] = $this->_('error_filename');
			}
				
			$old_name = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', html_entity_decode($_POST['path'], ENT_QUOTES, 'UTF-8')), '/');
			
			if (!file_exists($old_name) || $old_name == DIR_IMAGE . 'data') {
				$json['error'] = $this->_('error_copy');
			}
			
			if (is_file($old_name)) {
				$ext = strrchr($old_name, '.');
			} else {
				$ext = '';
			}
			
			$new_name = dirname($old_name) . '/' . str_replace('../', '', html_entity_decode($_POST['name'], ENT_QUOTES, 'UTF-8') . $ext);
																				
			if (file_exists($new_name)) {
				$json['error'] = $this->_('error_exists');
			}
		} else {
			$json['error'] = $this->_('error_select');
		}
		
		if (!$this->user->hasPermission('modify', 'common/filemanager')) {
				$json['error'] = $this->_('error_permission');
		}
		
		if (!isset($json['error'])) {
			if (is_file($old_name)) {
				copy($old_name, $new_name);
			} else {
				$this->recursiveCopy($old_name, $new_name);
			}
			
			$json['success'] = $this->_('text_copy');
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
		
		foreach ($directories  as $directory) {
			$output .= $this->recursiveFolders($directory);
		}
		
		return $output;
	}
	
	public function rename()
	{
		$this->load->language('common/filemanager');
		
		$json = array();
		
		if (isset($_POST['path']) && isset($_POST['name'])) {
			if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 255)) {
				$json['error'] = $this->_('error_filename');
			}
				
			$old_name = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', html_entity_decode($_POST['path'], ENT_QUOTES, 'UTF-8')), '/');
			
			if (!file_exists($old_name) || $old_name == DIR_IMAGE . 'data') {
				$json['error'] = $this->_('error_rename');
			}
			
			if (is_file($old_name)) {
				$ext = strrchr($old_name, '.');
			} else {
				$ext = '';
			}
			
			$new_name = dirname($old_name) . '/' . str_replace('../', '', html_entity_decode($_POST['name'], ENT_QUOTES, 'UTF-8') . $ext);
																				
			if (file_exists($new_name)) {
				$json['error'] = $this->_('error_exists');
			}
		}
		
		if (!$this->user->hasPermission('modify', 'common/filemanager')) {
				$json['error'] = $this->_('error_permission');
		}
		
		if (!isset($json['error'])) {
			rename($old_name, $new_name);
			
			$json['success'] = $this->_('text_rename');
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function upload()
	{
		$this->load->language('common/filemanager');
		
		$json = array();
		
		if (isset($_POST['directory'])) {
			if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
				$filename = basename(html_entity_decode($_FILES['image']['name'], ENT_QUOTES, 'UTF-8'));
				
				if ((strlen($filename) < 3) || (strlen($filename) > 255)) {
					$json['error'] = $this->_('error_filename');
				}
				
				$restricted = $this->user->isDesigner();
				if ($restricted && empty($this->requst->post['directory'])) {
					$_POST['directory'] = 'user_uploads/user_' . $this->user->getUserName() . '/';
				}

				$directory = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $_POST['directory']), '/');
				
				if (!is_dir($directory)) {
					$json['error'] = $this->_('error_directory');
				}
				
				if ($_FILES['image']['size'] > 30000000) {
					$json['error'] = $this->_('error_file_size');
				}
				
				$allowed = explode(',',$this->config->get('config_upload_images_mime_types_allowed'));
				foreach($allowed as &$a)
					$a = trim($a);
				if (!in_array($_FILES['image']['type'], $allowed)) {
					$json['error'] = $this->_('error_file_type');
				}
				
				$allowed = explode(',',$this->config->get('config_upload_images_allowed'));
				foreach($allowed as &$a)
					$a = trim($a);
				
				if (!in_array(strtolower(substr(strrchr($filename, '.'),1)), $allowed)) {
					$json['error'] = $this->_('error_file_type');
				}

				if ($_FILES['image']['error'] != UPLOAD_ERR_OK) {
					$json['error'] = 'error_upload_' . $_FILES['image']['error'];
				}
			} else {
				$json['error'] = $this->_('error_file');
			}
		} else {
			$json['error'] = $this->_('error_directory');
		}
		
		if (!$this->user->hasPermission('modify', 'common/filemanager')) {
				$json['error'] = $this->_('error_permission');
		}
		
		if (!isset($json['error'])) {
			if (@move_uploaded_file($_FILES['image']['tmp_name'], $directory . '/' . $filename)) {
				$json['success'] = $this->_('text_uploaded');
			} else {
				$json['error'] = $this->_('error_uploaded');
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
}
