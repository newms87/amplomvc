<?php
class Catalog_Controller_Account_Password extends Controller
{


	public function index()
	{
		$this->template->load('account/password');

		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', $this->url->link('account/password'));

			$this->url->redirect('account/login');
		}

		$this->document->setTitle(_l("Change Password"));

		if ($this->request->isPost() && $this->validate()) {
			$this->customer->editPassword($this->customer->getId(), $_POST['password']);

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: Your password has been successfully updated."));
				$this->url->redirect('account/account');
			}
		}

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account/account'));
		$this->breadcrumb->add(_l("Change Password"), $this->url->link('account/password'));

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

		$this->data['action'] = $this->url->link('account/password');

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

	private function validate()
	{
		if ((strlen($_POST['password']) < 4) || (strlen($_POST['password']) > 20)) {
			$this->error['password'] = _l("Password must be between 4 and 20 characters!");
		}

		if ($_POST['confirm'] != $_POST['password']) {
			$this->error['confirm'] = _l("Password confirmation does not match password!");
		}

		return $this->error ? false : true;
	}
}
