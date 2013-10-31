<?php
class Admin_Controller_Common_Reset extends Controller
{
	public function index()
	{
		if ($this->user->isLogged()) {
			$this->url->redirect($this->url->link('common/home'));
		}

		$code = !empty($_GET['code']) ? $_GET['code'] : '';

		$user_info = $code ? $this->Model_User_User->getUserByCode($code) : null;

		//User not found
		if (!$user_info) {
			$this->url->redirect($this->url->link('common/login'));
		}

		//Handle POST
		if ($this->request->isPost() && $this->validate()) {
			$this->user->updatePassword($user_info['user_id'], $_POST['password']);

			$this->message->add('success', _l('You have successfully updated your password!'));

			$this->url->redirect($this->url->link('common/login'));
		}

		//Breadcrumbs
		$this->breadcrumb->add(_l('Home'), $this->url->link('common/home'));
		$this->breadcrumb->add(_l('Password Reset'), $this->url->link('common/reset'));

		//Action Buttons
		$this->data['save'] = $this->url->link('common/reset', 'code=' . $code);
		$this->data['cancel'] = $this->url->link('common/login');

		//The Template
		$this->template->load('common/reset');

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
		if (!$this->validation->password($_POST['password'])) {
			$this->error['password'] = _l("Your Password must be at least 8 characters long!");
		}

		if ($_POST['confirm'] !== $_POST['password']) {
			$this->error['confirm'] = _l('Your Password and Confirmation do not match.');
		}

		return $this->error ? false : true;
	}
}
