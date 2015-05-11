<?php

class App_Controller_Mail_Header extends Controller
{
	public function index($data = array())
	{
		$data += array(
			'title' => option('site_name'),
			'logo'  => str_replace("./", '', option('site_logo')),
		);

		$this->render('mail/header', $data);
	}
}
