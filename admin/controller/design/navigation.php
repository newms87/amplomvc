<?php
class Admin_Controller_Design_Navigation extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Navigation"));

		//The Template
		$this->template->load('design/navigation_list');

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Navigation"), $this->url->link('design/navigation'));

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
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		$navigation_groups_total = $this->Model_Design_Navigation->getTotalNavigationGroups($filter);
		$navigation_groups       = $this->Model_Design_Navigation->getNavigationGroups($sort + $filter);

		$url_query = $this->url->getQueryExclude('navigation_group_id');

		foreach ($navigation_groups as &$nav_group) {
			$nav_group['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => $this->url->link('design/navigation/update', 'navigation_group_id=' . $nav_group['navigation_group_id']),
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => $this->url->link('design/navigation/delete', 'navigation_group_id=' . $nav_group['navigation_group_id'] . '&' . $url_query),
				)
			);

			if ($nav_group['name'] == 'admin') {
				$nav_group['actions']['reset'] = array(
					'text'   => _l("Reset Admin Navigation"),
					'href'   => $this->url->link('design/navigation/reset_admin_navigation' . '&' . $url_query),
					'#class' => 'reset',
				);
			}
		}

		//Build The Table
		$tt_data = array(
			'row_id' => 'navigation_group_id',
		);

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($navigation_groups);
		$this->table->setTemplateData($tt_data);
		$this->table->mapAttribute('filter_value', $filter);

		$this->data['list_view'] = $this->table->render();

		//Batch Actions
		$this->data['batch_actions'] = array(
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

		$this->data['batch_update'] = $this->url->link('design/navigation/batch_update', $url_query);

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $navigation_groups_total;

		$this->data['pagination'] = $this->pagination->render();

		//Action Buttons
		$this->data['insert'] = $this->url->link('design/navigation/update');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	public function update()
	{
		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['navigation_group_id'])) {
				$this->Model_Design_Navigation->addNavigationGroup($_POST);
			} //Update
			else {
				$this->Model_Design_Navigation->editNavigationGroup($_GET['navigation_group_id'], $_POST);
			}

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified navigation!"));

				$this->url->redirect('design/navigation');
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		if (!empty($_GET['navigation_group_id']) && $this->validateDelete()) {
			$this->Model_Design_Navigation->deleteNavigationGroup($_GET['navigation_group_id']);

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified navigation!"));

				$this->url->redirect('design/navigation');
			}
		}

		$this->index();
	}

	public function reset_admin_navigation()
	{
		$this->Model_Design_Navigation->reset_admin_navigation_group();

		$this->message->add("notify", "Admin Navigation Group has been reset!");

		$this->url->redirect('design/navigation');
	}

	public function batch_update()
	{
		$this->document->setTitle(_l("Navigation"));

		if (isset($_GET['selected']) && isset($_GET['action'])) {
			if ($_GET['action'] !== 'delete' || $this->validateDelete()) {
				foreach ($_GET['selected'] as $navigation_group_id) {
					switch ($_GET['action']) {
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
					if ($this->error) {
						break;
					}
				}
			}

			if (!$this->error) {
				if (!$this->message->hasError()) {
					$this->message->add('success', _l("Success: You have modified navigation!"));

					$this->url->redirect('design/navigation');
				}
			}
		}

		$this->index();
	}

	private function getForm()
	{
		//Page Head
		$this->document->setTitle(_l("Navigation"));

		//Insert or Update
		$navigation_group_id = isset($_GET['navigation_group_id']) ? (int)$_GET['navigation_group_id'] : null;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Navigation"), $this->url->link('design/navigation'));

		if ($navigation_group_id) {
			$this->breadcrumb->add(_l("Edit"), $this->url->link('design/navigation', 'navigation_group_id=' . $navigation_group_id));
		} else {
			$this->breadcrumb->add(_l("Add"), $this->url->link('design/navigation'));
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
			'stores' => array($this->config->get('config_default_store')),
			'status' => 1,
		);

		$this->data += $navigation_group_info + $defaults;

		//Link AC Template
		$this->data['links']['__ac_template__'] = array(
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
		$this->data['categories'] = $this->Model_Catalog_Category->getCategoryTree();

		$admin_store = array(
			'admin' => array(
				'store_id' => -1,
				'name'     => _l("Admin Panel"),
			)
		);

		$this->data['data_stores']     = $admin_store + $this->Model_Setting_Store->getStores();
		$this->data['data_conditions'] = $this->condition->getConditions();

		$this->data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		//Action Buttons
		$this->data['save']   = $this->url->link('design/navigation/update', 'navigation_group_id=' . $navigation_group_id);
		$this->data['cancel'] = $this->url->link('design/navigation');

		//The Template
		$this->template->load('design/navigation_form');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	public function choose_link()
	{
		//TODO: This will be an ajax call to display a template to help choose a URL
		// Categories, Products, custom, etc...
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'design/navigation')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify navigation!");
		}

		$navigation_group_id = isset($_GET['navigation_group_id']) ? (int)$_GET['navigation_group_id'] : 0;

		if (!isset($_POST['stores'])) {
			$_POST['stores'] = array('');
		}

		if (!$this->validation->text($_POST['name'], 3, 64)) {
			$this->error['name'] = _l("Navigation Group Name must be between 3 and 64 characters!");
		}

		if (!empty($_POST['links'])) {
			foreach ($_POST['links'] as $key => $link) {
				if (!$this->validation->text($link['display_name'], 1, 255)) {
					$link_name                                = !empty($link['name']) ? $link['name'] : (!empty($link['display_name']) ? $link['display_name'] : $key);
					$this->error["links[$key][display_name]"] = _l("The Display Name for the link %s must be between 1 and 255 characters!", $link_name);
				}

				//If name already exists in database, append _n to the name
				if (empty($link['name'])) {
					$name = $this->tool->getSlug($link['display_name']);
				} else {
					$name = $this->db->escape($this->tool->getSlug($link['name']));
				}

				$count = 0;
				do {
					$check_name = $count ? $name . '_' . $count : $name;

					$result = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "navigation_group WHERE name = '$check_name' AND navigation_group_id != $navigation_group_id");

					$count++;
				} while ($result->row['total']);

				$_POST['links'][$key]['name'] = $check_name;
			}
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'design/navigation')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify navigation!");
		}

		return $this->error ? false : true;
	}
}
