<?php
class Catalog_Controller_Account_Password extends Controller
{


	public function index()
	{
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
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['confirm'])) {
			$data['error_confirm'] = $this->error['confirm'];
		} else {
			$data['error_confirm'] = '';
		}

		$data['action'] = $this->url->link('account/password');

		if (isset($_POST['password'])) {
			$data['password'] = $_POST['password'];
		} else {
			$data['password'] = '';
		}

		if (isset($_POST['confirm'])) {
			$data['confirm'] = $_POST['confirm'];
		} else {
			$data['confirm'] = '';
		}

		$data['back'] = $this->url->link('account/account');

		$this->response->setOutput($this->render('account/password', $data));
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
