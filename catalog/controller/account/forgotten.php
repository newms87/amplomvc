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
			$code = $this->customer->generateCode();

			$this->customer->setCode($_POST['email'], $code);

			$email_data = array(
				'email' => $_POST['email'],
				'reset' => $this->url->link('account/forgotten/reset', 'code=' . $code),
			);

			$this->mail->sendTemplate('forgotten', $email_data);

			$this->message->add('notify', _l("Please follow the link that was sent to your email to reset your password."));

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
		$this->view->load('account/forgotten');

		//Dependencies
		$this->children = array(
			'area/left',
			'area/right',
			'area/top',
			'area/bottom',
			'common/footer',
			'common/header'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	public function reset()
	{
		if ($this->user->isLogged() || empty($_GET['code'])) {
			$this->url->redirect('common/home');
		}

		$code = $_GET['code'];

		$user = $this->user->lookupCode($code);

		//User not found
		if (!$user) {
			$this->message->add('warning', _l("Unable to locate password reset code. Please try again."));
			$this->url->redirect('account/login');
		}

		//Handle POST
		if ($this->request->isPost()) {
			//Validate Password
			if (!$this->validation->password($_POST['password'])) {
				if ($this->validation->isCode(Validation::PASSWORD_CONFIRM)) {
					$this->error['confirm'] = $this->validation->getError();
				} else {
					$this->error['password'] = $this->validation->getError();
				}
			} else {
				$this->user->updatePassword($user['user_id'], $_POST['password']);
				$this->user->clearCode($user['user_id']);

				$this->message->add('success', _l('You have successfully updated your password!'));
			}

			$this->url->redirect('account/login');
		}

		//Breadcrumbs
		$this->breadcrumb->add(_l('Home'), $this->url->link('common/home'));
		$this->breadcrumb->add(_l('Password Reset'), $this->url->link('account/forgotten/reset', 'code=' . $code));

		//Action Buttons
		$this->data['save']   = $this->url->link('account/forgotten/reset', 'code=' . $code);
		$this->data['cancel'] = $this->url->link('account/login');

		//The Template
		$this->view->load('common/reset');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
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
