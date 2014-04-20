<?php

class View
{
	private $registry;

	private $template;

	public function __construct($registry)
	{
		$this->registry = $registry;
	}

	public function __get($key)
	{
		return $this->registry->get($key);
	}

	public function getTemplate()
	{
		return $this->template;
	}

	public function load($path)
	{

	}

	public function render($path = null, $data = array())
	{

	}
}
