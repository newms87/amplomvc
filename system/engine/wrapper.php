<?php
class System_Engine_Wrapper
{
	private $registry;
	private $do_render = false;
	private $class;

	function __construct($registry)
	{
		$this->registry = $registry;
	}

	public function __get($key)
	{
		return $this->registry->get($key);
	}

	public function __call($key, $args)
	{
		call_user_func_array(array($this->{$this->class},$key), $args);
	}

	public function init($class)
	{
		$this->do_render = false;
		$this->class = $class;
	}

	public function render()
	{
		$this->do_render = true;
	}
}

runkit_class_adopt();
