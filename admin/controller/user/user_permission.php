<?php
class ControllerUserUserPermission extends Controller {
	
 
	public function index() {
		$this->load->language('user/user_group');
 
		$this->document->setTitle($this->_('heading_title'));
 		
		$this->getList();
	}

	public function insert() {
		$this->load->language('user/user_group');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_user_user_group->addUserGroup($_POST);
			
			$this->message->add('success', $this->_('text_success'));

			$url = '';

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}
			
			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}
						
			$this->redirect($this->url->link('user/user_permission', $url));
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('user/user_group');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_user_user_group->editUserGroup($_GET['user_group_id'], $_POST);
			
			$this->message->add('success', $this->_('text_success'));
			
			$url = '';

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}
						
			$this->redirect($this->url->link('user/user_permission', $url));
		}

		$this->getForm();
	}

	public function delete() { 
		$this->load->language('user/user_group');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
      		foreach ($_POST['selected'] as $user_group_id) {
				$this->model_user_user_group->deleteUserGroup($user_group_id);	
			}
						
			$this->message->add('success', $this->_('text_success'));
			
			$url = '';

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}
						
			$this->redirect($this->url->link('user/user_permission', $url));
		}

		$this->getList();
	}

	private function getList() {
$this->template->load('user/user_group_list');

		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'name';
		}
		 
		if (isset($_GET['order'])) {
			$order = $_GET['order'];
		} else {
			$order = 'ASC';
		}
		
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
		
		$url = '';
		
		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}	
	
		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}
			
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('user/user_permission', $url));

		$this->data['insert'] = $this->url->link('user/user_permission/insert', $url);
		$this->data['delete'] = $this->url->link('user/user_permission/delete', $url);	
	
		$this->data['user_groups'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$user_group_total = $this->model_user_user_group->getTotalUserGroups();
		
		$results = $this->model_user_user_group->getUserGroups($data);

		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('user/user_permission/update', 'user_group_id=' . $result['user_group_id'] . $url)
			);		
		
			$this->data['user_groups'][] = array(
				'user_group_id' => $result['user_group_id'],
				'name'          => $result['name'],
				'selected'      => isset($_POST['selected']) && in_array($result['user_group_id'], $_POST['selected']),
				'action'        => $action
			);
		}	
	
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$this->data['sort_name'] = $this->url->link('user/user_permission', 'sort=name' . $url);
		
		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}
												
		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}
				
		$this->pagination->init();
		$this->pagination->total = $user_group_total;
		$this->pagination->page = $page;
		$this->pagination->limit = $this->config->get('config_admin_limit');
		$this->pagination->text = $this->_('text_pagination');
		$this->pagination->url = $this->url->link('user/user_permission', $url . '&page={page}');
		
		$this->data['pagination'] = $this->pagination->render();				

		$this->data['sort'] = $sort; 
		$this->data['order'] = $order;

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
 	}

	private function getForm() {
$this->template->load('user/user_group_form');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
 		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = '';
		}

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}
			
		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}
		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('user/user_permission', $url));

		if (!isset($_GET['user_group_id'])) {
			$this->data['action'] = $this->url->link('user/user_permission/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('user/user_permission/update', 'user_group_id=' . $_GET['user_group_id'] . $url);
		}
		  
    	$this->data['cancel'] = $this->url->link('user/user_permission', $url);

		if (isset($_GET['user_group_id']) && $_SERVER['REQUEST_METHOD'] != 'POST') {
			$user_group_info = $this->model_user_user_group->getUserGroup($_GET['user_group_id']);
		}

		if (isset($_POST['name'])) {
			$this->data['name'] = $_POST['name'];
		} elseif (!empty($user_group_info)) {
			$this->data['name'] = $user_group_info['name'];
		} else {
			$this->data['name'] = '';
		}
		
		$ignore = array(
			'common/home',
			'common/startup',
			'common/login',
			'common/logout',
			'common/forgotten',
			'common/reset',			
			'error/not_found',
			'error/permission',
			'common/footer',
			'common/header'
		);
				
		$this->data['permissions'] = array();
		
		$files = glob(DIR_APPLICATION . 'controller/*/*.php');
		
		foreach ($files as $file) {
			$data = explode('/', dirname($file));
			
			$permission = end($data) . '/' . basename($file, '.php');
			
			if (!in_array($permission, $ignore)) {
				$this->data['permissions'][] = $permission;
			}
		}
		
		if (isset($_POST['permission']['access'])) {
			$this->data['access'] = $_POST['permission']['access'];
		} elseif (isset($user_group_info['permission']['access'])) {
			$this->data['access'] = $user_group_info['permission']['access'];
		} else { 
			$this->data['access'] = array();
		}

		if (isset($_POST['permission']['modify'])) {
			$this->data['modify'] = $_POST['permission']['modify'];
		} elseif (isset($user_group_info['permission']['modify'])) {
			$this->data['modify'] = $user_group_info['permission']['modify'];
		} else { 
			$this->data['modify'] = array();
		}
	
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'user/user_permission')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 64)) {
			$this->error['name'] = $this->_('error_name');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'user/user_permission')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		foreach ($_POST['selected'] as $user_group_id) {
			$user_total = $this->model_user_user->getTotalUsersByGroupId($user_group_id);

			if ($user_total) {
				$this->error['warning'] = sprintf($this->_('error_user'), $user_total);
			}
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}