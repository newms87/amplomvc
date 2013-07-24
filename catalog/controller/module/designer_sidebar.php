<?php
class Catalog_Controller_Module_DesignerSidebar extends Controller
{
	protected function index($setting)
	{
		$this->template->load('module/designer_sidebar');
		
		$this->language->load('module/designer_sidebar');
		
		empty($setting['limit'])?$setting['limit']=3:'';
		
		$data = array(
				'sort'=>'date_expires',
				'order'=>'ASC',
				'limit'=>$setting['limit']
			);
		$designers = $this->Model_Catalog_Designer->getDesigners($data);
		
		$designers = is_array($designers)?$designers: array();
		
		foreach($designers as &$f)
			$f['href'] = $this->url->site($f['keyword']);
		$this->data['designers'] = $designers;
		
		$this->data['view_all_designers'] = $this->url->link('designers/designers');
		
		if(count($designers) == 0)return;
		
		$this->render();
	}
}
