<?php
class Admin_Controller_Common_Login extends Controller
{
	public function index()
	{
		$this->template->load('common/login');

		$this->document->setTitle(_l("Administration"));

		//IF user is logged in, redirect to the homepage
		if (isset($_POST['username']) && isset($_POST['password'])) {
			$this->user->logout();
		} elseif ($this->user->isLogged()) {
			$this->url->redirect('common/home');
		}

		//if user is not logged in and has provided valid login credentials
		if ($this->request->isPost() && $this->validate()) {
			if (!empty($_GET['redirect'])) {
				$this->url->redirect(urldecode($_GET['redirect']));
			} else {
				$this->url->redirect('common/home');
			}
		}

		$this->data['to_front'] = $this->url->store($this->config->get('config_default_store'), 'common/home');

		if (isset($this->session->data['token']) && !isset($_COOKIE['token'])) {
			$this->error['warning'] = _l("Invalid token session. Please login again.");
		}

		$this->data['messages'] = $this->message->fetch();

		$defaults = array(
			'username' => '',
			'password' => '',
		);

		foreach ($defaults as $key => $default) {
			$this->data[$key] = isset($_POST[$key]) ? $_POST[$key] : $default;
		}

		//If trying to access an admin page, redirect after login
		if (!isset($_GET['redirect'])) {
			$route = $this->url->getPath();

			if ($route) {
				$not_allowed = array(
					'common/login',
					'common/logout'
				);

				if (in_array($route, $not_allowed)) {
					$redirect = urlencode($this->url->link('common/home'));
				} else {
					$redirect = urlencode(preg_replace("/redirect=[^&#]*/", '', $this->url->here()));
				}
			} else {
				$redirect = urlencode($this->url->link('common/home'));
			}
		} else {
			$redirect = $_GET['redirect'];
		}

		$this->data['action'] = $this->url->link('common/login', 'redirect=' . $redirect);

		$this->data['forgotten'] = $this->url->link('common/forgotten');

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (isset($_POST['username']) && isset($_POST['password']) && !$this->user->login($_POST['username'], $_POST['password'])) {
			if (!empty($_GET['response'])) {
				echo "FAILURE";
				exit;
			}

			$this->message->add('warning', _l("No match for Username and/or Password."));

			return false;
		}

		if (!empty($_GET['response'])) {
			echo "SUCCESS";
			exit;
		}

		return true;
	}
}
