<?php
class Admin_Controller_Module_Featured extends Controller
{
	
	
	public function index()
	{
		$this->template->load('module/featured');

		$this->load->language('module/featured');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$settings = $_POST;

			foreach($settings['featured_module'] as $key=>$module)
				$settings['featured_module'][] = array('status'=>$module['status'], 'layout_id'=>$module['layout_id'], 'sort_order'=>$module['sort_order'],'position'=>$module['filter_menu_position'],'display'=>$module['display'],'fm_id'=>$key);
			
			$this->Model_Setting_Setting->editSetting('featured', $settings);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('module/featured'));
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_module'), $this->url->link('extension/module'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('module/featured'));
		
		$this->data['action'] = $this->url->link('module/featured');
		$this->data['cancel'] = $this->url->link('extension/module');
		if (isset($_POST['featured_product'])) {
			$products = $_POST['featured_product'];
		} else {
			$products = $this->config->get('featured_product');
		}
		
		$this->data['featured_product'] = array();
		
		if (!empty($products)) {
			foreach ($products as $product_id) {
				$product_info = $this->Model_Catalog_Product->getProduct($product_id);
				
				if ($product_info) {
					$this->data['featured_product'][] = array(
						'product_id' => $product_info['product_id'],
						'name'		=> $product_info['name']
					);
				}
			}
		}
		
		$dfp = $this->config->get('default_product_filter');
		$this->data['default_product_filter'] = isset($dfp)?$dfp:'';
		$pft=$this->config->get('product_filter_types');
		$this->data['product_filter_types'] = isset($pft)?$pft:array();
		
		$this->data['modules'] = array();
		
		if (isset($_POST['featured_module'])) {
			$this->data['modules'] = $_POST['featured_module'];
		} else {
			$this->data['modules'] = $this->config->get('featured_module');
		}
				
		$this->data['layouts'] = $this->Model_Design_Layout->getLayouts();

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'module/featured')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (isset($_POST['featured_module'])) {
			foreach ($_POST['featured_module'] as $key => $value) {
				if (!$value['image_width'] || !$value['image_height']) {
					$this->error['image'] = $this->_('error_image');
				}
			}
		}
				
		return $this->error ? false : true;
	}
}