<?php

/**
 * Title: URL Aliases
 * Icon: url_alias_icon.png
 * Order: 7
 */
class App_Controller_Admin_Setting_UrlAlias extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("URL Aliases"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("Settings"), site_url('admin/setting/store'));
		$this->breadcrumb->add(_l("URL Aliases"), site_url('admin/setting/url_alias'));

		//The Table Columns
		$columns = array();

		$columns['alias'] = array(
			'type'         => 'text',
			'display_name' => _l("URL Alias:"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['path'] = array(
			'type'         => 'text',
			'display_name' => _l("Path:"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['query'] = array(
			'type'         => 'text',
			'display_name' => _l("Query:"),
			'filter'       => true,
			'sortable'     => true,
		);

		$non_stores = array(
			array(
				'store_id' => '-1',
				'name'     => _l("Admin Panel"),
			),
			array(
				'store_id' => 0,
				'name'     => _l("All Stores"),
			),
		);

		$columns['store_id'] = array(
			'type'         => 'select',
			'display_name' => _l("Store:"),
			'filter'       => true,
			'build_config' => array(
				'store_id',
				'name'
			),
			'build_data'   => array_merge($non_stores, $this->Model_Setting_Store->getStores()),
			'sortable'     => false,
		);

		$columns['status'] = array(
			'type'         => 'select',
			'display_name' => _l("Status:"),
			'filter'       => true,
			'build_data'   => array(
				0 => _l("Disabled"),
				1 => _l("Enabled"),
			),
			'sortable'     => true,
		);

		//Get Sorted / Filtered Data
		$sort   = $this->sort->getQueryDefaults('alias', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		$url_alias_total = $this->Model_Setting_UrlAlias->getTotalUrlAliases($filter);
		$url_aliases     = $this->Model_Setting_UrlAlias->getUrlAliases($sort + $filter);

		$url_query = $this->url->getQueryExclude('url_alias_id');

		foreach ($url_aliases as &$url_alias) {
			$url_alias['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/setting/url_alias/update', 'url_alias_id=' . $url_alias['url_alias_id'])
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/setting/url_alias/delete', 'url_alias_id=' . $url_alias['url_alias_id'] . '&' . $url_query)
				)
			);
		}
		unset($url_alias);

		//Build The Table
		$tt_data = array(
			'row_id' => 'url_alias_id',
		);

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($url_aliases);
		$this->table->setTemplateData($tt_data);
		$this->table->mapAttribute('filter_value', $filter);

		$data['list_view'] = $this->table->render();

		//Batch Actions
		$data['batch_actions'] = array(
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

		$data['batch_update'] = 'setting/url_alias/batch_update';

		//Render Limit Menu
		$data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $url_alias_total;

		$data['pagination'] = $this->pagination->render();

		//Action Buttons
		$data['insert'] = site_url('admin/setting/url_alias/update');
		$data['delete'] = site_url('admin/setting/url_alias/delete');
		$data['cancel'] = site_url('admin/setting/store');

		//Render
		$this->response->setOutput($this->render('setting/url_alias_list', $data));
	}

	public function update()
	{
		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['url_alias_id'])) {
				$this->Model_Setting_UrlAlias->addUrlAlias($_POST);
			} //Update
			else {
				$this->Model_Setting_UrlAlias->editUrlAlias($_GET['url_alias_id'], $_POST);
			}

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Success: You have modified the url alias table!"));

				redirect('admin/setting/url_alias');
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		if (!empty($_GET['url_alias_id']) && $this->validateDelete()) {
			$this->Model_Setting_UrlAlias->deleteUrlAlias($_GET['url_alias_id']);

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Success: You have modified the url alias table!"));

				redirect('admin/setting/url_alias');
			}
		}

		$this->index();
	}

	public function batch_update()
	{
		if (!empty($_GET['selected']) && isset($_GET['action'])) {
			foreach ($_GET['selected'] as $url_alias_id) {
				$data = array();

				switch ($_GET['action']) {
					case 'enable':
						$data['status'] = 1;
						break;
					case 'disable':
						$data['status'] = 0;
						break;
					case 'delete':
						$this->Model_Setting_UrlAlias->deleteUrlAlias($url_alias_id);
						break;
					default:
						break 2; //Break For loop
				}

				$this->Model_Setting_UrlAlias->editUrlAlias($url_alias_id, $data);
			}

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Success: You have modified the url alias table!"));
			}
		}

		redirect('admin/setting/url_alias', $this->url->getQueryExclude('action', 'action_value'));
	}

	public function getForm()
	{
		//Page Head
		$this->document->setTitle(_l("URL Aliases"));

		//Insert or Update
		$url_alias_id = isset($_GET['url_alias_id']) ? (int)$_GET['url_alias_id'] : 0;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("URL Aliases"), site_url('admin/setting/url_alias'));

		if (!$url_alias_id) {
			$this->breadcrumb->add(_l("Add"), site_url('admin/setting/url_alias/udpate'));
		} else {
			$this->breadcrumb->add(_l("Edit"), site_url('admin/setting/url_alias/update', 'url_alias_id=' . $url_alias_id));
		}

		//Load Information
		if ($url_alias_id && !$this->request->isPost()) {
			$url_alias_info = $this->Model_Setting_UrlAlias->getUrlAlias($url_alias_id);
		}

		//Load Values or Defaults
		$defaults = array(
			'alias'    => '',
			'path'     => '',
			'query'    => '',
			'store_id' => 0,
			'redirect' => '',
			'status'   => 1,
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$data[$key] = $_POST[$key];
			} elseif (isset($url_alias_info[$key])) {
				$data[$key] = $url_alias_info[$key];
			} else {
				$data[$key] = $default;
			}
		}

		//Template Data
		$non_stores = array(
			array(
				'store_id' => '-1',
				'name'     => _l("Admin Panel"),
			),
			array(
				'store_id' => 0,
				'name'     => _l("All Stores"),
			),
		);

		$data['data_stores'] = array_merge($non_stores, $this->Model_Setting_Store->getStores());

		$data['data_non_stores'] = array(
			array(
				'store_id' => '-1',
				'name'     => _l("Admin Panel"),
			),
			array(
				'store_id' => 0,
				'name'     => _l("All Stores"),
			),
		);

		$data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		//Action Buttons
		$data['save']   = site_url('admin/setting/url_alias/update', 'url_alias_id=' . $url_alias_id);
		$data['cancel'] = site_url('admin/setting/url_alias');

		//Render
		$this->response->setOutput($this->render('setting/url_alias_form', $data));
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'setting/url_alias')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify url aliases!");
		}

		if (empty($_POST['alias'])) {
			$this->error['alias'];
		}

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'setting/url_alias')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify url aliases!");
		}

		return empty($this->error);
	}
}
