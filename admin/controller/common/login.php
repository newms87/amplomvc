<?php
class Admin_Controller_Common_Login extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Administration"));

		//I user is logged in, redirect to the homepage
		if ($this->user->isLogged()) {
			$this->url->redirect('common/home');
		}

		$this->data['to_front'] = $this->url->store($this->config->get('config_default_store'), 'common/home');

		if (isset($this->session->data['token']) && !isset($_COOKIE['token'])) {
			$this->error['warning'] = _l("Invalid token session. Please login again.");
		}

		$this->data['messages'] = $this->message->fetch();

		$defaults = array(
			'username' => '',
		);

		$this->data += $_POST + $defaults;

		//If trying to access an admin page, redirect after login
		if (!empty($_REQUEST['redirect'])) {
			$redirect = $_REQUEST['redirect'];
		} else {
			$redirect = 'common/home';
		}

		$this->request->setRedirect('login', $redirect);

		//Actions
		$this->data['action'] = $this->url->link('common/login/authenticate');
		$this->data['forgotten'] = $this->url->link('common/forgotten');

		//The Template
		$this->view->load('common/login');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	public function authenticate()
	{
		if ($this->user->isLogged()) {
			$this->message->add('notify', _l("You are already logged in. Please log out first."));
		}
		elseif ($this->user->login($_POST['username'], $_POST['password'])) {
			if (!empty($_REQUEST['redirect'])) {
				$redirect = $_REQUEST['redirect'];
			} elseif ($this->request->hasRedirect('login')) {
				$this->request->doRedirect('login');
			} else {
				$redirect = 'common/home';
			}

			$this->url->redirect($redirect);
		} else {
			$this->message->add('warning', $this->user->getError());
		}

		$this->url->redirect('common/login');
	}
}
