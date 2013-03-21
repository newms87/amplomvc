<?php  
class ControllerModuleBanner extends Controller {
	protected function index($setting) {
$this->template->load('module/banner');

		static $module = 0;
		
		$this->document->addScript('catalog/view/javascript/jquery/jquery.cycle.js');
				
		$this->data['banners'] = array();
		
		$results = $this->model_design_banner->getBanner($setting['banner_id']);
		  
		foreach ($results as $result) {
			if (file_exists(DIR_IMAGE . $result['image'])) {
				$this->data['banners'][] = array(
					'title' => $result['title'],
					'link'  => $result['link'],
					'image' => $this->image->resize($result['image'], $setting['width'], $setting['height'])
				);
			}
		}
		
		$this->data['module'] = $module++;
				






		$this->render();
	}
}