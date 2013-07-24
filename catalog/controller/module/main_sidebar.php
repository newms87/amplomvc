<?php
class Catalog_Controller_Module_MainSidebar extends Controller
{
	protected function index()
	{
		$this->template->load('error/not_found');

		$this->language->load('module/main_sidebar');
		
		$designers = $this->Model_Catalog_Manufacturer->getManufacturers();
		foreach($designers as $key=>$designer)
			$designers[$key]['href'] = SITE_URL . $designer['keyword'];
		
		$this->data['designers'] = $designers;

		$this->render();
	}
}