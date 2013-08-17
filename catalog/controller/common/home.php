<?php
class Catalog_Controller_Common_Home extends Controller
{
	public function index()
	{
		$this->template->load('common/home');

		$this->document->setTitle($this->config->get('config_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));

		$this->language->set('head_title', $this->config->get('config_title'));

		$this->data['main_image'] = $this->image->resize('data/display.jpg', 1024, 640);

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
