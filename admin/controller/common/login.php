<?php
class Admin_Controller_Common_Login extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Administration"));

		//I user is logged in, redirect to the homepage
		if ($this->user->isLogged()) {
			redirect('common/home');
		}

		$data['to_front'] = $this->url->store(option('config_default_store'), 'common/home');

		if ($this->session->has('token') && !isset($_COOKIE['token'])) {
			$this->error['warning'] = _l("Invalid token session. Please login again.");
		}

		$data['messages'] = $this->message->fetch();

		$defaults = array(
			'username' => '',
		);

		$data += $_POST + $defaults;

		//If trying to access an admin page, redirect after login
		if (!empty($_REQUEST['redirect'])) {
			$redirect = $_REQUEST['redirect'];
		} else {
			$redirect = 'common/home';
		}

		$this->request->setRedirect('login', $redirect);

		//Actions
		$data['action'] = site_url('common/login/authenticate');
		$data['forgotten'] = site_url('common/forgotten');

		//Render
		$this->response->setOutput($this->render('common/login', $data));
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

			redirect($redirect);
		} else {
			$this->message->add('warning', $this->user->getError());
		}

		redirect('common/login');
	}
}
