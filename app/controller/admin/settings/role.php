<?php

/**
 * Class App_Controller_Admin_Settings_Role
 * Title: User Roles
 * Icon: login.png
 * Order: 5
 *
 */

class App_Controller_Admin_Settings_Role extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("User Roles"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("User Roles"), site_url('admin/settings/role'));

		//Batch Actions
		if (user_can('w', 'admin/settings/role/batch_action')) {
			$actions = array(
				'delete' => array(
					'label' => _l("Delete"),
				),
			);

			$data['batch_action'] = array(
				'actions' => $actions,
				'url'     => site_url('admin/settings/role/batch-action'),
			);
		}

		$data['view_listing_id'] = $this->Model_UserRole->getViewListingId();

		//Render
		output($this->render('settings/role/list', $data));
	}

	public function listing()
	{
		//The Table Columns
		$disable = array(
			'permissions' => false,
		);

		$columns = $this->Model_UserRole->getColumns($disable + (array)_request('columns'));

		//The Sort & Filter Data
		$sort   = $this->sort->getQueryDefaults('name', 'ASC');
		$filter = _get('filter', array());

		list($user_roles, $user_role_total) = $this->Model_UserRole->getRecords($sort, $filter, $columns, true, 'user_role_id');

		foreach ($user_roles as $user_role_id => &$user_role) {
			$actions = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/settings/role/form', 'user_role_id=' . $user_role_id)
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/settings/role/delete', 'user_role_id=' . $user_role_id)
				),
			);

			$user_role['actions'] = $actions;
		}
		unset($user_role);

		$listing = array(
			'row_id'         => 'user_role_id',
			'extra_cols'     => $this->Model_UserRole->getColumns($disable),
			'columns'        => $columns,
			'rows'           => $user_roles,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $user_role_total,
			'listing_path'   => 'admin/settings/role/listing',
			'save_path'      => 'admin/settings/role/save',
		);

		$output = block('widget/listing', null, $listing);

		//Response
		if ($this->is_ajax) {
			output($output);
		}

		return $output;
	}

	public function form()
	{
		//Page Head
		set_page_info('title', _l("User Roles"));

		//Insert or Update
		$user_role_id = !empty($_GET['user_role_id']) ? (int)$_GET['user_role_id'] : 0;

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("User Roles"), site_url('admin/settings/role'));
		breadcrumb($user_role_id ? _l("Update") : _l("New"), site_url('admin/settings/role/form', 'user_role_id=' . $user_role_id));

		//The Data
		$user_role = $_POST;

		if ($user_role_id && !IS_POST) {
			$user_role = $this->Model_UserRole->getRole($user_role_id);
		}

		// Defaults
		$defaults = array(
			'name'        => '',
			'permissions' => array(),
		);

		$user_role += $defaults;

		//Template Data
		$areas = $this->Model_UserRole->getRestrictedAreas();

		$this->fillAreaDefaults($areas, (array)$user_role['permissions']);

		$user_role['data_areas'] = $areas;

		$user_role['data_perms'] = array(
			''  => _l("none"),
			'r' => _l("read"),
			'w' => _l("write"),
		);

		//Render
		output($this->render('settings/role/form', $user_role));
	}

	public function save()
	{
		if (!$this->Model_UserRole->save(_request('user_role_id'), $_POST)) {
			message('error', $this->Model_UserRole->getError());
		} else {
			message('success', _l("The User Role has been updated!"));
		}

		//Response
		if ($this->is_ajax) {
			output_message();
		} elseif ($this->message->has('error')) {
			post_redirect('admin/settings/role/form');
		} else {
			redirect('admin/settings/role');
		}
	}

	public function delete()
	{
		//Delete
		if ($this->Model_UserRole->remove(_get('user_role_id'))) {
			message('success', _l("User Role has been removed"));
		} else {
			message('error', $this->Model_UserRole->getError());
		}

		//Response
		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/settings/role');
		}
	}

	public function batch_action()
	{
		foreach ($_POST['batch'] as $user_role_id) {
			switch ($_POST['action']) {
				case 'delete':
					$this->Model_UserRole->remove($user_role_id);
					break;
			}
		}

		if ($this->Model_UserRole->hasError()) {
			message('error', $this->Model_UserRole->getError());
		} else {
			message('success', _l("The User Groups have been updated!"));
		}

		if ($this->is_ajax) {
			$this->listing();
		} else {
			redirect('admin/settings/role');
		}
	}

	private function fillAreaDefaults(&$areas, array $perms)
	{
		foreach ($perms as $p => $value) {
			if ($p === '*') {
				continue;
			}

			if (isset($areas[$p])) {
				$areas[$p]['*'] = isset($value['*']) ? $value['*'] : '';
			}

			if (count($value) > 1) {
				$this->fillAreaDefaults($areas[$p], $value);
			}
		}
	}
}
