<?php
class Admin_Controller_Extension_Plugin extends Controller 
{
	
	public function index()
	{
		$this->load->language('extension/plugin');
		
		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
	}
	
	public function getList()
	{
		$this->template->load('extension/plugin');
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('extension/plugin'));
		
		$plugins = $this->Model_Setting_Plugin->getInstalledPlugins();
		
		$installed_plugins = array_keys($plugins);
		
		$this->data['plugins'] = array();
		$plugin_dirs = scandir(DIR_PLUGIN);
		
		if ($plugin_dirs) {
			foreach ($plugin_dirs as $dir) {
				if($dir == '.' || $dir == '..')continue;

				$action = array();
				
				if (in_array($dir,$installed_plugins)) {
					$action[] = array(
						'text' => $this->_('text_edit'),
						'href' => $this->url->link('extension/plugin/update', 'name='.$dir)
					);
					$action[] = array(
						'text' => $this->_('text_uninstall'),
						'href' => $this->url->link('extension/plugin/uninstall', 'name='.$dir)
					);
				}
				else {
					$action[] = array(
						'text' => $this->_('text_install'),
						'href' => $this->url->link('extension/plugin/install', 'name='.$dir)
					);
				}
				
				$this->data['plugins'][] = array(
					'name'	=> $dir,
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
	
	public function getForm()
	{
		$this->template->load('extension/plugin_form');
		
		if (!isset($_GET['name'])) {
			$this->message->add('warning', $this->_('error_no_plugin'));
			$this->url->redirect($this->url->link('extension/plugin'));
		}
		$plugin_name = $_GET['name'];
		
		$this->document->setTitle($this->_('heading_title'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('extension/plugin'));
				
		if (isset($_POST['plugin_data'])) {
			$this->data['plugin_data'] = $_POST['plugin_data'];
		}
		else {
			$this->data['plugin_data'] = $this->Model_Setting_Plugin->getPluginData($plugin_name);
		}
		
		$this->data['name'] = $plugin_name;

		$this->data['action'] = $this->url->link('extension/plugin/update','name='.$plugin_name);
		$this->data['cancel'] = $this->url->link('extension/plugin');
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	public function update()
	{
		$this->cache->delete('model');
		
		$this->load->language('extension/plugin');

		if (!isset($_GET['name'])) {
			$this->message->add('warning', $this->_('error_no_plugin'));
			$this->url->redirect($this->url->link('extension/plugin'));
		}

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->Model_Setting_Plugin->updatePlugin($_GET['name'], $_POST['plugin_data']);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('extension/plugin'));
		}

		$this->getForm();
	}
	
	public function install()
	{
		if (isset($_GET['name'])) {
			$this->plugin->install($_GET['name']);
		}
		
		$this->url->redirect($this->url->link('extension/plugin'));
	}
	
	public function uninstall()
	{
		if (isset($_GET['name'])) {
			$keep_data = isset($_GET['keep_data']) ? (int)$_GET['keep_data'] : true;
			
			$this->plugin->uninstall($_GET['name'], $keep_data);
		}
		
		$this->url->redirect($this->url->link('extension/plugin'));
	}
	
	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'extension/plugin')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		$plugs = $_POST['plugin_data'];
		$name = ucfirst($_GET['name']);
		
		foreach ($plugs as $p) {
			
		}
		
		return $this->error ? false : true;
	}
}
