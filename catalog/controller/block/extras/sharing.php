<?php
class Catalog_Controller_Block_Extras_Sharing extends Controller
{

	public function index()
	{
		$this->template->load('block/extras/sharing');

		$this->render();
	}
}
