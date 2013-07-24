<?php
class Catalog_Controller_Account_Newsletter extends Controller
{
	public function index()
	{
		$this->template->load('account/newsletter');

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/newsletter');
	
			$this->url->redirect($this->url->link('account/login'));
		}
		
		$this->language->load('account/newsletter');
		
		$this->document->setTitle($this->_('heading_title'));
				
		if ($this->request->isPost()) {
			$data = array(
				'newsletter' => $_POST['newsletter'],
			);
			
			$this->customer->edit($data);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				$this->url->redirect($this->url->link('account/account'));
			}
		}

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
		$this->breadcrumb->add($this->_('text_newsletter'), $this->url->link('account/newsletter'));

		$this->data['action'] = $this->url->link('account/newsletter');
		
		$this->data['newsletter'] = $this->customer->info('newsletter');
		
		$this->data['back'] = $this->url->link('account/account');

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