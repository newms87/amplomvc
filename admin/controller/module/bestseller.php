<?php
class ControllerModuleBestSeller extends Controller {
	
	public function index() {
		$this->template->load('module/bestseller');

		$this->load->language('module/bestseller');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('bestseller', $_POST);
			
			$this->cache->delete('product');
			
			$this->message->add('success',$this->_('text_success'));
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_module'), $this->url->link('extension/module'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('module/bestseller'));
		
		$this->data['action'] = $this->url->link('module/bestseller');
		
		$this->data['cancel'] = $this->url->link('extension/module');
		
		$this->data['modules'] = array();
		
		$configs = array(
			'modules'=>'bestseller_module',
			'bestseller_list' => 'bestseller_list',
			'options'=>'bestseller_option'
		);
		foreach($configs as $key=>$config){
			$this->data[$key] = isset($_POST[$config])?$_POST[$config]:$this->config->get($config);
		}
		
		if(!$this->data['bestseller_list']){
			$this->data['bestseller_list'] = array();
		}
		else{
			$names = $this->model_catalog_product->getProductNames(array_keys($this->data['bestseller_list']));
			foreach($names as $n){
				$this->data['bestseller_list'][$n['product_id']] = $n['name'];
			}
		}
		
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/bestseller')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (isset($_POST['bestseller_module'])) {
			foreach ($_POST['bestseller_module'] as $key => $value) {
				if (!$value['image_width'] || !$value['image_height']) {
					$this->error["image-$key"] = $this->_('error_image');
				}
			}
		}
		
		return $this->error ? false : true;
	}
}
