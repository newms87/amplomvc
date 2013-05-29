<?php
class ControllerPluginCms extends Controller 
{
	
	public function settings()
	{
		$this->language->plugin('cms','admin/cms');
		
		$configs = array('config_image_cms_category_article_width','config_image_cms_category_article_height'
							);
		foreach ($configs as $c) {
			$this->data[$c] = isset($_POST[$c])?$_POST[$c]:$this->config->get($c);
		}
	}
	
	public function settings_validate($return)
	{
		$this->language->plugin('cms', 'admin/cms');
		$configs = array('config_image_cms_category_article_width','config_image_cms_category_article_height');
		foreach ($configs as $c) {
			$check = isset($_POST[$c])?$_POST[$c]:null;
			if (!$check) {
				$this->error['error_image_cms_category_article'] = $this->_('error_image_cms_category_article');
			}
		}
		
		return $this->error?false:$return;
	}
}
