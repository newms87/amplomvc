<?php
class ControllerAccountNewsletter extends Controller {
	public function index() {
		$this->template->load('account/newsletter');

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/newsletter');
	
			$this->url->redirect($this->url->link('account/login'));
		}
		
		$this->language->load('account/newsletter');
		
		$this->document->setTitle($this->_('heading_title'));
				
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->model_account_customer->editNewsletter($_POST['newsletter']);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('account/account'));
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