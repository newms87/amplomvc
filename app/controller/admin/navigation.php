<?php

class App_Controller_Admin_Navigation extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("Navigation"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Navigation"), site_url('admin/navigation'));

		//Batch Actions
		$actions = array(
			'enable'  => array(
				'label' => _l("Enable"),
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
			'url'     => site_url('admin/navigation/batch-action'),
		);

		//The Listing
		$data['listing'] = $this->listing();

		//Render
		output($this->render('navigation/list', $data));
	}

	public function listing()
	{
		$sort    = (array)_request('sort', array('name' => 'ASC'));
		$filter  = (array)_request('filter');
		$options = array(
			'index'   => 'navigation_group_id',
			'page'    => _get('page'),
			'limit'   => _get('limit', option('admin_list_limit', 20)),
			'columns' => $this->Model_Navigation->getColumns((array)_request('columns')),
		);

		list($navigation_groups, $total) = $this->Model_Navigation->getGroups($sort, $filter, $options, true);

		foreach ($navigation_groups as $navigation_group_id => &$group) {
			$group['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/navigation/form', 'navigation_group_id=' . $navigation_group_id),
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/navigation/delete', 'navigation_group_id=' . $navigation_group_id),
				),
			);

			if ($group['name'] === 'admin') {
				$group['actions']['reset'] = array(
					'text'               => _l("Restore"),
					'href'               => site_url('admin/navigation/restore-admin-navigation'),
					'#data-confirm-text' => 'This will Restore all the default Admin Navigation links. Existing links will not be overwritten or removed. Are you sure you want to continue?',
				);
			}
		}

		$listing = array(
			'records'        => $navigation_groups,
			'sort'           => $sort,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $total,
			'listing_path'   => 'admin/navigation/listing',
			'save_path'      => 'admin/navigation/save',
		);

		$output = block('widget/listing', null, $listing + $options);

		if ($this->is_ajax) {
			output($output);
		} else {
			return $output;
		}
	}

	public function form()
	{
		//Page Head
		set_page_info('title', _l("Navigation"));

		//Insert or Update
		$navigation_group_id = (int)_get('navigation_group_id');

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Navigation"), site_url('admin/navigation'));
		breadcrumb($navigation_group_id ? _l("Edit") : _l("Add"), site_url('admin/navigation/form', 'navigation_group_id=' . $navigation_group_id));

		//Load Values or Defaults
		$group = $_POST;

		if (!IS_POST && $navigation_group_id) {
			$group = $this->Model_Navigation->getGroup($navigation_group_id);
		}

		$defaults = array(
			'name'   => '',
			'links'  => array(),
			'status' => 1,
		);

		$group += $defaults;

		//Link AC Template
		$group['links']['__ac_template__'] = array(
			'navigation_id' => '',
			'parent_id'     => '',
			'name'          => 'new_link __ac_template__',
			'display_name'  => 'New Link __ac_template__',
			'title'         => '',
			'path'          => '',
			'query'         => '',
			'target'        => '',
			'condition'     => '',
			'status'        => 1,
		);

		$group['data_conditions'] = Condition::$conditions;

		$group['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		$group['navigation_group_id'] = $navigation_group_id;

		//Render
		output($this->render('navigation/form', $group));
	}

	public function save()
	{
		if ($navigation_group_id = $this->Model_Navigation->saveGroup((int)_get('navigation_group_id'), $_POST)) {
			message('success', _l("The Navigation Group has been saved!"));
			message('data', array('navigation_group_id' => $navigation_group_id));
		} else {
			message('error', $this->Model_Navigation->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} elseif ($this->message->has('error')) {
			post_redirect('admin/navigation/form', 'navigation_group_id=' . $navigation_group_id);
		} else {
			redirect('admin/navigation');
		}
	}

	public function delete()
	{
		if ($this->Model_Navigation->removeGroup((int)_get('navigation_group_id'))) {
			message('success', _l("Success: You have modified Navigation!"));
		} else {
			message('error', $this->Model_Navigation->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/navigation');
		}
	}

	public function batch_action()
	{
		foreach ($_POST['batch'] as $navigation_group_id) {
			switch ($_POST['action']) {
				case 'enable':
					$this->Model_Navigation->editNavigationGroup($navigation_group_id, array('status' => 1));
					break;

				case 'disable':
					$this->Model_Navigation->editNavigationGroup($navigation_group_id, array('status' => 0));
					break;

				case 'delete':
					$this->Model_Navigation->removeGroup($navigation_group_id);
					break;
			}

			if ($this->Model_Navigation->hasError()) {
				message('error', $this->Model_Navigation->fetchError());
			} else {
				message('success', _l("Success: You have modified navigation!"));
			}
		}

		if ($this->is_ajax) {
			$this->listing();
		} else {
			redirect('admin/navigation');
		}
	}

	public function restore_admin_navigation()
	{
		$this->Model_Navigation->restoreAdminNavigation();

		if ($this->Model_Navigation->hasError()) {
			message('error', $this->Model_Navigation->fetchError());
			redirect('admin/navigation');
		}

		message("notify", "Admin Navigation Group has been restored!");

		if (!$this->is_ajax) {
			redirect('admin/navigation');
		}

		output_message();
	}

	public function reset_admin_navigation()
	{
		$this->Model_Navigation->resetAdminNavigationGroup();

		if ($this->Model_Navigation->hasError()) {
			message('error', $this->Model_Navigation->fetchError());
			redirect('admin/navigation');
		}

		message("notify", "Admin Navigation Group has been reset!");

		if (!$this->is_ajax) {
			redirect('admin/navigation');
		}

		output_message();
	}

	public function choose_link()
	{
		//TODO: This will be an ajax call to display a template to help choose a URL
		// Categories, Products, custom, etc...
	}
}
