<?php
class App_Controller_Common_Maintenance extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Maintenance"));

		//Render
		$this->response->setOutput($this->render('common/maintenance', $data));
	}
}
