<?php
class ControllerCommonElmanager extends Controller {
	
	public function index() {
$this->template->load('common/elmanager');

		if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
			$this->data['base'] = HTTPS_SERVER;
		} else {
			$this->data['base'] = HTTP_SERVER;
		}
			
		$dir = '';
      
      $this->data['elfinder_root_dir'] = '';
      
      if($this->user->isDesigner()){
         $dir = 'user_uploads/user_' . $this->user->getUserName();
         $this->data['elfinder_root_dir'] = 'data/user_uploads/';
      }
      
		_is_writable(DIR_IMAGE.'data/'.$dir, $this->config->get('config_image_dir_mode'));
      
      $_SESSION['elfinder_root_dir'] = $dir;
      $_SESSION['elfinder_dir_mode'] = $this->config->get('config_image_dir_mode');
      $_SESSION['elfinder_file_mode'] = $this->config->get('config_image_file_mode');
      
		
		$this->response->setOutput($this->render());
	}	
}