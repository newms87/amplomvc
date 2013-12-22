<?php
class Catalog_Controller_Account_Login extends Controller
{
	public function index()
	{
		//Redirect customer if logged in
		if ($this->customer->isLogged()) {
			if ($this->request->hasRedirect()) {
				$this->request->doRedirect();
			} else {
				$this->url->redirect('common/home');
			}
		}

		//Page Head
		$this->document->setTitle(_l("Account Login"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account/account'));
		$this->breadcrumb->add(_l("Login"), $this->url->link('account/login'));

		//Input Data
		$user_info = array();

		if ($this->request->isPost()) {
			$user_info = $_POST;
		}

		$defaults = array(
			'username' => '',
		);

		$this->data += $user_info + $defaults;

		//Template Data
		$this->data['gp_login'] = $this->Catalog_Model_Block_Login_Google->getConnectUrl();
		$this->data['fb_login'] = $this->Catalog_Model_Block_Login_Facebook->getConnectUrl();

		//Action Buttons
		$this->data['login']     = $this->url->link('account/login/login');
		$this->data['register']  = $this->url->link('account/register');
		$this->data['forgotten'] = $this->url->link('account/forgotten');

		//Resolve Redirect
		if (!empty($_REQUEST['redirect'])) {
			$this->request->setRedirect($_REQUEST['redirect']);
		} elseif (!$this->request->hasRedirect()) {
			$this->request->setRedirect('common/home');
		}

		//The Template
		$this->template->load('account/login');

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

	public function login()
	{
		if ($this->request->isPost()) {
			if (!$this->customer->login($_POST['username'], $_POST['password'])) {
				$this->message->add('warning', _l("Login failed. Invalid user name and / or password."));
			}
		}

		//Resolve Redirect
		if ($this->customer->isLogged()) {
			if ($this->request->hasRedirect()) {
				$this->request->doRedirect();
			} else {
				$this->url->redirect('account/account');
			}
		}

		$this->url->redirect('account/login');
	}
}
