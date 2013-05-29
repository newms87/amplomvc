<?php
class ControllerExtensionTotal extends Controller 
{
	public function index()
	{
		$this->template->load('extension/total');

		$this->load->language('extension/total');
		
		$this->document->setTitle($this->_('heading_title'));

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('extension/total'));

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

		$extensions = $this->model_setting_extension->getInstalled('total');
		
		foreach ($extensions as $key => $value) {
			if (!file_exists(DIR_APPLICATION . 'controller/total/' . $value . '.php')) {
				$this->model_setting_extension->uninstall('total', $value);
				
				unset($extensions[$key]);
			}
		}
		
		$this->data['extensions'] = array();
				
		$files = glob(DIR_APPLICATION . 'controller/total/*.php');
		
		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');
				
				$this->load->language('total/' . $extension);
	
				$action = array();
				
				if (!in_array($extension, $extensions)) {
					$action[] = array(
						'text' => $this->_('text_install'),
						'href' => $this->url->link('extension/total/install', 'extension=' . $extension)
					);
				} else {
					$action[] = array(
						'text' => $this->_('text_edit'),
						'href' => $this->url->link('total/' . $extension . '')
					);
								
					$action[] = array(
						'text' => $this->_('text_uninstall'),
						'href' => $this->url->link('extension/total/uninstall', 'extension=' . $extension)
					);
				}
										
				$this->data['extensions'][] = array(
					'name'		=> $this->_('heading_title'),
					'status'	=> $this->config->get($extension . '_status') ? $this->_('text_enabled') : $this->_('text_disabled'),
					'sort_order' => $this->config->get($extension . '_sort_order'),
					'action'	=> $action
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
		if (!$this->user->hasPermission('modify', 'extension/total')) {
			$this->session->data['error'] = $this->_('error_permission');
			
			$this->url->redirect($this->url->link('extension/total'));
		} else {
			$this->model_setting_extension->install('total', $_GET['extension']);

			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'total/' . $_GET['extension']);
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'total/' . $_GET['extension']);

			_require_once(DIR_APPLICATION . 'controller/total/' . $_GET['extension'] . '.php');
			
			$class = 'ControllerTotal' . str_replace('_', '', $_GET['extension']);
			$class = new $class($this->registry);
			
			if (method_exists($class, 'install')) 
{
				$class ->install();
			}
			
			$this->url->redirect($this->url->link('extension/total'));
		}
	}
	
	public function uninstall()
	{
		if (!$this->user->hasPermission('modify', 'extension/total')) {
			$this->session->data['error'] = $this->_('error_permission');
			
			$this->url->redirect($this->url->link('extension/total'));
		} else {
			$this->model_setting_extension->uninstall('total', $_GET['extension']);
		
			$this->model_setting_setting->deleteSetting($_GET['extension']);
		
			_require_once(DIR_APPLICATION . 'controller/total/' . $_GET['extension'] . '.php');
			
			$class = 'ControllerTotal' . str_replace('_', '', $_GET['extension']);
			$class = new $class($this->registry);
			
			if (method_exists($class, 'uninstall')) 
{
				$class->uninstall();
			}
		
			$this->url->redirect($this->url->link('extension/total'));
		}
	}
}