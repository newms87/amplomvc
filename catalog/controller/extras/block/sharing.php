<?php
class Catalog_Controller_Extras_Block_Sharing extends Controller 
{
		
	public function index()
	{
		$this->language->load('extras/block/sharing');
		
		$this->template->load('extras/block/sharing');
		
		$this->render();
	}
}