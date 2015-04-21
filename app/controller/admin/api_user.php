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
		output($this->render('api_user/list', $data));
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

		list($api_users, $total) = $this->Model_ApiUser->getRecords($sort, $filter, $options, true);

		foreach ($api_users as $api_user_id => &$api_user) {
			$actions = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/api_user/form', 'api_user_id=' . $api_user_id)
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/api_user/remove', 'api_user_id=' . $api_user_id)
				),
			);

			$api_user['actions'] = $actions;
		}
		unset($api_user);

		$listing = array(
			'extra_cols'     => $this->Model_ApiUser->getColumns(false),
			'records'        => $api_users,
			'sort'           => $sort,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $total,
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
		set_page_info('title', _l("API User Information"));

		//Insert or Update
		$api_user_id = _get('api_user_id', null);

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("User"), site_url('admin/api_user'));
		breadcrumb($api_user_id ? _l("Update") : _l("New"), site_url('admin/api_user/form', 'api_user_id=' . $api_user_id));

		//The Data
		$api_user = $_POST;

		if ($api_user_id && !IS_POST) {
			$api_user = $this->Model_ApiUser->getRecord($api_user_id);
		}

		$defaults = array(
			'api_user_id'  => $api_user_id,
			'user_id'      => user_info('user_id'),
			'username'     => user_info('username'),
			'user_role_id' => option('default_api_user_role_id'),
			'api_key'      => '',
			'public_key'   => '',
			'private_key'  => '',
			'status'       => 1,
		);

		$api_user += $defaults;

		$role_filter = array(
			'user_id' => user_info('user_id'),
			'type'    => 'api_user',
		);

		$api_user['data_user_roles'] = $this->Model_UserRole->getRecords(null, $role_filter, array('cache' => true));

		if (user_is('Administrator', 'Top Administrator')) {
			$api_user['data_users'] = $this->Model_User->getRecords(null, null, array('cache' => true));
		}

		//Response
		output($this->render('api_user/form', $api_user));
	}

	public function save()
	{
		if ($api_user_id = $this->Model_ApiUser->save(_request('api_user_id'), $_POST)) {
			message('success', _l("The User has been updated successfully!"));
			message('data', array('api_user_id' => $api_user_id));
		} else {
			message('error', $this->Model_ApiUser->fetchError());
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
		if ($this->Model_ApiUser->remove(_get('api_user_id'))) {
			message('success', _l("API User was deleted!"));
		} else {
			message('error', $this->Model_ApiUser->fetchError());
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

		foreach ($batch as $api_user_id) {
			switch ($action) {
				case 'enable':
					$this->Model_ApiUser->save($api_user_id, array('status' => 1));
					break;

				case 'disable':
					$this->Model_ApiUser->save($api_user_id, array('status' => 0));
					break;

				case 'delete':
					$this->Model_ApiUser->remove($api_user_id);
					break;
			}
		}

		if ($this->Model_ApiUser->hasError()) {
			message('error', $this->Model_ApiUser->fetchError());
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
