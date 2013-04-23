<?php  
class ControllerCommonColumnLeft extends Controller {
	public function index() {
		$this->template->load('common/column_left');

		$layout_id = $this->model_design_layout->get_layout_id();
	
		$module_data = array();
		
		$extensions = $this->model_setting_extension->getExtensions('module');		
		
		foreach ($extensions as $extension) {
			$modules = $this->config->get($extension['code'] . '_module');
			
			if ($modules) {
				foreach ($modules as $module) {
					if ($module['layout_id'] == $layout_id && $module['position'] == 'column_left' && $module['status']) {
						$module_data[] = array(
							'code'       => $extension['code'],
							'setting'    => $module,
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
		
		$blocks = $this->model_block_block->getBlocksForPosition('column_left');
		
		$this->data['blocks'] = array();
		
		foreach($blocks as $key => $block){
			list($context, $name) = explode('/', $key);
			$this->data['blocks'][] = $this->getBlock($context, $name);
		}

		$this->render();
	}
}