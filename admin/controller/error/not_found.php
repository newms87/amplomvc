<?php
class Admin_Controller_Error_NotFound extends Controller
{
	public function index()
	{
		$this->template->load('error/not_found');

		$this->language->load('error/not_found');

		$this->document->setTitle($this->_('head_title'));

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('error/not_found'));

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}
}
