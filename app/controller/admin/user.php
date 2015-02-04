<?php

class App_Controller_Admin_User extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("User"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("User"), site_url('admin/user'));

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
			'url'     => site_url('admin/user/batch-action'),
		);

		//Response
		output($this->render('user/list', $data));
	}

	public function listing()
	{
		//The Table Columns
		$requested_cols = $this->request->get('columns');

		$columns = $this->Model_User->getColumns($requested_cols);

		//The Sort & Filter Data
		$sort   = $this->sort->getQueryDefaults('username', 'ASC');
		$filter = _get('filter', array());

		list($users, $user_total) = $this->Model_User->getRecords($sort, $filter, null, true, 'user_id');

		foreach ($users as $user_id => &$user) {
			$actions = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/user/form', 'user_id=' . $user_id)
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/user/delete', 'user_id=' . $user_id)
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
			'row_id'         => 'user_id',
			'extra_cols'     => $this->Model_User->getColumns(false),
			'columns'        => $columns,
			'rows'           => $users,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $user_total,
			'listing_path'   => 'admin/user/listing',
			'save_path'      => 'admin/user/save',
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
		set_page_info('title', _l("User Information"));

		//Insert or Update
		$user_id = _get('user_id', null);

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("User"), site_url('admin/user'));
		breadcrumb($user_id ? _l("Update") : _l("New"), site_url('admin/user/form', 'user_id=' . $user_id));

		//The Data
		$user = $_POST;

		if ($user_id && !IS_POST) {
			$user = $this->Model_User->getUser($user_id);

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

		$user['data_user_roles'] = $this->Model_UserRole->getRecords(array('cache' => true));

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
		if ($this->Model_User->save(_request('user_id'), $_POST)) {
			message('success', _l("The User has been updated successfully!"));
		} else {
			message('error', $this->Model_User->getError());
		}

		if ($this->is_ajax) {
			output_message();
		} elseif ($this->message->has('error')) {
			$this->form();
		} else {
			redirect('admin/user');
		}
	}

	public function delete()
	{
		if ($this->Model_User->remove(_get('user_id'))) {
			message('success', _l("User was deleted!"));
		} else {
			message('error', $this->Model_User->getError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/user');
		}
	}

	public function batch_action()
	{
		foreach (_post('batch', array()) as $user_id) {
			switch ($_POST['action']) {
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
			message('error', $this->Model_User->getError());
		} else {
			message('success', _l("Users were updated successfully!"));
		}

		if ($this->is_ajax) {
			$this->listing();
		} else {
			redirect('admin/user');
		}
	}

	public function login()
	{
		set_page_info('title', _l("Administration"));

		//If user is logged in, redirect to the homepage
		if (is_logged()) {
			redirect('admin');
		}

		if ($this->session->has('token') && !isset($_COOKIE['token'])) {
			$this->error['warning'] = _l("Invalid token session. Please login again.");
		}

		$defaults = array(
			'username' => '',
		);

		$data = $_POST + $defaults;

		//Render
		output($this->render('user/login', $data));
	}

	public function authenticate()
	{
		if (is_logged()) {
			message('notify', _l("You are already logged in. Please log out first."));
		} elseif ($this->user->login($_POST['username'], $_POST['password'])) {
			if (!empty($_REQUEST['redirect'])) {
				$redirect = $_REQUEST['redirect'];
			} elseif ($this->request->hasRedirect()) {
				$this->request->doRedirect();
			} else {
				$redirect = 'admin';
			}

			redirect($redirect);
		} else {
			message('warning', $this->user->getError());
		}

		//Minimize exposure of password
		unset($_POST['password']);

		post_redirect('admin/user/login');
	}

	public function logout()
	{
		$this->user->logout();

		redirect('admin/user/login');
	}

	public function forgotten()
	{
		//Verify User is not already logged in
		if (is_logged()) {
			redirect();
		}

		//Page Title
		set_page_info('title', _l("Forgot Your Password?"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Forgotten Password"), site_url('admin/user/forgotten'));

		//Entry Data
		$data['email'] = isset($_POST['email']) ? $_POST['email'] : '';

		//Action Buttons
		$data['action'] = site_url('admin/user/reset-request');
		$data['cancel'] = site_url('admin/user/login');

		//Render
		output($this->render('user/forgotten', $data));
	}

	public function reset_request()
	{
		if ($this->user->requestReset(_post('email'))) {
			message('success', _l("Please follow the link that was sent to your email to reset your password."));
		} else {
			message('error', $this->user->getError());
		}

		redirect('admin/user/login');
	}

	public function reset()
	{
		if (is_logged() || empty($_GET['code'])) {
			redirect();
		}

		$code = $_GET['code'];

		$user_id = $this->user->lookupResetCode($code);

		//User not found
		if (!$user_id) {
			message('warning', _l("Unable to locate password reset code. Please try again."));
			redirect('admin/user/login');
		}

		//Handle POST
		if (IS_POST) {
			//Validate Password
			if (!validate('password', $_POST['password'])) {
				if ($this->validation->isErrorCode(Validation::PASSWORD_CONFIRM)) {
					$this->error['confirm'] = $this->validation->getError();
				} else {
					$this->error['password'] = $this->validation->getError();
				}
			} else {
				$this->user->updatePassword($user_id, $_POST['password']);
				$this->user->clearResetCode($user_id);

				message('success', _l('You have successfully updated your password!'));
			}

			redirect('admin/user/login');
		}

		//Breadcrumbs
		breadcrumb(_l('Home'), site_url('admin'));
		breadcrumb(_l('Password Reset'), site_url('admin/user/reset', 'code=' . $code));

		//Action Buttons
		$data['save']   = site_url('admin/user/reset', 'code=' . $code);
		$data['cancel'] = site_url('admin/user/login');

		//Render
		output($this->render('user/reset', $data));
	}
}
