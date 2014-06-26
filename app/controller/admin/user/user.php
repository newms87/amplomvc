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
		$this->breadcrumb->add(_l("Home"), site_url('admin'));
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
		output($this->render('user/user_list', $data));
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

		$columns['name'] = array(
			'type'         => 'text',
			'display_name' => _l("Name"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['status'] = array(
			'type'         => 'select',
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
		$filter = _get('filter', array());

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
				$user['name'] = $user['firstname'] ? $user['firstname'] : _l("No Name");
			} elseif ($user['firstname']) {
				$user['name'] = $user['lastname'] . ', ' . $user['firstname'];
			} else {
				$user['name'] = $user['lastname'];
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
		if (is_ajax()) {
			output($output);
		}

		return $output;
	}

	public function form()
	{
		//Page Head
		$this->document->setTitle(_l("User Information"));

		//Insert or Update
		$user_id = _get('user_id', null);

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("User"), site_url('admin/user/user'));
		$this->breadcrumb->add($user_id ? _l("Update") : _l("New"), site_url('admin/user/user/form', 'user_id=' . $user_id));

		//The Data
		$user = $_POST;

		if ($user_id && !is_post()) {
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
		output($this->render('user/user_form', $user));
	}

	public function update()
	{
		if ($this->Model_User_User->save(_get('user_id'), $_POST)) {
			$this->message->add('success', _l("The Page has been updated successfully!"));
		} else {
			$this->message->add('error', $this->Model_User_User->getError());
		}

		if (is_ajax()) {
			output($this->message->toJSON());
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

		if (is_ajax()) {
			output($this->message->toJSON());
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

		if (is_ajax()) {
			$this->listing();
		} else {
			redirect('admin/user/user');
		}
	}
}
