<?php
class ControllerCommonReset extends Controller {
	
	
	public function index() {
		if ($this->user->isLogged()) {
			$this->url->redirect($this->url->link('common/home'));
		}
				
		if (isset($_GET['code'])) {
			$code = $_GET['code'];
		} else {
			$code = '';
		}
		
		$user_info = $this->model_user_user->getUserByCode($code);
		
		if ($user_info) {
		$this->template->load('common/reset');

			$this->load->language('common/reset');
			
			if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
				$this->model_user_user->editPassword($user_info['user_id'], $_POST['password']);
	
				$this->message->add('success', $this->_('text_success'));
		
				$this->url->redirect($this->url->link('common/login'));
			}
			
				$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
				$this->breadcrumb->add($this->_('text_reset'), $this->url->link('common/reset'));

			if (isset($this->error['password'])) { 
				$this->data['error_password'] = $this->error['password'];
			} else {
				$this->data['error_password'] = '';
			}
	
			if (isset($this->error['confirm'])) { 
				$this->data['error_confirm'] = $this->error['confirm'];
			} else {
				$this->data['error_confirm'] = '';
			}
			
			$this->data['action'] = $this->url->link('common/reset', 'code=' . $code);
	
			$this->data['cancel'] = $this->url->link('common/login');
			
			if (isset($_POST['password'])) {
				$this->data['password'] = $_POST['password'];
			} else {
				$this->data['password'] = '';
			}
	
			if (isset($_POST['confirm'])) {
				$this->data['confirm'] = $_POST['confirm'];
			} else {
				$this->data['confirm'] = '';
			}
			
			$this->children = array(
				'common/header',
				'common/footer'
			);
									
			$this->response->setOutput($this->render());						
		} else {
			return $this->forward('common/login');
		}
	}

	private function validate() {
		if ((strlen($_POST['password']) < 4) || (strlen($_POST['password']) > 20)) {
				$this->error['password'] = $this->_('error_password');
		}

		if ($_POST['confirm'] != $_POST['password']) {
				$this->error['confirm'] = $this->_('error_confirm');
		}  

		return $this->error ? false : true;
	}
}