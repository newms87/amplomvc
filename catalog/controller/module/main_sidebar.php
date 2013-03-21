<?php  
class ControllerModuleMainSidebar extends Controller {
	protected function index() {
$this->template->load('error/not_found');

		$this->language->load('module/main_sidebar');
		
		$designers = $this->model_catalog_manufacturer->getManufacturers();
		foreach($designers as $key=>$designer)
			$designers[$key]['href'] = SITE_URL . $designer['keyword'];
		
		$this->data['designers'] = $designers;






		$this->render();
	}
}