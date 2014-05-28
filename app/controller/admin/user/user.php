<?php

class App_Controller_Admin_User_User extends Controller
{
	static $allow = array(
		'modify' => array(
			'form',
			'update',
			'delete',
			'batch_action',
		),
	);

	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("User"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("User"), site_url('admin/user/user'));

		//Listing
		$data['listing'] = $this->listing();

		//Batch Actions
		$actions = array(
			'enable'  => array(
				'label' => _l("Enable")
			),
			'disable' => array(
				'label' => _l("Disable"),
			),
			'delete'  => array(
				'label' => _l("Delete"),
			),
		);

		$data['batch_action'] = array(
			'actions' => $actions,
			'url'     => site_url('admin/user/user/batch_action'),
		);

		//Actions
		$data['insert'] = site_url('admin/user/user/form');

		//Response
		$this->response->setOutput($this->render('user/user_list', $data));
	}

	public function listing()
	{
		//The Table Columns
		$columns = array();

		$columns['username'] = array(
			'type'         => 'text',
			'display_name' => _l("Username"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['lastname'] = array(
			'type'         => 'text',
			'display_name' => _l("Name"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['status'] = array(
			'type'         => 'text',
			'display_name' => _l("Status"),
			'build_data'   => array(
				0 => _l("Disabled"),
				1 => _l("Enabled"),
			),
			'filter'       => true,
			'sortable'     => true,
		);

		//The Sort & Filter Data
		$sort   = $this->sort->getQueryDefaults('username', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		$user_total = $this->Model_User_User->getTotalUsers($filter);
		$users      = $this->Model_User_User->getUsers($sort + $filter);

		foreach ($users as &$user) {
			$actions = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/user/user/form', 'user_id=' . $user['user_id'])
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/user/user/delete', 'user_id=' . $user['user_id'])
				),
			);

			$user['actions'] = $actions;

			if (!$user['lastname']) {
				$user['lastname'] = $user['firstname'] ? $user['firstname'] : _l("No Name");
			} elseif ($user['firstname']) {
				$user['lastname'] .= ', ' . $user['firstname'];
			}
		}
		unset($user);

		$listing = array(
			'row_id'         => 'user_id',
			'columns'        => $columns,
			'rows'           => $users,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $user_total,
			'listing_path'   => 'admin/user/user/listing',
		);

		$output = block('widget/listing', null, $listing);

		//Response
		if ($this->request->isAjax()) {
			$this->response->setOutput($output);
		}

		return $output;
	}

	public function form()
	{
		//Page Head
		$this->document->setTitle(_l("User Information"));

		//Insert or Update
		$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("User"), site_url('admin/user/user'));
		$this->breadcrumb->add($user_id ? _l("Update") : _l("New"), site_url('admin/user/user/form', 'user_id=' . $user_id));

		//The Data
		$user = $_POST;

		if ($user_id && !$this->request->isPost()) {
			$user = $this->Model_User_User->getUser($user_id);
		}

		$defaults = array(
			'username'     => '',
			'firstname'    => '',
			'lastname'     => '',
			'email'        => '',
			'user_role_id' => option('config_default_user_role', 12),
			'status'       => 1,
		);

		$user += $defaults;

		$user['data_user_roles'] = $this->Model_User_Role->getRoles();

		$user['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		//Actions
		$user['save'] = site_url('admin/user/user/update', 'user_id=' . $user_id);

		//Response
		$this->response->setOutput($this->render('user/user_form', $user));
	}

	public function update()
	{
		//Insert
		if (empty($_GET['user_id'])) {
			$this->Model_User_User->add($_POST);
		} //Update
		else {
			$this->Model_User_User->edit($_GET['user_id'], $_POST);
		}

		if ($this->Model_User_User->hasError()) {
			$this->message->add('error', $this->Model_User_User->getError());
		} else {
			$this->message->add('success', _l("The Page has been updated successfully!"));
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} elseif ($this->message->has('error')) {
			$this->form();
		} else {
			redirect('admin/user/user');
		}
	}

	public function delete()
	{
		$this->Model_User_User->remove($_GET['user_id']);

		if ($this->Model_User_User->hasError()) {
			$this->message->add('error', $this->Model_User_User->getError());
		} else {
			$this->message->add('notify', _l("User was deleted!"));
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} else {
			redirect('admin/user/user');
		}
	}

	public function batch_action()
	{
		foreach ($_POST['batch'] as $user_id) {
			switch ($_POST['action']) {
				case 'enable':
					$this->Model_User_User->edit($user_id, array('status' => 1));
					break;

				case 'disable':
					$this->Model_User_User->edit($user_id, array('status' => 0));
					break;

				case 'delete':
					$this->Model_User_User->remove($user_id);
					break;
			}
		}

		if ($this->Model_User_User->hasError()) {
			$this->message->add('error', $this->Model_User_User->getError());
		} else {
			$this->message->add('success', _l("Success: You have modified navigation!"));
		}

		if ($this->request->isAjax()) {
			$this->listing();
		} else {
			redirect('admin/user/user');
		}
	}
}
