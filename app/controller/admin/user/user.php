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
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("User"), site_url('admin/user/user'));

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
		$requested_cols = $this->request->get('columns');

		$columns = $this->Model_User_User->getColumns($requested_cols);

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
			'extra_cols'     => $this->Model_User_User->getColumns(false),
			'columns'        => $columns,
			'rows'           => $users,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $user_total,
			'listing_path'   => 'admin/user/user/listing',
			'save_path'      => 'admin/user/user/save',
		);

		$output = block('widget/listing', null, $listing);

		//Response
		if (IS_AJAX) {
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
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("User"), site_url('admin/user/user'));
		breadcrumb($user_id ? _l("Update") : _l("New"), site_url('admin/user/user/form', 'user_id=' . $user_id));

		//The Data
		$user = $_POST;

		if ($user_id && !IS_POST) {
			$user = $this->Model_User_User->getUser($user_id);

			$user['meta'] = $this->Model_User_User->getMeta($user_id);
		}

		$defaults = array(
			'username'     => '',
			'firstname'    => '',
			'lastname'     => '',
			'email'        => '',
			'user_role_id' => option('config_default_user_role', 12),
			'status'       => 1,
			'meta'         => array(),
		);

		$user += $defaults;

		$user['data_user_roles'] = $this->Model_User_Role->getRoles();

		$user['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		$user['meta']['__ac_template__'] = array(
			'key'   => 'my-key',
			'value' => '',
		);

		//Actions
		$user['save'] = site_url('admin/user/user/save', 'user_id=' . $user_id);

		//Response
		output($this->render('user/user_form', $user));
	}

	public function save()
	{
		if ($this->Model_User_User->save(_request('user_id'), $_POST)) {
			message('success', _l("The User has been updated successfully!"));
		} else {
			message('error', $this->Model_User_User->getError());
		}

		if (IS_AJAX) {
			output($this->message->toJSON());
		} elseif ($this->message->has('error')) {
			$this->form();
		} else {
			redirect('admin/user/user');
		}
	}

	public function delete()
	{
		if ($this->Model_User_User->remove(_get('user_id'))) {
			message('success', _l("User was deleted!"));
		} else {
			message('error', $this->Model_User_User->getError());
		}

		if (IS_AJAX) {
			output($this->message->toJSON());
		} else {
			redirect('admin/user/user');
		}
	}

	public function batch_action()
	{
		foreach (_post('batch', array()) as $user_id) {
			switch ($_POST['action']) {
				case 'enable':
					$this->Model_User_User->save($user_id, array('status' => 1));
					break;

				case 'disable':
					$this->Model_User_User->save($user_id, array('status' => 0));
					break;

				case 'delete':
					$this->Model_User_User->remove($user_id);
					break;
			}
		}

		if ($this->Model_User_User->hasError()) {
			message('error', $this->Model_User_User->getError());
		} else {
			message('success', _l("Users were updated successfully!"));
		}

		if (IS_AJAX) {
			$this->listing();
		} else {
			redirect('admin/user/user');
		}
	}
}
