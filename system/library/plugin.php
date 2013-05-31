<?php
class Plugin{
	private $registry;
	
	function __construct($registry)
	{
		$this->registry = $registry;
	}
	
	public function __get($key)
	{
		return $this->registry->get($key);
	}
	
	/**
	 * Install a plugin
	 * 
	 * @param $name - The name of the directory for the plugin to install
	 * @return Boolean - true on success, or false on failure (error message will be set)
	 */
	
	public function install($name)
	{
		$this->cache->delete('plugin');
		$this->cache->delete('model');
		$this->cache->delete('lang_ext');
		
		$setup_file = DIR_PLUGIN . $name . '/setup.php';
		
		if (!is_file($setup_file)) {
			$this->message->add("warning",sprintf($this->_('error_install_file'),$setup_file, $name));
		}
		else {
			_require_once(DIR_SYSTEM . 'plugins/setupplugin.php');
			_require_once($setup_file);
			
			$user_class = 'Setup'.preg_replace("/[^A-Z0-9]/i", "",$name);
			$user_class = new $user_class($this->registry);
			
			if (method_exists($user_class, 'install')) {
				$controller_adapters = array();
				$db_requests 			= array();
				
				$user_class ->install($controller_adapters, $db_requests);
				
				if ($this->System_Model_Plugin->install($name, $controller_adapters, $db_requests)) {
					$this->message->add('success',sprintf($this->_("success_install"),$name));
				}
			}
			else {
				$this->message->add('warning',sprintf($this->_("error_install_function"), $name));
			}
		}
	}
}
