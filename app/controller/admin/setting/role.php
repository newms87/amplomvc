<?php

class App_Controller_Admin_Setting_Role extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("User Roles"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("User Roles"), site_url('admin/setting/role'));

		//Batch Actions
		if (user_can('w', 'admin/setting/role/batch_action')) {
			$actions = array(
				'delete' => array(
					'label' => _l("Delete"),
				),
			);

			$data['batch_action'] = array(
				'actions' => $actions,
				'url'     => site_url('admin/setting/role/batch-action'),
			);
		}

		$data['view_listing_id'] = $this->Model_Setting_Role->getViewListingId();

		//Render
		output($this->render('setting/role/list', $data));
	}

	public function listing()
	{
		//The Table Columns
		$disable = array(
			'permissions' => false,
		);

		$columns = $this->Model_Setting_Role->getColumns($disable + (array)_request('columns'));

		//The Sort & Filter Data
		$sort   = $this->sort->getQueryDefaults('name', 'ASC');
		$filter = _get('filter', array());

		list($user_roles, $user_role_total) = $this->Model_Setting_Role->getRoles($sort, $filter, $columns, true, 'user_role_id');

		foreach ($user_roles as $user_role_id => &$user_role) {
			$actions = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/setting/role/form', 'user_role_id=' . $user_role_id)
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/setting/role/delete', 'user_role_id=' . $user_role_id)
				),
			);

			$user_role['actions'] = $actions;
		}
		unset($user_role);

		$listing = array(
			'row_id'         => 'user_role_id',
			'extra_cols'     => $this->Model_Setting_Role->getColumns($disable),
			'columns'        => $columns,
			'rows'           => $user_roles,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $user_role_total,
			'listing_path'   => 'admin/setting/role/listing',
			'save_path'      => 'admin/setting/role/save',
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
			'permissions' => array(),
		);

		$user_role += $defaults;

		//Template Data
		$areas = $this->Model_Setting_Role->getRestrictedAreas();

		$this->fillAreaDefaults($areas, (array)$user_role['permissions']);

		$user_role['data_areas'] = $areas;

		$user_role['data_perms'] = array(
			''  => _l("none"),
			'r' => _l("read"),
			'w' => _l("write"),
		);

		//Render
		output($this->render('setting/role/form', $user_role));
	}

	public function save()
	{
		if (!$this->Model_Setting_Role->save(_request('user_role_id'), $_POST)) {
			message('error', $this->Model_Setting_Role->getError());
		} else {
			message('success', _l("The User Role has been updated!"));
		}

		//Response
		if (IS_AJAX) {
			output_json($this->message->fetch());
		} elseif ($this->message->has('error')) {
			post_redirect('admin/setting/role/form');
		} else {
			redirect('admin/setting/role');
		}
	}

	public function delete()
	{
		//Delete
		if ($this->Model_Setting_Role->remove(_get('user_role_id'))) {
			message('success', _l("User Role has been removed"));
		} else {
			message('error', $this->Model_Setting_Role->getError());
		}

		//Response
		if (IS_AJAX) {
			output_json($this->message->fetch());
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
