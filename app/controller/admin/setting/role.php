<?php

class App_Controller_Admin_Setting_Role extends Controller
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
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("User Roles"), site_url('admin/setting/role'));

		//Batch Actions
		$actions = array(
			'delete' => array(
				'label' => _l("Delete"),
			),
		);

		$data['batch_action'] = array(
			'actions' => $actions,
			'url'     => site_url('admin/setting/role/batch_action'),
		);

		//The Listing
		$data['listing'] = $this->listing();

		//Actions
		$data['insert'] = site_url('admin/setting/role/form');

		//Render
		output($this->render('setting/role_list', $data));
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

		$user_role_total = $this->Model_Setting_Role->getTotalRoles($filter);
		$user_roles      = $this->Model_Setting_Role->getRoles($sort + $filter);

		foreach ($user_roles as &$user_role) {
			$actions = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/setting/role/form', 'user_role_id=' . $user_role['user_role_id'])
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/setting/role/delete', 'user_role_id=' . $user_role['user_role_id'])
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
			'listing_path'   => 'admin/setting/role/listing',
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
		$this->document->setTitle(_l("User Roles"));

		//Insert or Update
		$user_role_id = !empty($_GET['user_role_id']) ? (int)$_GET['user_role_id'] : 0;

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("User Roles"), site_url('admin/setting/role'));
		breadcrumb($user_role_id ? _l("Update") : _l("New"), site_url('admin/setting/role/form', 'user_role_id=' . $user_role_id));

		//The Data
		$user_role = $_POST;

		if ($user_role_id && !IS_POST) {
			$user_role = $this->Model_Setting_Role->getRole($user_role_id);
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
		$user_role['data_controllers'] = $this->Model_Setting_Role->getControllers();

		//Actions
		$user_role['save'] = site_url('admin/setting/role/update', 'user_role_id=' . $user_role_id);

		//Render
		output($this->render('setting/role_form', $user_role));
	}

	public function update()
	{
		//Insert
		if (empty($_GET['user_role_id'])) {
			$this->Model_Setting_Role->add($_POST);
		} //Update
		else {
			$this->Model_Setting_Role->edit($_GET['user_role_id'], $_POST);
		}

		//Success / Error
		if ($this->Model_Setting_Role->hasError()) {
			message('error', $this->Model_Setting_Role->getError());
		} else {
			message('success', _l("The User Role has been updated!"));
		}

		//Response
		if (IS_AJAX) {
			output($this->message->toJSON());
		} elseif ($this->message->has('error')) {
			$this->form();
		} else {
			redirect('admin/setting/role');
		}
	}

	public function delete()
	{
		//Delete
		$this->Model_Setting_Role->deleteUserRole($_GET['category_id']);

		//Success / Error
		if ($this->Model_Setting_Role->hasError()) {
			message('error', $this->Model_Setting_Role->getError());
		} else {
			message('notify', _l("User Role was deleted!"));
		}

		//Response
		if (IS_AJAX) {
			output($this->message->toJSON());
		} else {
			redirect('admin/setting/role');
		}
	}

	public function batch_action()
	{
		foreach ($_POST['batch'] as $user_role_id) {
			switch ($_POST['action']) {
				case 'delete':
					$this->Model_Setting_Role->remove($user_role_id);
					break;
			}
		}

		if ($this->Model_Setting_Role->hasError()) {
			message('error', $this->Model_Setting_Role->getError());
		} else {
			message('success', _l("The User Groups have been updated!"));
		}

		if (IS_AJAX) {
			$this->listing();
		} else {
			redirect('admin/setting/role');
		}
	}
}
