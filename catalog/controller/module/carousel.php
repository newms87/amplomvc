<?php
class ControllerModuleCarousel extends Controller {
	protected function index($setting) {
		$this->template->load('module/carousel');

		static $module = 0;
		
		$this->document->addScript('catalog/view/javascript/jquery/jquery.jcarousel.min.js');
		
		if (file_exists('catalog/view/theme/' . $this->config->get('config_theme') . '/stylesheet/carousel.css')) {
			$this->document->addStyle('catalog/view/theme/' . $this->config->get('config_theme') . '/stylesheet/carousel.css');
		} else {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/carousel.css');
		}
						
		$this->data['limit'] = $setting['limit'];
		$this->data['scroll'] = $setting['scroll'];
				
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