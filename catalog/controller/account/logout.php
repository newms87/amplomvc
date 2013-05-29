<?php
class ControllerAccountLogout extends Controller 
{
	public function index()
	{
		$this->template->load('common/success');

		if ($this->customer->isLogged()) {
				
			$this->customer->logout();
			
			$this->cart->clear();
		}
 
		$this->language->load('account/logout');
		
		$this->document->setTitle($this->_('heading_title'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
		$this->breadcrumb->add($this->_('text_logout'), $this->url->link('account/logout'));
		
		$this->data['continue'] = $this->url->link('common/home');
		
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
