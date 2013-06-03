<?php
class Admin_Controller_User_User extends Controller 
{
	
	
  	public function index()
  	{
		$this->load->language('user/user');

		$this->document->setTitle($this->_('heading_title'));
	
		$this->getList();
  	}
	
  	public function insert()
  	{
		$this->load->language('user/user');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->Model_User_User->addUser($_POST);
			
			if($this->user->isAdmin())
				$this->message->add('success', $this->_('text_success'));
			else
				$this->message->add('success', $this->_('text_success_portal'));
			
			$url = $this->get_url();
						
			if($this->user->isAdmin())
				$this->url->redirect($this->url->link('user/user', $url));
			else
				$this->url->redirect($this->url->link('common/home', $url));
		}
	
		$this->getForm();
  	}

  	public function update()
  	{
		$this->load->language('user/user');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->Model_User_User->editUser($_GET['user_id'], $_POST);
			
			$url = $this->get_url();
			
			if ($this->user->isDesigner()) {
				$this->message->add('success', $this->_('text_success_portal'));
				$this->url->redirect($this->url->link('common/home', $url));
			}
			else {
				$this->message->add('success', $this->_('text_success'));
				$this->url->redirect($this->url->link('user/user', $url));
			}
		}
	
		$this->getForm();
  	}
 
  	public function delete()
  	{
		$this->load->language('user/user');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
				foreach ($_POST['selected'] as $user_id) {
				$this->Model_User_User->deleteUser($user_id);
			}

			if($this->user->isAdmin())
				$this->message->add('success', $this->_('text_success'));
			else
				$this->message->add('success', $this->_('text_success_portal'));
			
			$url = $this->get_url();
			
			$this->url->redirect($this->url->link('user/user', $url));
		}
	
		$this->getList();
  	}

  	private function getList()
  	{
  		if ($this->user->isDesigner()) {
  			$this->url->redirect($this->url->link('common/home'));
		}
		
		$this->template->load('user/user_list');

  		$url_items = array('sort'=>'username','order'=>'ASC','page'=>1);
		foreach($url_items as $item=>$default)
			$$item = isset($_GET[$item])?$_GET[$item]:$default;
			
		$url = $this->get_url();
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('user/user'));
		
		$this->data['insert'] = $this->url->link('user/user/insert', $url);
		$this->data['delete'] = $this->url->link('user/user/delete', $url);
			
		$this->data['users'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$user_total = $this->Model_User_User->getTotalUsers();
		
		$results = $this->Model_User_User->getUsers($data);
		
		foreach ($results as &$result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('user/user/update', 'user_id=' . $result['user_id'] . $url)
			);
			
			$result['status']	= $result['status'] ? $this->_('text_enabled') : $this->_('text_disabled');
			$result['date_added'] = $this->tool->format_datetime($result['date_added'], $this->language->getInfo('date_format_short'));
			$result['selected']	= isset($_POST['selected']) && in_array($result['user_id'], $_POST['selected']);
			$result['action']	= $action;
		}unset($result);
		
		$this->data['users'] = $results;
		
		$url = $order == 'ASC' ? '&order=DESC' : '&order=ASC';
		
		$url .= $this->get_url(array('page'));
					
		$sort_by = array('username','email','status','date_added');
		foreach($sort_by as $s)
			$this->data['sort_'.$s] = $this->url->link('user/user', 'sort=' . $s . $url);
		
		$url = $this->get_url(array('sort','order'));
				
		$this->pagination->init();
		$this->pagination->total = $user_total;
		$this->data['pagination'] = $this->pagination->render();
								
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
  	}
	
	private function getForm()
	{
		if ($this->user->isDesigner()) {
			$this->template->load('user/user_form_restricted');
		}
		else {
			$this->template->load('user/user_form');
		}

		$user_id =  isset($_GET['user_id'])? $_GET['user_id']:null;
		
		$this->verify_user();
		
		$url = $this->get_url();
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		
		if (!$this->user->isDesigner()) {
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('user/user'));
		}
		
		if (!$user_id) {
			$this->data['action'] = $this->url->link('user/user/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('user/user/update', 'user_id=' .$user_id . $url);
		}
		
		$this->data['cancel'] = $this->url->link('user/user', $url);

		if ($user_id && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
			$user_info = $this->Model_User_User->getUser($user_id);
		}
		
		$data_items = array('username'=>'','password'=>'','confirm'=>'','firstname'=>'','lastname'=>'',
								'email'=>'','designers'=>array(),'user_group_id'=>12,'status'=>0,
								);
		$no_fill = array('confirm','password','designers');
		
		foreach ($data_items as $item=>$default) {
			if (isset($_POST[$item]))
				$this->data[$item] = $_POST[$item];
			elseif (!empty($user_info) && !in_array($item,$no_fill))
				$this->data[$item] = $user_info[$item];
			else
				$this->data[$item] = $default;
		}
		
		if (!empty($user_info) && !isset($_POST['designers'])) {
			$this->data['designers'] = array();
			foreach($this->Model_User_User->getUserDesigners($user_id) as $d)
			$this->data['designers'][] = $d['designer_id'];
		}
		
		$manufacturers = $this->Model_Catalog_Manufacturer->getManufacturers();
		foreach($manufacturers as $m)
			$this->data['manufacturers'][$m['manufacturer_id']] = $m['name'];
		
		$this->data['user_groups'] = $this->Model_User_UserGroup->getUserGroups();
		
		$this->data['contact_template'] = $this->getChild('includes/contact',array('type'=>'user', 'id'=>$user_id));
		
		
		if (!$user_id) {
			$this->breadcrumb->add($this->_('text_new_user'), $this->url->link('user/user/insert'));
		}
		else {
			$this->breadcrumb->add($this->data['username'], $this->url->link('user/user/update', 'user_id=' . $user_id));
		}
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
  	}
  	
  	private function validateForm()
  	{
  		$this->verify_user();
		if (!$this->user->hasPermission('modify', 'user/user')) {
				$this->error['warning'] = $this->_('error_permission');
		}
		
		if ($this->user->isAdmin()) {
			if ((strlen($_POST['username']) < 3) || (strlen($_POST['username']) > 20)) {
					$this->error['username'] = $this->_('error_username');
			}

			$user_info = $this->Model_User_User->getUserByUsername($_POST['username']);
			
			if (!isset($_GET['user_id'])) {
				if ($user_info) {
					$this->error['warning'] = $this->_('error_exists');
				}
			} else {
				if ($user_info && ($_GET['user_id'] != $user_info['user_id'])) {
					$this->error['warning'] = $this->_('error_exists');
				}
			}
		}
		
		if ((strlen($_POST['firstname']) < 1) || (strlen($_POST['firstname']) > 32)) {
			$this->error['firstname'] = $this->_('error_firstname');
		}

		if ((strlen($_POST['lastname']) < 1) || (strlen($_POST['lastname']) > 32)) {
				$this->error['lastname'] = $this->_('error_lastname');
		}

		if ($_POST['password'] || (!isset($_GET['user_id']))) {
				if ((strlen($_POST['password']) < 4) || (strlen($_POST['password']) > 20)) {
				$this->error['password'] = $this->_('error_password');
				}
	
			if ($_POST['password'] != $_POST['confirm']) {
				$this->error['confirm'] = $this->_('error_confirm');
			}
		}
		
		if ($this->user->isAdmin()) {
			//if this is a Designer user
			if ($_POST['user_group_id'] == 12) {
				if (!isset($_POST['designers'])) {
					$this->error['no_designer'] = $this->_('error_no_designer');
				}
			
				if (!isset($_POST['contact'])) {
					$this->error['no_contact'] = $this->_("error_no_contact");
				}
			}
		}
	
		return $this->error ? false : true;
  	}

  	private function validateDelete()
  	{
  		$this->verify_user(0);
		
		if (!$this->user->hasPermission('modify', 'user/user')) {
				$this->error['warning'] = $this->_('error_permission');
		}
		
		foreach ($_POST['selected'] as $user_id) {
			if ($this->user->getId() == $user_id) {
				$this->error['warning'] = $this->_('error_account');
			}
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
  	}
	
	private function verify_user($user_id = null)
	{
		if ($this->user->isDesigner()) {
			$user_id = isset($user_id) ? $user_id : (isset($_GET['user_id']) ? $_GET['user_id'] : 0);
			if ($user_id != $this->user->getId()) {
				$this->message->add('warning', $this->_('error_wrong_user'));
				$this->url->redirect($this->url->link("common/home"));
			}
		}
	}
	
	private function get_url($override=array()){
		$url = '';
		$filters = !empty($override)?$override:array('sort', 'order', 'page');
		foreach($filters as $f)
			if (isset($_GET[$f]))
				$url .= "&$f=" . $_GET[$f];
		return $url;
	}
}