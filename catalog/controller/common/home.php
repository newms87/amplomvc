<?php

class Catalog_Controller_Common_Home extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle($this->config->get('config_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));

		//Page Title
		$data = array(
			'page_title'     => $this->config->get('config_title'),
			'call_to_action' => $this->config->get('config_home_call_to_action'),
		);

		//Dependencies
		$this->children = array(
			'area/left',
			'area/right',
			'area/top',
			'area/bottom',
			'common/footer',
			'common/header'
		);

		//Render
		$output = $this->render('common/home', $data);
		$this->response->setOutput($output);
	}
}
