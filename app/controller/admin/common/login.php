<?php
class App_Controller_Admin_Common_Login extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Administration"));

		//I user is logged in, redirect to the homepage
		if ($this->user->isLogged()) {
			redirect('admin');
		}

		if ($this->session->has('token') && !isset($_COOKIE['token'])) {
			$this->error['warning'] = _l("Invalid token session. Please login again.");
		}

		$defaults = array(
			'username' => '',
		);

		$data = $_POST + $defaults;

		//Actions
		$data['action'] = site_url('admin/common/login/authenticate');

		//Render
		output($this->render('common/login', $data));
	}

	public function authenticate()
	{
		if ($this->user->isLogged()) {
			$this->message->add('notify', _l("You are already logged in. Please log out first."));
		}
		elseif ($this->user->login($_POST['username'], $_POST['password'])) {
			if (!empty($_REQUEST['redirect'])) {
				$redirect = $_REQUEST['redirect'];
			} elseif ($this->request->hasRedirect()) {
				$this->request->doRedirect();
			} else {
				$redirect = 'admin';
			}

			redirect($redirect);
		} else {
			$this->message->add('warning', $this->user->getError());
		}

		redirect('admin/common/login');
	}
}
