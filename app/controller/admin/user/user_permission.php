<?php
class App_Controller_Admin_User_UserPermission extends Controller
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

			redirect('admin/user/user_permission', $url);
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

			redirect('admin/user/user_permission', $url);
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

		redirect('admin/user/user_permission');
	}

	private function getList()
	{
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

		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("User Permissions"), site_url('admin/user/user_permission', $url));

		$data['insert'] = site_url('admin/user/user_permission/insert', $url);
		$data['delete'] = site_url('admin/user/user_permission/delete', $url);

		$data['user_groups'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * option('config_admin_limit'),
			'limit' => option('config_admin_limit')
		);

		$user_group_total = $this->Model_User_UserGroup->getTotalUserGroups();

		$results = $this->Model_User_UserGroup->getUserGroups($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("Edit"),
				'href' => site_url('admin/user/user_permission/update', 'user_group_id=' . $result['user_group_id'] . $url)
			);

			$data['user_groups'][] = array(
				'user_group_id' => $result['user_group_id'],
				'name'          => $result['name'],
				'selected'      => isset($_GET['selected']) && in_array($result['user_group_id'], $_GET['selected']),
				'action'        => $action
			);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if ($this->session->has('success')) {
			$data['success'] = $this->session->get('success');

			$this->session->remove('success');
		} else {
			$data['success'] = '';
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

		$data['sort_name'] = site_url('admin/user/user_permission', 'sort=name' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total  = $user_group_total;
		$data['pagination'] = $this->pagination->render();

		$data['sort']  = $sort;
		$data['order'] = $order;

		$this->response->setOutput($this->render('user/user_group_list', $data));
	}

	private function getForm()
	{
		$user_group_id = !empty($_GET['user_group_id']) ? (int)$_GET['user_group_id'] : 0;

		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("User Permissions"), site_url('admin/user/user_permission'));

		$url_query = $this->url->getQuery('sort', 'order', 'page');

		if ($user_group_id) {
			$data['action'] = site_url('admin/user/user_permission/update', 'user_group_id=' . $user_group_id . $url_query);
		} else {
			$data['action'] = site_url('admin/user/user_permission/insert', $url_query);
		}

		$data['cancel'] = site_url('admin/user/user_permission', $url_query);

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
				$data[$key] = $_POST[$key];
			} elseif (isset($user_group_info[$key])) {
				$data[$key] = $user_group_info[$key];
			} elseif (!$user_group_id) {
				$data[$key] = $default;
			}
		}

		if (!isset($data['permissions']['access'])) {
			$data['permissions']['access'] = array();
		}

		if (!isset($data['permissions']['modify'])) {
			$data['permissions']['modify'] = array();
		}

		$data['data_controllers'] = $this->Model_User_UserGroup->get_controller_list();

		$this->response->setOutput($this->render('user/user_group_form', $data));
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'user/user_permission')) {
			$this->error['warning'] = _l("You do not have permission to modify User Permissions");
		}

		if (!$this->validation->text($_POST['name'], 3, 64)) {
			$this->error['name'] = _l("Group Name must be between 3 and 64 characters");
		}

		return empty($this->error);
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

		return empty($this->error);
	}
}
