<?php
class Admin_Controller_Extension_Module extends Controller
{
	public function index()
	{
		$this->template->load('extension/module');

		$this->language->load('extension/module');
		
		$this->document->setTitle($this->_('heading_title'));

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('extension/module'));

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

		$extensions = $this->Model_Setting_Extension->getInstalled('module');
		
		foreach ($extensions as $key => $value) {
			if (!file_exists(DIR_APPLICATION . 'controller/module/' . $value . '.php')) {
				$this->Model_Setting_Extension->uninstall('module', $value);
				
				unset($extensions[$key]);
			}
		}
		
		$this->data['extensions'] = array();
						
		$files = glob(DIR_APPLICATION . 'controller/module/*.php');
		
		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');
				
				$this->language->load('module/' . $extension);
	
				$action = array();
				
				if (!in_array($extension, $extensions)) {
					$action[] = array(
						'text' => $this->_('text_install'),
						'href' => $this->url->link('extension/module/install', 'extension=' . $extension)
					);
				} else {
					$action[] = array(
						'text' => $this->_('text_edit'),
						'href' => $this->url->link('module/' . $extension . '')
					);
								
					$action[] = array(
						'text' => $this->_('text_uninstall'),
						'href' => $this->url->link('extension/module/uninstall', 'extension=' . $extension)
					);
				}
												
				$this->data['extensions'][] = array(
					'name'	=> $this->_('heading_title'),
					'action' => $action
				);
			}
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	public function install()
	{
		if (!$this->user->hasPermission('modify', 'extension/module')) {
			$this->session->data['error'] = $this->_('error_permission');
			
			$this->url->redirect($this->url->link('extension/module'));
		} else {
			$this->Model_Setting_Extension->install('module', $_GET['extension']);

			$this->Model_User_UserGroup->addPermission($this->user->getId(), 'access', 'module/' . $_GET['extension']);
			$this->Model_User_UserGroup->addPermission($this->user->getId(), 'modify', 'module/' . $_GET['extension']);
			
			_require(DIR_APPLICATION . 'controller/module/' . $_GET['extension'] . '.php');
			
			$class = 'ControllerModule' . str_replace('_', '', $_GET['extension']);
			$class = new $class('module' . $_GET['extension'], 	$this->registry);
			
			if (method_exists($class, 'install')) {
				$class->install();
			}
			
			$this->url->redirect($this->url->link('extension/module'));
		}
	}
	
	public function uninstall()
	{
		$this->language->load('extension/module');
		
		if (!$this->user->hasPermission('modify', 'extension/module')) {
			$this->session->data['error'] = $this->_('error_permission');
			
			$this->url->redirect($this->url->link('extension/module'));
		} else {
			$this->Model_Setting_Extension->uninstall('module', $_GET['extension']);
		
			$this->Model_Setting_Setting->deleteSetting($_GET['extension']);
		
			_require(DIR_APPLICATION . 'controller/module/' . $_GET['extension'] . '.php');
			
			$class = 'ControllerModule' . str_replace('_', '', $_GET['extension']);
			$class = new $class('module/' . $_GET['extension'], $this->registry);
			
			if (method_exists($class, 'uninstall')) {
				$class->uninstall();
			}
			
			$this->message->add('success', $this->_('text_uninstalled_module'));
		
			$this->url->redirect($this->url->link('extension/module'));
		}
	}
}