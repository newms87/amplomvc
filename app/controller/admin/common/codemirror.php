<?php

class App_Controller_Admin_Common_Codemirror extends Controller
{
	public function index()
	{
		output($this->render('common/codemirror'));
	}
}
