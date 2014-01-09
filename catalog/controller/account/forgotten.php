<?php
class Catalog_Controller_Account_Forgotten extends Controller
{
	public function index()
	{
		//Customer already logged in.
		if ($this->customer->isLogged()) {
			$this->url->redirect('account/account');
		}

		//Page Head
		$this->document->setTitle(_l("Forgot Your Password?"));

		//Handle POST
		if ($this->request->isPost() && $this->validate()) {
			$password = substr(md5(rand()), 0, 10);

			$customer_id = $this->customer->emailRegistered($_POST['email']);
			$this->customer->editPassword($customer_id, $password);

			$data = array(
				'email' => $_POST['email'],
				'password' => $password,
			);

			$this->mail->callController('forgotten', $data);

			$this->message->add('success', _l('Your password has been reset! Your new password has been sent to your email. Please change your password as soon as you log into your account!'));

			$this->url->redirect('account/login');
		}

		//Breadcrumbs
		$this->breadcrumb->add(_l('Home'), $this->url->link('common/home'));
		$this->breadcrumb->add(_l('Account'), $this->url->link('account/account'));
		$this->breadcrumb->add(_l('Forgotten Password'), $this->url->link('account/forgotten'));

		//Action Buttons
		$this->data['save'] = $this->url->link('account/forgotten');
		$this->data['back'] = $this->url->link('account/login');

		//The Template
		$this->template->load('account/forgotten');

		//Dependencies
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->validation->email($_POST['email'])) {
			$this->error['email'] = _l('Please enter a valid email address.');
		} elseif (!$this->customer->emailRegistered($_POST['email'])) {
			$this->error['email'] = _l('That email address is not registered with us.');
		}

		return $this->error ? false : true;
	}
}
