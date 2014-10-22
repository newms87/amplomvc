#<?php
//=====
class Response extends Library
{
//.....
	public function output()
	{
//-----
//>>>>> {php}
		//Database Profiling
		if (DB_PROFILE && !IS_AJAX) {
			$this->dev->performance();
		}
//-----
//=====
	}
//.....
}
//-----
