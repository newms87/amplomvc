<?php
class Catalog_Controller_Account_Login extends Controller
{
	public function index()
	{
		$this->template->load('account/login');

		//Resolve Redirect
		$redirect = !empty($_REQUEST['redirect']) ? str_replace('&amp;', '&', $_REQUEST['redirect']) : $this->url->link('account/account');

		// Login override for admin users
		if (!empty($_COOKIE['customer_token'])) {
			$this->customer->logout();

			$customer_info = $this->System_Model_Customer->getCustomerByToken($_COOKIE['customer_token']);

			if ($customer_info && $this->customer->login($customer_info['email'], '', true)) {
				$this->url->redirect($redirect);
			}
		}

		if ($this->customer->isLogged()) {
			$this->url->redirect($redirect);
		}

		$this->language->load('account/login');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && $this->validate()) {
			$this->url->redirect($redirect);
		}

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
		$this->breadcrumb->add($this->_('text_login'), $this->url->link('account/login'));

		//Action Buttons
		$this->data['login']    = $this->url->link('account/login');
		$this->data['register']  = $this->url->link('account/register');
		$this->data['forgotten'] = $this->url->link('account/forgotten');

		if (!empty($_POST['redirect'])) {
			$this->data['redirect'] = $_POST['redirect'];
		} elseif (isset($_GET['redirect'])) {
			$this->data['redirect'] = $_GET['redirect'];
		} elseif (isset($this->session->data['redirect'])) {
			$this->data['redirect'] = $this->session->data['redirect'];
			unset($this->session->data['redirect']);
		}

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
		if (!$this->customer->login($_POST['email'], $_POST['password'])) {
			$this->error['warning'] = $this->_('error_login');
		}

		//Verify redirect stays on our site for security purposes
		if (isset($_POST['redirect']) && strpos($_POST['redirect'], SITE_URL) !== 0 && strpos($_POST['redirect'], SITE_SSL) !== 0) {
			$this->error['warning'] = $this->_('error_redirect_domain');
			unset($_POST['redirect']);
		}

		return $this->error ? false : true;
	}
}
