#<?php

//=====
final class Action
{
//.....
	public function __construct($path, $parameters = array())
	{
//-----
//>>>>> {php} {before}
		if (AMPLO_PROFILE) {
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
			if (AMPLO_PROFILE) {
				_profile('CALL: ' . $this->class . '->' . $this->method . '()');
			}
//-----
//=====
			$this->output = $controller->output ? $controller->output : $output;
//-----
//>>>>> {php}
			if (AMPLO_PROFILE) {
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
