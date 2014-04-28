<?php
class Admin_Controller_User_User extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("User"));

		$this->getList();
	}

	public function insert()
	{
		$this->document->setTitle(_l("User"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_User_User->addUser($_POST);

			if ($this->user->isAdmin()) {
				$this->message->add('success', _l("Success: You have modified users!"));
			} else {
				$this->message->add('success', _l("Success: You have updated your account!"));
			}

			$url = $this->get_url();

			if ($this->user->isAdmin()) {
				redirect('user/user', $url);
			} else {
				redirect('common/home', $url);
			}
		}

		$this->getForm();
	}

	public function update()
	{
		$this->document->setTitle(_l("User"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_User_User->editUser($_GET['user_id'], $_POST);

			$url = $this->get_url();

			$this->message->add('success', _l("Success: You have modified users!"));
			redirect('user/user', $url);
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->document->setTitle(_l("User"));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $user_id) {
				$this->Model_User_User->deleteUser($user_id);
			}

			if ($this->user->isAdmin()) {
				$this->message->add('success', _l("Success: You have modified users!"));
			} else {
				$this->message->add('success', _l("Success: You have updated your account!"));
			}

			$url = $this->get_url();

			redirect('user/user', $url);
		}

		$this->getList();
	}

	private function getList()
	{
		$url_items = array(
			'sort'  => 'username',
			'order' => 'ASC',
			'page'  => 1
		);
		foreach ($url_items as $item => $default) {
			$$item = isset($_GET[$item]) ? $_GET[$item] : $default;
		}

		$url = $this->get_url();

		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("User"), site_url('user/user'));

		$data['insert'] = site_url('user/user/insert', $url);
		$data['delete'] = site_url('user/user/delete', $url);

		$data['users'] = array();

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
				'text' => _l("Edit"),
				'href' => site_url('user/user/update', 'user_id=' . $result['user_id'] . $url)
			);

			$result['status']     = $result['status'] ? _l("Enabled") : _l("Disabled");
			$result['date_added'] = $this->date->format($result['date_added'], 'short');
			$result['selected']   = isset($_GET['selected']) && in_array($result['user_id'], $_GET['selected']);
			$result['action']     = $action;
		}
		unset($result);

		$data['users'] = $results;

		$url = $order == 'ASC' ? '&order=DESC' : '&order=ASC';

		$url .= $this->get_url(array('page'));

		$sort_by = array(
			'username',
			'email',
			'status',
			'date_added'
		);
		foreach ($sort_by as $s) {
			$data['sort_' . $s] = site_url('user/user', 'sort=' . $s . $url);
		}

		$url = $this->get_url(array(
			'sort',
			'order'
		));

		$this->pagination->init();
		$this->pagination->total  = $user_total;
		$data['pagination'] = $this->pagination->render();

		$data['sort']  = $sort;
		$data['order'] = $order;

		$this->response->setOutput($this->render('user/user_list', $data));
	}

	private function getForm()
	{
		$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

		$url = $this->get_url();

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("User"), site_url('user/user'));

		if (!$user_id) {
			$data['action'] = site_url('user/user/insert', $url);
		} else {
			$data['action'] = site_url('user/user/update', 'user_id=' . $user_id . $url);
		}

		$data['cancel'] = site_url('user/user', $url);

		if ($user_id && !$this->request->isPost()) {
			$user_info = $this->Model_User_User->getUser($user_id);
		}

		$data_items = array(
			'username'      => '',
			'password'      => '',
			'confirm'       => '',
			'firstname'     => '',
			'lastname'      => '',
			'email'         => '',
			'designers'     => array(),
			'user_group_id' => 12,
			'status'        => 0,
		);
		$no_fill    = array(
			'confirm',
			'password',
			'designers'
		);

		foreach ($data_items as $item => $default) {
			if (isset($_POST[$item])) {
				$data[$item] = $_POST[$item];
			} elseif (!empty($user_info) && !in_array($item, $no_fill)) {
				$data[$item] = $user_info[$item];
			} else {
				$data[$item] = $default;
			}
		}

		$manufacturers = $this->Model_Catalog_Manufacturer->getManufacturers();
		foreach ($manufacturers as $m) {
			$data['manufacturers'][$m['manufacturer_id']] = $m['name'];
		}

		$data['user_groups'] = $this->Model_User_UserGroup->getUserGroups();

		$contact = array(
			'type' => 'user',
			'id'   => $user_id
		);


		if (!$user_id) {
			$this->breadcrumb->add(_l("Create New User"), site_url('user/user/insert'));
		} else {
			$this->breadcrumb->add($data['username'], site_url('user/user/update', 'user_id=' . $user_id));
		}

		$data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		$this->response->setOutput($this->render('user/user_form', $data));
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'user/user')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify users!");
		}

		if ($this->user->isAdmin()) {
			if ((strlen($_POST['username']) < 3) || (strlen($_POST['username']) > 20)) {
				$this->error['username'] = _l("Username must be between 3 and 20 characters!");
			}

			$user_info = $this->Model_User_User->getUserByUsername($_POST['username']);

			if (!isset($_GET['user_id'])) {
				if ($user_info) {
					$this->error['warning'] = _l("Warning: Username is already in use!");
				}
			} else {
				if ($user_info && ($_GET['user_id'] != $user_info['user_id'])) {
					$this->error['warning'] = _l("Warning: Username is already in use!");
				}
			}
		}

		if ((strlen($_POST['firstname']) < 1) || (strlen($_POST['firstname']) > 32)) {
			$this->error['firstname'] = _l("First Name must be between 1 and 32 characters!");
		}

		if ((strlen($_POST['lastname']) < 1) || (strlen($_POST['lastname']) > 32)) {
			$this->error['lastname'] = _l("Last Name must be between 1 and 32 characters!");
		}

		if ($_POST['password'] || (!isset($_GET['user_id']))) {
			if ((strlen($_POST['password']) < 4) || (strlen($_POST['password']) > 20)) {
				$this->error['password'] = _l("Password must be between 4 and 20 characters!");
			}

			if ($_POST['password'] !== $_POST['confirm']) {
				$this->error['confirm'] = _l("Password and Confirmation do not match!");
			}
		}

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'user/user')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify users!");
		}

		foreach ($_GET['selected'] as $user_id) {
			if ($this->user->getId() == $user_id) {
				$this->error['warning'] = _l("Warning: You can not delete your own account!");
			}
		}

		return empty($this->error);
	}

	private function get_url($override = array())
	{
		$url     = '';
		$filters = !empty($override) ? $override : array(
			'sort',
			'order',
			'page'
		);
		foreach ($filters as $f) {
			if (isset($_GET[$f])) {
				$url .= "&$f=" . $_GET[$f];
			}
		}
		return $url;
	}
}
