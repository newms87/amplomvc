<?php
class Admin_Controller_Module_FeaturedCarousel extends Controller
{
	
	
	public function index()
	{
		$this->template->load('module/featured_carousel');

		$this->language->load('module/featured_carousel');

		$this->document->setTitle($this->_('heading_title'));
		
		$is_post = $this->request->isPost();
		
		if ($is_post && $this->validate()) {
			$this->Model_Setting_Setting->editSetting('featured_carousel', $_POST);
			
			$this->message->add('success', $this->_('text_success'));
			
			$is_post = false;
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_module'), $this->url->link('extension/module'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('module/featured_carousel'));
		
		$this->data['action'] = $this->url->link('module/featured_carousel', 'another=343');
		$this->data['cancel'] = $this->url->link('extension/module');
		
		$defaults = array(
			'featured_carousel_module' => array(),
			'featured_carousel_list' => array(),
			'featured_product_list' => array(),
		);
		
		if (!$is_post) {
			$featured_carousel = $this->Model_Setting_Setting->getSetting('featured_carousel');
		}
		
		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			}
			elseif (isset($featured_carousel[$key])) {
				$this->data[$key] = $featured_carousel[$key];
			}
			else {
				$this->data[$key] = $default;
			}
		}
		
		foreach ($this->data['featured_carousel_list'] as &$item) {
			$item['thumb'] = $this->image->resize($item['image'], 100,100);
		}
		
		foreach ($this->data['featured_product_list'] as &$item) {
			$item['thumb'] = $this->image->resize($item['image'], 100,100);
		}
		
		$this->data['data_designers'] = $this->Model_Catalog_Manufacturer->getManufacturers();
		
		$this->data['data_layouts'] = $this->Model_Design_Layout->getLayouts();
		
		$this->data['no_image'] = $this->image->resize('no_image.png', 100, 100);
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'module/featured_carousel')) {
			$this->error['warning'] = $this->_('error_permission');
		}
				
		return $this->error ? false : true;
	}
}
