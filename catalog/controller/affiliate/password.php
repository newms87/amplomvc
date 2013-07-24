<?php
class Catalog_Controller_Affiliate_Password extends Controller
{
	
		
  	public function index()
  	{
		$this->template->load('affiliate/password');

		if (!$this->affiliate->isLogged()) {
				$this->session->data['redirect'] = $this->url->link('affiliate/password');

				$this->url->redirect($this->url->link('affiliate/login'));
		}

		$this->language->load('affiliate/password');

		$this->document->setTitle($this->_('heading_title'));
			
		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Affiliate_Affiliate->editPassword($this->affiliate->getEmail(), $_POST['password']);

				$this->message->add('success', $this->_('text_success'));
	
			$this->url->redirect($this->url->link('affiliate/account'));
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('affiliate/account'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('affiliate/password'));

		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}

		if (isset($this->error['confirm'])) {
			$this->data['error_confirm'] = $this->error['confirm'];
		} else {
			$this->data['error_confirm'] = '';
		}
	
		$this->data['action'] = $this->url->link('affiliate/password');
		
		if (isset($_POST['password'])) {
			$this->data['password'] = $_POST['password'];
		} else {
			$this->data['password'] = '';
		}

		if (isset($_POST['confirm'])) {
			$this->data['confirm'] = $_POST['confirm'];
		} else {
			$this->data['confirm'] = '';
		}

		$this->data['back'] = $this->url->link('affiliate/account');

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

  	private function validate()
  	{
		if ((strlen($_POST['password']) < 4) || (strlen($_POST['password']) > 20)) {
				$this->error['password'] = $this->_('error_password');
		}

		if ($_POST['confirm'] != $_POST['password']) {
				$this->error['confirm'] = $this->_('error_confirm');
		}
	
		return $this->error ? false : true;
  	}
}
