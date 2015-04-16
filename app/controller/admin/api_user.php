<?php

class App_Controller_Admin_ApiUser extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("API Users"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("User"), site_url('admin/api_user'));

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
			'url'     => site_url('admin/api_user/batch-action'),
		);

		//Response
		output($this->render('user/list', $data));
	}

	public function listing()
	{
		$sort    = (array)_request('sort', array('username' => 'ASC'));
		$filter  = (array)_request('filter');
		$options = array(
			'index'   => 'api_user_id',
			'page'    => _get('page', 1),
			'limit'   => _get('limit', option('admin_list_limit', 20)),
			'columns' => $this->Model_ApiUser->getColumns((array)_request('columns')),
		);

		list($users, $user_total) = $this->Model_User->getRecords($sort, $filter, $options, true);

		foreach ($users as $user_id => &$user) {
			$actions = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/api_user/form', 'user_id=' . $user_id)
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/api_user/remove', 'user_id=' . $user_id)
				),
			);

			$user['actions'] = $actions;

			if (!$user['last_name']) {
				$user['name'] = $user['first_name'] ? $user['first_name'] : _l("No Name");
			} elseif ($user['first_name']) {
				$user['name'] = $user['last_name'] . ', ' . $user['first_name'];
			} else {
				$user['name'] = $user['last_name'];
			}
		}
		unset($user);

		$listing = array(
			'extra_cols'     => $this->Model_User->getColumns(false),
			'records'        => $users,
			'sort'           => $sort,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $user_total,
			'listing_path'   => 'admin/api_user/listing',
			'save_path'      => 'admin/api_user/save',
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
		set_page_info('title', _l("User Information"));

		//Insert or Update
		$user_id = _get('user_id', null);

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("User"), site_url('admin/api_user'));
		breadcrumb($user_id ? _l("Update") : _l("New"), site_url('admin/api_user/form', 'user_id=' . $user_id));

		//The Data
		$user = $_POST;

		if ($user_id && !IS_POST) {
			$user = $this->Model_User->getRecord($user_id);

			$user['meta'] = $this->Model_User->getMeta($user_id);
		}

		$defaults = array(
			'user_id'      => $user_id,
			'username'     => '',
			'first_name'   => '',
			'last_name'    => '',
			'email'        => '',
			'user_role_id' => option('config_default_user_role', 12),
			'status'       => 1,
			'meta'         => array(),
		);

		$user += $defaults;

		$user['data_user_roles'] = $this->Model_UserRole->getRecords(null, null, array('cache' => true));

		$user['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		$user['meta']['__ac_template__'] = '';

		//Response
		output($this->render('user/form', $user));
	}

	public function save()
	{
		if ($user_id = $this->Model_User->save(_request('user_id'), $_POST)) {
			message('success', _l("The User has been updated successfully!"));
			message('data', array('user_id' => $user_id));
		} else {
			message('error', $this->Model_User->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} elseif ($this->message->has('error')) {
			$this->form();
		} else {
			redirect('admin/api_user');
		}
	}

	public function remove()
	{
		if ($this->Model_User->remove(_get('user_id'))) {
			message('success', _l("User was deleted!"));
		} else {
			message('error', $this->Model_User->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/api_user');
		}
	}

	public function batch_action()
	{
		$batch  = (array)_request('batch');
		$action = _request('action');
		$value  = _request('value');

		foreach ($batch as $user_id) {
			switch ($action) {
				case 'enable':
					$this->Model_User->save($user_id, array('status' => 1));
					break;

				case 'disable':
					$this->Model_User->save($user_id, array('status' => 0));
					break;

				case 'delete':
					$this->Model_User->remove($user_id);
					break;
			}
		}

		if ($this->Model_User->hasError()) {
			message('error', $this->Model_User->fetchError());
		} else {
			message('success', _l("Users were updated successfully!"));
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/api_user');
		}
	}
}
