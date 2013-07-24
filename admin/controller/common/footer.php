<?php
class Admin_Controller_Common_Footer extends Controller
{
	public function index()
	{
		$this->template->load('common/footer');

		$this->load->language('common/footer');
		
		$this->render();
  	}
}