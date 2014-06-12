<?php

class App_Controller_Admin_Design_Navigation extends Controller
{
	static $allow = array(
		'modify ' => array(
			'update',
			'delete',
			'batch_action',
			'reset_admin_navigation',
			'form',
		),
	);

	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Navigation"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("Navigation"), site_url('admin/design/navigation'));

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
			'url'     => site_url('admin/design/navigation/batch_action'),
		);

		//The Listing
		$data['listing'] = $this->listing();

		//Action Buttons
		$data['insert'] = site_url('admin/design/navigation/form');

		//Render
		$this->response->setOutput($this->render('design/navigation_list', $data));
	}

	public function update()
	{
		//Insert
		if (empty($_GET['navigation_group_id'])) {
			$this->Model_Design_Navigation->addNavigationGroup($_POST);
		} //Update
		else {
			$this->Model_Design_Navigation->editNavigationGroup($_GET['navigation_group_id'], $_POST);
		}

		if ($this->Model_Design_Navigation->hasError()) {
			$this->message->add('error', $this->Model_Design_Navigation->getError());
		} else {
			$this->message->add('success', _l("Navigation has been updated!"));
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} elseif ($this->message->has('error')) {
			$this->form();
		} else {
			redirect('admin/design/navigation');
		}
	}

	public function delete()
	{
		$this->Model_Design_Navigation->deleteNavigationGroup($_GET['navigation_group_id']);

		if ($this->Model_Design_Navigation->hasError()) {
			$this->message->add('error', $this->Model_Design_Navigation->getError());
		} else {
			$this->message->add('success', _l("Success: You have modified Navigation!"));
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} else {
			redirect('admin/design/navigation');
		}
	}

	public function listing()
	{
		//Column Build Data
		$stores = array(
			'admin' => array(
				'store_id' => 'admin',
				'name'     => 'Admin Panel'
			)
		);

		$stores += $this->Model_Setting_Store->getStores();

		//The Table Columns
		$columns = array();

		$columns['name'] = array(
			'type'         => 'text',
			'display_name' => _l("Navigation Group"),
			'filter'       => true,
			'sortable'     => true,
			'sort_value'   => 'name',
		);

		$columns['stores'] = array(
			'type'         => 'multiselect',
			'display_name' => _l("Stores"),
			'filter'       => true,
			'build_config' => array(
				'store_id',
				'name'
			),
			'build_data'   => $stores,
			'sortable'     => false,
		);

		$columns['status'] = array(
			'type'         => 'select',
			'display_name' => _l("Status"),
			'filter'       => true,
			'build_data'   => array(
				0 => _l("Disabled"),
				1 => _l("Enabled"),
			),
			'sortable'     => true,
			'sort_value'   => 'status',
		);

		//Get Sorted / Filtered Data
		$sort   = $this->sort->getQueryDefaults('name', 'ASC');
		$filter = _get('filter', array());

		$navigation_groups_total = $this->Model_Design_Navigation->getTotalNavigationGroups($filter);
		$navigation_groups       = $this->Model_Design_Navigation->getNavigationGroups($sort + $filter);

		$url_query = $this->url->getQueryExclude('navigation_group_id');

		foreach ($navigation_groups as &$nav_group) {
			$nav_group['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/design/navigation/form', 'navigation_group_id=' . $nav_group['navigation_group_id']),
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/design/navigation/delete', 'navigation_group_id=' . $nav_group['navigation_group_id'] . '&' . $url_query),
				)
			);

			if ($nav_group['name'] == 'admin') {
				$nav_group['actions']['reset'] = array(
					'text'   => _l("Reset Admin Navigation"),
					'href'   => site_url('admin/design/navigation/reset_admin_navigation', $url_query),
					'#class' => 'reset',
				);
			}
		}

		$listing = array(
			'row_id'         => 'navigation_group_id',
			'columns'        => $columns,
			'rows'           => $navigation_groups,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $navigation_groups_total,
			'listing_path'   => 'design/navigation/listing',
		);

		$output = block('widget/listing', null, $listing);

		if ($this->request->isAjax()) {
			$this->response->setOutput($output);
		} else {
			return $output;
		}
	}

	public function form()
	{
		//Page Head
		$this->document->setTitle(_l("Navigation"));

		//Insert or Update
		$navigation_group_id = isset($_GET['navigation_group_id']) ? (int)$_GET['navigation_group_id'] : null;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("Navigation"), site_url('admin/design/navigation'));

		if ($navigation_group_id) {
			$this->breadcrumb->add(_l("Edit"), site_url('admin/design/navigation', 'navigation_group_id=' . $navigation_group_id));
		} else {
			$this->breadcrumb->add(_l("Add"), site_url('admin/design/navigation'));
		}

		//Load Values or Defaults
		$navigation_group_info = array();

		if ($this->request->isPost()) {
			$navigation_group_info = $_POST;
		} elseif ($navigation_group_id) {
			$navigation_group_info = $this->Model_Design_Navigation->getNavigationGroup($navigation_group_id);
		}

		$defaults = array(
			'name'   => '',
			'links'  => array(),
			'stores' => array(option('config_default_store')),
			'status' => 1,
		);

		$data = $navigation_group_info + $defaults;

		//Link AC Template
		$data['links']['__ac_template__'] = array(
			'navigation_id' => '',
			'parent_id'     => '',
			'name'          => 'new_link __ac_template__',
			'display_name'  => 'New Link __ac_template__',
			'title'         => '',
			'href'          => '',
			'query'         => '',
			'condition'     => '',
			'status'        => 1,
		);

		//Template Data
		$data['categories'] = $this->Model_Catalog_Category->getCategoryTree();

		$admin_store = array(
			'admin' => array(
				'store_id' => -1,
				'name'     => _l("Admin Panel"),
			)
		);

		$data['data_stores']     = $admin_store + $this->Model_Setting_Store->getStores();
		$data['data_conditions'] = $this->condition->getConditions();

		$data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		//Action Buttons
		$data['save']   = site_url('admin/design/navigation/update', 'navigation_group_id=' . $navigation_group_id);
		$data['cancel'] = site_url('admin/design/navigation');

		//Render
		$this->response->setOutput($this->render('design/navigation_form', $data));
	}

	public function batch_action()
	{
		foreach ($_POST['batch'] as $navigation_group_id) {
			switch ($_POST['action']) {
				case 'enable':
					$this->Model_Design_Navigation->editNavigationGroup($navigation_group_id, array('status' => 1));
					break;

				case 'disable':
					$this->Model_Design_Navigation->editNavigationGroup($navigation_group_id, array('status' => 0));
					break;

				case 'delete':
					$this->Model_Design_Navigation->deleteNavigationGroup($navigation_group_id);
					break;
			}

			if ($this->Model_Design_Navigation->hasError()) {
				$this->message->add('error', $this->Model_Design_Navigation->getError());
			} else {
				$this->message->add('success', _l("Success: You have modified navigation!"));
			}
		}

		if ($this->request->isAjax()) {
			$this->listing();
		} else {
			redirect('admin/design/navigation');
		}
	}

	public function reset_admin_navigation()
	{
		$this->Model_Design_Navigation->resetAdminNavigationGroup();

		if ($this->Model_Design_Navigation->hasError()) {
			$this->message->add('error', $this->Model_Design_Navigation->getError());
			redirect('admin/design/navigation');
		}

		$this->message->add("notify", "Admin Navigation Group has been reset!");

		if (!$this->request->isAjax()) {
			redirect('admin/design/navigation');
		}

		$this->response->setOutput($this->message->toJSON());
	}

	public function choose_link()
	{
		//TODO: This will be an ajax call to display a template to help choose a URL
		// Categories, Products, custom, etc...
	}
}
