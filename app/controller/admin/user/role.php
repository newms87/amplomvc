<?php

class App_Controller_Admin_User_Role extends Controller
{
	static $allow = array(
		'modify' => array(
			'form',
			'listing',
			'update',
			'delete',
		),
	);

	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("User Roles"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("User Roles"), site_url('admin/user/role'));

		//Batch Actions
		$actions = array(
			'delete' => array(
				'label' => _l("Delete"),
			),
		);

		$data['batch_action'] = array(
			'actions' => $actions,
			'url'     => site_url('admin/user/role/batch_action'),
		);

		//The Listing
		$data['listing'] = $this->listing();

		//Actions
		$data['insert'] = site_url('admin/user/role/form');

		//Render
		output($this->render('user/role_list', $data));
	}

	public function listing()
	{
		//The Table Columns
		$columns = array();

		$columns['name'] = array(
			'type'         => 'text',
			'display_name' => _l("Name"),
			'filter'       => true,
			'sortable'     => true,
		);

		//The Sort & Filter Data
		$sort   = $this->sort->getQueryDefaults('name', 'ASC');
		$filter = _get('filter', array());

		$user_role_total = $this->Model_User_Role->getTotalRoles($filter);
		$user_roles      = $this->Model_User_Role->getRoles($sort + $filter);

		foreach ($user_roles as &$user_role) {
			$actions = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/user/role/form', 'user_role_id=' . $user_role['user_role_id'])
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/user/role/delete', 'user_role_id=' . $user_role['user_role_id'])
				),
			);

			$user_role['actions'] = $actions;
		}
		unset($user_role);

		$listing = array(
			'row_id'         => 'user_role_id',
			'columns'        => $columns,
			'rows'           => $user_roles,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $user_role_total,
			'listing_path'   => 'admin/user/role/listing',
		);

		$output = block('widget/listing', null, $listing);

		//Response
		if ($this->request->isAjax()) {
			output($output);
		}

		return $output;
	}

	public function form()
	{
		//Page Head
		$this->document->setTitle(_l("User Roles"));

		//Insert or Update
		$user_role_id = !empty($_GET['user_role_id']) ? (int)$_GET['user_role_id'] : 0;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("User Roles"), site_url('admin/user/role'));
		$this->breadcrumb->add($user_role_id ? _l("Update") : _l("New"), site_url('admin/user/role/form', 'user_role_id=' . $user_role_id));

		//The Data
		$user_role = $_POST;

		if ($user_role_id && !$this->request->isPost()) {
			$user_role = $this->Model_User_Role->getRole($user_role_id);
		}

		// Defaults
		$defaults = array(
			'name'        => '',
			'permissions' => array(
				'access' => array(),
				'modify' => array(),
			),
		);

		$user_role += $defaults;

		//Template Data
		$user_role['data_controllers'] = $this->Model_User_Role->getControllers();

		//Actions
		$user_role['save'] = site_url('admin/user/role/update', 'user_role_id=' . $user_role_id);

		//Render
		output($this->render('user/role_form', $user_role));
	}

	public function update()
	{
		//Insert
		if (empty($_GET['user_role_id'])) {
			$this->Model_User_Role->add($_POST);
		} //Update
		else {
			$this->Model_User_Role->edit($_GET['user_role_id'], $_POST);
		}

		//Success / Error
		if ($this->Model_User_Role->hasError()) {
			$this->message->add('error', $this->Model_User_Role->getError());
		} else {
			$this->message->add('success', _l("The User Role has been updated!"));
		}

		//Response
		if ($this->request->isAjax()) {
			output($this->message->toJSON());
		} elseif ($this->message->has('error')) {
			$this->form();
		} else {
			redirect('admin/user/role');
		}
	}

	public function delete()
	{
		//Delete
		$this->Model_User_Role->deleteUserRole($_GET['category_id']);

		//Success / Error
		if ($this->Model_User_Role->hasError()) {
			$this->message->add('error', $this->Model_User_Role->getError());
		} else {
			$this->message->add('notify', _l("User Role was deleted!"));
		}

		//Response
		if ($this->request->isAjax()) {
			output($this->message->toJSON());
		} else {
			redirect('admin/user/role');
		}
	}

	public function batch_action()
	{
		foreach ($_POST['batch'] as $user_role_id) {
			switch ($_POST['action']) {
				case 'delete':
					$this->Model_User_Role->remove($user_role_id);
					break;
			}
		}

		if ($this->Model_User_Role->hasError()) {
			$this->message->add('error', $this->Model_User_Role->getError());
		} else {
			$this->message->add('success', _l("The User Groups have been updated!"));
		}

		if ($this->request->isAjax()) {
			$this->listing();
		} else {
			redirect('admin/user/role');
		}
	}
}
