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
		if (AMPLO_PROFILE && !IS_AJAX) {
			$this->dev->performance();
		}
//-----
//=====
	}
//.....
}

//-----
