<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * Class App_Controller_Admin_Settings_Role
 * Title: User Roles
 * Icon: login.png
 * Order: 5
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
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
		$actions = array(
			'delete' => array(
				'label' => _l("Delete"),
			),
		);

		$data['batch_action'] = array(
			'actions' => $actions,
			'url'     => site_url('admin/settings/role/batch-action'),
		);

		//Render
		output($this->render('settings/role/list', $data));
	}

	public function listing()
	{
		//The Table Columns
		$sort    = (array)_get('sort', array('name' => 'ASC'));
		$filter  = (array)_get('filter');
		$options = array(
			'index'   => 'user_role_id',
			'page'    => _get('page'),
			'limit'   => _get('limit', option('admin_list_limit', 20)),
			'columns' => $this->Model_UserRole->getColumns((array)_request('columns')),
		);

		list($user_roles, $total) = $this->Model_UserRole->getRecords($sort, $filter, $options, true);

		foreach ($user_roles as $user_role_id => &$user_role) {
			$user_role['user_count'] = $this->Model_User->getTotalRecords(array('user_role_id' => $user_role_id));

			$actions = array(
				'edit' => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/settings/role/form', 'user_role_id=' . $user_role_id)
				),
			);

			if (!$user_role['user_count']) {
				$actions['delete'] = array(
					'text' => _l("Delete"),
					'href' => site_url('admin/settings/role/delete', 'user_role_id=' . $user_role_id)
				);
			}

			$user_role['actions'] = $actions;
		}
		unset($user_role);

		$listing = array(
			'extra_cols'   => $this->Model_UserRole->getColumns(false),
			'records'      => $user_roles,
			'total'        => $total,
			'sort'         => $sort,
			'filter_value' => $filter,
			'pagination'   => true,
			'listing_path' => 'admin/settings/role/listing',
			'save_path'    => 'admin/settings/role/save',
		);

		$output = block('widget/listing', null, $listing + $options);

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
			'user_role_id' => '',
			'type'         => App_Model_UserRole::TYPE_USER,
			'level'        => 1,
			'name'         => '',
			'permissions'  => array(),
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
		if ($user_role_id = $this->Model_UserRole->save(_request('user_role_id'), $_POST)) {
			message('success', _l("The User Role has been updated!"));
			message('data', array('user_role_id' => $user_role_id));
		} else {
			message('error', $this->Model_UserRole->fetchError());
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
			message('error', $this->Model_UserRole->fetchError());
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
		$batch  = (array)_request('batch');
		$action = _request('action');
		$value  = _request('value');

		foreach ($batch as $user_role_id) {
			switch ($action) {
				case 'delete':
					$this->Model_UserRole->remove($user_role_id);
					break;
			}
		}

		if ($this->Model_UserRole->hasError()) {
			message('error', $this->Model_UserRole->fetchError());
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
