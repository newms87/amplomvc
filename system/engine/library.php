<?php

abstract class Library
{
	protected $registry;
	protected $error = array();

	public function __construct($registry)
	{
		$this->registry = $registry;
	}

	public function __get($key)
	{
		return $this->registry->get($key);
	}

	public function _($key)
	{
		if (func_num_args() > 1) {
			$args = func_get_args();

			return call_user_func_array(array($this->language, 'format'), $args);
		}

		return $this->language->get($key);
	}
}
