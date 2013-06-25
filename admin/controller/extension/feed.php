<?php
class Admin_Controller_Extension_Feed extends Controller 
{
	public function index()
	{
		$this->template->load('extension/feed');

		$this->load->language('extension/feed');
		
		$this->document->setTitle($this->_('heading_title'));

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('extension/feed'));

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		if (isset($this->session->data['error'])) {
			$this->data['error'] = $this->session->data['error'];
		
			unset($this->session->data['error']);
		} else {
			$this->data['error'] = '';
		}

		$extensions = $this->Model_Setting_Extension->getInstalled('feed');
		
		foreach ($extensions as $key => $value) {
			if (!file_exists(DIR_APPLICATION . 'controller/feed/' . $value . '.php')) {
				$this->Model_Setting_Extension->uninstall('feed', $value);
				
				unset($extensions[$key]);
			}
		}
		
		$this->data['extensions'] = array();
						
		$files = glob(DIR_APPLICATION . 'controller/feed/*.php');
		
		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');
			
				$this->load->language('feed/' . $extension);

				$action = array();
			
				if (!in_array($extension, $extensions)) {
					$action[] = array(
						'text' => $this->_('text_install'),
						'href' => $this->url->link('extension/feed/install', 'extension=' . $extension)
					);
				} else {
					$action[] = array(
						'text' => $this->_('text_edit'),
						'href' => $this->url->link('feed/' . $extension . '')
					);
							
					$action[] = array(
						'text' => $this->_('text_uninstall'),
						'href' => $this->url->link('extension/feed/uninstall', 'extension=' . $extension)
					);
				}
									
				$this->data['extensions'][] = array(
					'name'	=> $this->_('heading_title'),
					'status' => $this->config->get($extension . '_status') ? $this->_('text_enabled') : $this->_('text_disabled'),
					'action' => $action
				);
			}
		}

		$this->data['breadcrumbs'] = $this->breadcrumb->render();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	public function install()
	{
		if (!$this->user->hasPermission('modify', 'extension/feed')) {
				$this->session->data['error'] = $this->_('error_permission');
			
			$this->url->redirect($this->url->link('extension/feed'));
		} else {
			$this->Model_Setting_Extension->install('feed', $_GET['extension']);
		
			$this->Model_User_UserGroup->addPermission($this->user->getId(), 'access', 'feed/' . $_GET['extension']);
			$this->Model_User_UserGroup->addPermission($this->user->getId(), 'modify', 'feed/' . $_GET['extension']);
		
			_require(DIR_APPLICATION . 'controller/feed/' . $_GET['extension'] . '.php');
			
			$class = 'ControllerFeed' . str_replace('_', '', $_GET['extension']);
			$class = new $class($this->registry);
			
			if (method_exists($class, 'install')) {
				$class->install();
			}
		
			$this->url->redirect($this->url->link('extension/feed'));
		}
	}
	
	public function uninstall()
	{
		if (!$this->user->hasPermission('modify', 'extension/feed')) {
				$this->session->data['error'] = $this->_('error_permission');
			
			$this->url->redirect($this->url->link('extension/feed'));
		} else {
			$this->Model_Setting_Extension->uninstall('feed', $_GET['extension']);
		
			$this->Model_Setting_Setting->deleteSetting($_GET['extension']);
		
			_require(DIR_APPLICATION . 'controller/feed/' . $_GET['extension'] . '.php');
			
			$class = 'ControllerFeed' . str_replace('_', '', $_GET['extension']);
			$class = new $class($this->registry);
			
			if (method_exists($class, 'uninstall')) {
				$class->uninstall();
			}
		
			$this->url->redirect($this->url->link('extension/feed'));
		}
	}
}