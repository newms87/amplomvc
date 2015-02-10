#<?php

//=====
final class Action
{
//.....
	public function __construct($path, $parameters = array())
	{
//-----
//>>>>> {php} {before}
		if (DB_PROFILE) {
			_profile('START: ' . $class . '->' . $method . '()');
		}
//-----
//=====
		require_once(_mod($file));
//-----
//=====
	}

//.....
	public function execute($is_ajax = null)
	{
//.....
		if ($this->is_valid) {
//-----
//>>>>> {php}
			if (DB_PROFILE) {
				_profile('CALL: ' . $this->class . '->' . $this->method . '()');
			}
//-----
//=====
			$this->output = $controller->output;
//-----
//>>>>> {php}
			if (DB_PROFILE) {
				_profile('END: ' . $this->class . '->' . $this->method . '()');
			}
//-----
//=====
		}
//.....
	}
//.....
}

//-----
