<?php
class ControllerModuleFlashsaleSidebar extends Controller {
	protected function index($setting) {
		$this->template->load('module/flashsale_sidebar');
		
		$this->language->load('module/flashsale_sidebar'); 
		
		empty($setting['limit'])?$setting['limit']=3:'';
		
		$filter = 'date_start < NOW() AND date_end > NOW()';
		$sort = 'name ASC';
		$flashsales = $this->model_catalog_flashsale->getFlashsales($filter, $sort, $setting['limit']);
		
		$flashsales = is_array($flashsales)?$flashsales: array();
		
		foreach($flashsales as &$f)
			$f['href'] = $this->url->site($f['keyword']);
		$this->data['flashsales'] = $flashsales;
		
		if(count($flashsales) == 0)return;
		
		$this->render();
	}
}