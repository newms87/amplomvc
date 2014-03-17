<?php
class Admin_Controller_User_UserPermission extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("User Permissions"));

		$this->getList();
	}

	public function insert()
	{
		$this->document->setTitle(_l("User Permissions"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_User_UserGroup->addUserGroup($_POST);

			$this->message->add('success', _l("You have successfully modified user permissions"));

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

			$this->url->redirect('user/user_permission', $url);
		}

		$this->getForm();
	}

	public function update()
	{
		$this->document->setTitle(_l("Update User Permissions"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_User_UserGroup->editUserGroup($_GET['user_group_id'], $_POST);

			$this->message->add('success', _l("You have successfully modified user permissions"));

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

			$this->url->redirect('user/user_permission', $url);
		}

		$this->getForm();
	}

	public function delete()
	{
		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $user_group_id) {
				$this->Model_User_UserGroup->deleteUserGroup($user_group_id);
			}

			$this->message->add('success', _l("You have successfully removed the user group"));
		}

		$this->url->redirect('user/user_permission');
	}

	private function getList()
	{
		$this->view->load('user/user_group_list');

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

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("User Permissions"), $this->url->link('user/user_permission', $url));

		$this->data['insert'] = $this->url->link('user/user_permission/insert', $url);
		$this->data['delete'] = $this->url->link('user/user_permission/delete', $url);

		$this->data['user_groups'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		$user_group_total = $this->Model_User_UserGroup->getTotalUserGroups();

		$results = $this->Model_User_UserGroup->getUserGroups($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("Edit"),
				'href' => $this->url->link('user/user_permission/update', 'user_group_id=' . $result['user_group_id'] . $url)
			);

			$this->data['user_groups'][] = array(
				'user_group_id' => $result['user_group_id'],
				'name'          => $result['name'],
				'selected'      => isset($_GET['selected']) && in_array($result['user_group_id'], $_GET['selected']),
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
		$this->pagination->total  = $user_group_total;
		$this->data['pagination'] = $this->pagination->render();

		$this->data['sort']  = $sort;
		$this->data['order'] = $order;

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		$this->view->load('user/user_group_form');

		$user_group_id = !empty($_GET['user_group_id']) ? (int)$_GET['user_group_id'] : 0;

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("User Permissions"), $this->url->link('user/user_permission'));

		$url_query = $this->url->getQuery('sort', 'order', 'page');

		if ($user_group_id) {
			$this->data['action'] = $this->url->link('user/user_permission/update', 'user_group_id=' . $user_group_id . $url_query);
		} else {
			$this->data['action'] = $this->url->link('user/user_permission/insert', $url_query);
		}

		$this->data['cancel'] = $this->url->link('user/user_permission', $url_query);

		if ($user_group_id && !$this->request->isPost()) {
			$user_group_info = $this->Model_User_UserGroup->getUserGroup($user_group_id);
		}

		//initialize the values in order of Post, Database, Default
		$defaults = array(
			'name'        => '',
			'permissions' => array(),
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($user_group_info[$key])) {
				$this->data[$key] = $user_group_info[$key];
			} elseif (!$user_group_id) {
				$this->data[$key] = $default;
			}
		}

		if (!isset($this->data['permissions']['access'])) {
			$this->data['permissions']['access'] = array();
		}

		if (!isset($this->data['permissions']['modify'])) {
			$this->data['permissions']['modify'] = array();
		}

		$this->data['data_controllers'] = $this->Model_User_UserGroup->get_controller_list();

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'user/user_permission')) {
			$this->error['warning'] = _l("You do not have permission to modify User Permissions");
		}

		if (!$this->validation->text($_POST['name'], 3, 64)) {
			$this->error['name'] = _l("Group Name must be between 3 and 64 characters");
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'user/user_permission')) {
			$this->error['warning'] = _l("You do not have permission to modify User Permissions");
		}

		foreach ($_GET['selected'] as $user_group_id) {
			$user_total = $this->Model_User_User->getTotalUsersByGroupId($user_group_id);

			if ($user_total) {
				$this->error['warning'] = _l("This user group is associated to %s users.", $user_total);
			}
		}

		return $this->error ? false : true;
	}
}
