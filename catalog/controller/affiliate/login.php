<?php
class ControllerAffiliateLogin extends Controller 
{
	
	
	public function index()
	{
		$this->template->load('affiliate/login');

		if ($this->affiliate->isLogged()) {
				$this->url->redirect($this->url->link('affiliate/account'));
		}
	
		$this->language->load('affiliate/login');

		$this->document->setTitle($this->_('heading_title'));
						
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['email']) && isset($_POST['password']) && $this->validate()) {
			if (isset($_POST['redirect'])) {
				$this->url->redirect(str_replace('&amp;', '&', $_POST['redirect']));
			} else {
				$this->url->redirect($this->url->link('affiliate/account'));
			}
		}
		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('affiliate/account'));
			$this->breadcrumb->add($this->_('text_login'), $this->url->link('affiliate/login'));

		$this->language->format('text_description', $this->config->get('config_name'), $this->config->get('config_name'), $this->config->get('config_commission') . '%');
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		$this->data['action'] = $this->url->link('affiliate/login');
		$this->data['register'] = $this->url->link('affiliate/register');
		$this->data['forgotten'] = $this->url->link('affiliate/forgotten');
		
		if (isset($_POST['redirect'])) {
			$this->data['redirect'] = $_POST['redirect'];
		} elseif (isset($this->session->data['redirect'])) {
				$this->data['redirect'] = $this->session->data['redirect'];
			
			unset($this->session->data['redirect']);
		} else {
			$this->data['redirect'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
	
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);
						
		$this->response->setOutput($this->render());
  	}
  
  	private function validate()
  	{
		if (!$this->affiliate->login($_POST['email'], $_POST['password'])) {
				$this->error['warning'] = $this->_('error_login');
		}
	
		return $this->error ? false : true;
  	}
}