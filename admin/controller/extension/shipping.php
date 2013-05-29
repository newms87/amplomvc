<?php
class ControllerExtensionShipping extends Controller 
{
	public function index()
	{
		$this->template->load('extension/shipping');

		$this->load->language('extension/shipping');
		
		$this->document->setTitle($this->_('heading_title'));
  		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('extension/shipping'));

		$extensions = $this->model_setting_extension->getInstalled('shipping');
		
		foreach ($extensions as $key => $value) {
			if (!file_exists(DIR_APPLICATION . 'controller/shipping/' . $value . '.php')) {
				$this->model_setting_extension->uninstall('shipping', $value);
				
				unset($extensions[$key]);
			}
		}
		
		$this->data['extensions'] = array();
		
		$files = glob(DIR_APPLICATION . 'controller/shipping/*.php');
		
		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');
				
				$this->load->language('shipping/' . $extension);
	
				$action = array();
				
				if (!in_array($extension, $extensions)) {
					$action[] = array(
						'text' => $this->_('text_install'),
						'href' => $this->url->link('extension/shipping/install', 'extension=' . $extension)
					);
				} else {
					$action[] = array(
						'text' => $this->_('text_edit'),
						'href' => $this->url->link('shipping/' . $extension . '')
					);
								
					$action[] = array(
						'text' => $this->_('text_uninstall'),
						'href' => $this->url->link('extension/shipping/uninstall', 'extension=' . $extension)
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
		if (!$this->user->hasPermission('modify', 'extension/shipping')) {
			$this->session->data['error'] = $this->_('error_permission');
			
			$this->url->redirect($this->url->link('extension/shipping'));
		} else {
			$this->model_setting_extension->install('shipping', $_GET['extension']);

			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'shipping/' . $_GET['extension']);
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'shipping/' . $_GET['extension']);

			_require_once(DIR_APPLICATION . 'controller/shipping/' . $_GET['extension'] . '.php');
			
			$class = 'ControllerShipping' . str_replace('_', '', $_GET['extension']);
			$class = new $class($this->registry);
			
			if (method_exists($class, 'install')) 
{
				$class ->install();
			}
			
			$this->url->redirect($this->url->link('extension/shipping'));
		}
	}
	
	public function uninstall()
	{
		if (!$this->user->hasPermission('modify', 'extension/shipping')) {
			$this->session->data['error'] = $this->_('error_permission');
			
			$this->url->redirect($this->url->link('extension/shipping'));
		} else {
			$this->model_setting_extension->uninstall('shipping', $_GET['extension']);
		
			$this->model_setting_setting->deleteSetting($_GET['extension']);
		
			_require_once(DIR_APPLICATION . 'controller/shipping/' . $_GET['extension'] . '.php');
			
			$class = 'ControllerShipping' . str_replace('_', '', $_GET['extension']);
			$class = new $class($this->registry);
			
			if (method_exists($class, 'uninstall')) 
{
				$class->uninstall();
			}
		
			$this->url->redirect($this->url->link('extension/shipping'));
		}
	}
}