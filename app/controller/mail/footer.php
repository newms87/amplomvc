<?php

class App_Controller_Mail_Footer extends Controller
{
	public function index($data = array())
	{
		$this->render('mail/footer', $data);
	}
}
