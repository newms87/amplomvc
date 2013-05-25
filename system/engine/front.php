<?php
final class Front {
	protected $registry;
	protected $error;
	
	public function __construct(&$registry) {
		$this->registry = &$registry;
	}
	
  	public function dispatch($action, $error) {
		$this->error = $error;
		
		while ($action) {
			$action = $this->execute($action);
		}
  	}
	
	private function execute($action) {
		$file = $action->getFile();
		$class = $action->getClass();
		$class_path = $action->getClassPath();
		$method = $action->getMethod();
		$args = $action->getArgs();
		
		$action = '';

		if (file_exists($file)) {
			_require_once($file);

			$controller = new $class($class_path, $this->registry);
			
			if (is_callable(array($controller, $method))) {
				$action = call_user_func_array(array($controller, $method), $args);
			} else {
				$action = $this->error;
			
				$this->error = '';
			}
		} else {
			$action = $this->error;
			
			$this->error = '';
		}
		
		return $action;
	}
}
