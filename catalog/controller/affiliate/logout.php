<?php
class Catalog_Controller_Affiliate_Logout extends Controller
{
	public function index()
	{
		$this->template->load('common/success');

		if ($this->affiliate->isLogged()) {
			$this->affiliate->logout();

			$this->url->redirect('affiliate/logout');
		}

		$this->language->load('affiliate/logout');

		$this->document->setTitle($this->_('head_title'));

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_account'), $this->url->link('affiliate/account'));
		$this->breadcrumb->add($this->_('text_logout'), $this->url->link('affiliate/logout'));

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
