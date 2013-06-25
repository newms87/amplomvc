<?php
class Catalog_Controller_Information_Information extends Controller 
{
	public function index()
	{
		$this->language->load('information/information');
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		
		$information_id = isset($_GET['information_id']) ? $_GET['information_id'] : 0;
		
		$information_info = $this->Model_Catalog_Information->getInformation($information_id);
		
		if ($information_info) {
			//Layout override (only if set)
			$layout_id = $this->Model_Catalog_Information->getInformationLayoutId($information_id);
			
			if ($layout_id) {
				$this->config->set('config_layout_id', $layout_id);
			}
			
			$this->template->load('information/information');
			
			$this->document->setTitle($information_info['title']);
			
			$this->breadcrumb->add($information_info['title'], $this->url->link('information/information', 'information_id=' .  $information_id));
			
			$this->language->set('heading_title', $information_info['title']);
			
			$this->data['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');
			
			$this->data['continue'] = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : $this->url->link('common/home');

			$this->data['breadcrumbs'] = $this->breadcrumb->render();
			
			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);
			
			$this->response->setOutput($this->render());
		} else {
			$this->template->load('error/not_found');

			$this->breadcrumb->add($this->_('text_error'), $this->url->link('information/information', 'information_id=' . $information_id));
			
			$this->document->setTitle($this->_('text_error'));
			
			$this->language->set('heading_title', $this->_('text_error'));

			$this->data['continue'] = $this->url->link('common/home');

			$this->data['breadcrumbs'] = $this->breadcrumb->render();
			
			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);
					
			$this->response->setOutput($this->render());
		}
  	}
	
	public function info()
	{
		$this->language->load('information/information');
		$this->template->load('information/information_only');
		
		$information_id = isset($_GET['information_id']) ? $_GET['information_id'] : 0;
		
		$information_info = $this->Model_Catalog_Information->getInformation($information_id);
		
		if ($information_info) {
			$information_info['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');
			 
			$this->data = $information_info;
		}
		
		$this->response->setOutput($this->render());
	}
}