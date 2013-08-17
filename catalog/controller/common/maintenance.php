<?php
class Catalog_Controller_Common_Maintenance extends Controller
{
	public function index()
	{
		$this->template->load('common/maintenance');

		$this->language->load('common/maintenance');

		$this->document->setTitle($this->_('head_title'));

		$this->language->set('message', $this->_('text_message'));

		$this->children = array(
			'common/footer',
			'common/header'
		);

		$this->response->setOutput($this->render());
	}
}
