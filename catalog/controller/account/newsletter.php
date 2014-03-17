<?php
class Catalog_Controller_Account_Newsletter extends Controller
{
	public function index()
	{
		$this->view->load('account/newsletter');

		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', $this->url->link('account/newsletter'));

			$this->url->redirect('account/login');
		}

		$this->document->setTitle(_l("Newsletter Subscription"));

		if ($this->request->isPost()) {
			$data = array(
				'newsletter' => $_POST['newsletter'],
			);

			$this->customer->edit($data);

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: Your newsletter subscription has been successfully updated!"));
				$this->url->redirect('account/account');
			}
		}

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account/account'));
		$this->breadcrumb->add(_l("Newsletter"), $this->url->link('account/newsletter'));

		$this->data['action'] = $this->url->link('account/newsletter');

		$this->data['newsletter'] = $this->customer->info('newsletter');

		$this->data['back'] = $this->url->link('account/account');

		$this->children = array(
			'area/left',
			'area/right',
			'area/top',
			'area/bottom',
			'common/footer',
			'common/header'
		);

		$this->response->setOutput($this->render());
	}
}
