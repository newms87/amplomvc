<?php
class ControllerAffiliateSuccess extends Controller 
{
	public function index()
	{
		$this->template->load('common/success');

		$this->language->load('affiliate/success');
  
		$this->document->setTitle($this->_('heading_title'));
		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('affiliate/account'));
			$this->breadcrumb->add($this->_('text_success'), $this->url->link('affiliate/success'));

		$this->data['text_message'] = $this->language->format('text_approval', $this->config->get('config_name'), $this->url->link('information/contact'));
		
		$this->data['continue'] = $this->url->link('affiliate/account');

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