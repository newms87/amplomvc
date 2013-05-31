<?php
class Catalog_Controller_Module_Banner extends Controller 
{
	protected function index($setting)
	{
		$this->template->load('module/banner');

		static $module = 0;
		
		$this->document->addScript('catalog/view/javascript/jquery/jquery.cycle.js');
				
		$this->data['banners'] = array();
		
		$results = $this->Model_Design_Banner->getBanner($setting['banner_id']);
		
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