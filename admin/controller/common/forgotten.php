<?php
class Admin_Controller_Common_Forgotten extends Controller
{
	public function index()
	{
		//Verify User is not already logged in
		if ($this->user->isLogged()) {
			$this->url->redirect('common/home');
		}

		//Page Title
		$this->document->setTitle(_l("Forgot Your Password?"));

		//Handle POST
		if ($this->request->isPost() && $this->validate()) {
			$code = $this->user->generateCode();

			$this->user->setCode($_POST['email'], $code);

			$email_data = array(
				'reset' => $this->url->link('common/forgotten/reset', 'code=' . $code),
				'email' => $_POST['email'],
			);

			$this->mail->sendTemplate('forgotten_admin', $email_data);

			$this->message->add('success', _l("Please follow the link that was sent to your email to reset your password."));

			$this->url->redirect('common/login');
		}

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Forgotten Password"), $this->url->link('common/forgotten'));

		//Entry Data
		$this->data['email'] = isset($_POST['email']) ? $_POST['email'] : '';

		//Action Buttons
		$this->data['action'] = $this->url->link('common/forgotten');
		$this->data['cancel'] = $this->url->link('common/login');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//The Template
		$this->view->load('common/forgotten');

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
			$this->url->redirect('common/login');
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

			$this->url->redirect('common/login');
		}

		//Breadcrumbs
		$this->breadcrumb->add(_l('Home'), $this->url->link('common/home'));
		$this->breadcrumb->add(_l('Password Reset'), $this->url->link('common/forgotten/reset', 'code=' . $code));

		//Action Buttons
		$this->data['save']   = $this->url->link('common/forgotten/reset', 'code=' . $code);
		$this->data['cancel'] = $this->url->link('common/login');

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
		if (empty($_POST['email']) || !$this->Model_User_User->getTotalUsersByEmail($_POST['email'])) {
			$this->error['email'] = _l("Warning: The E-Mail Address was not found in our records, please try again!");
		}

		return $this->error ? false : true;
	}
}
