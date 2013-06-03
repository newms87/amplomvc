<?php
class Catalog_Controller_Common_AboveContent extends Controller 
{
	public function index()
	{
		$this->template->load('common/above_content');

		$layout_id = $this->Model_Design_Layout->get_layout_id();

		$module_data = array();
		
		$extensions = $this->Model_Setting_Extension->getExtensions('module');
		
		foreach ($extensions as $extension) {
			$modules = $this->config->get($extension['code'] . '_module');
			
			if ($modules) {
				foreach ($modules as $module) {
					if ($module['layout_id'] == $layout_id && $module['position'] == 'above_content' && $module['status']) {
						$module_data[] = array(
							'code'		=> $extension['code'],
							'setting'	=> $module,
							'sort_order' => $module['sort_order']
						);
					}
				}
			}
		}
		
		$sort_order = array();
	
		foreach ($module_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}
		
		array_multisort($sort_order, SORT_ASC, $module_data);
		
		$this->data['modules'] = array();
		
		foreach ($module_data as $module) {
			$module = $this->getChild('module/' . $module['code'], $module['setting']);
			
			if ($module) {
				$this->data['modules'][] = $module;
			}
		}
		
		//Load Blocks associated with this position
		$blocks = $this->Model_Block_Block->getBlocksForPosition('above_content');
		
		$this->data['blocks'] = array();
		
		foreach ($blocks as $key => $block) {
			$this->data['blocks'][] = $this->getBlock($key);
		}
		
		$this->render();
	}
}
