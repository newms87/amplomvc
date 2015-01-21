<?php

/**
 * Title: URL Aliases
 * Icon: alias.png
 * Order: 7
 */
class App_Controller_Admin_Settings_UrlAlias extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("URL Aliases"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Settings"), site_url('admin/settings'));
		breadcrumb(_l("URL Aliases"), site_url('admin/settings/url-alias'));

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
		$filter = _get('filter', array());

		$url_alias_total = $this->Model_Setting_UrlAlias->getTotalUrlAliases($filter);
		$url_aliases     = $this->Model_Setting_UrlAlias->getUrlAliases($sort + $filter);

		$url_query = $this->url->getQueryExclude('url_alias_id');

		foreach ($url_aliases as &$url_alias) {
			$url_alias['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/settings/url_alias/update', 'url_alias_id=' . $url_alias['url_alias_id'])
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/settings/url_alias/delete', 'url_alias_id=' . $url_alias['url_alias_id'] . '&' . $url_query)
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

		$data['batch_update'] = 'settings/url_alias/batch_update';

		//Render Limit Menu
		$data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $url_alias_total;

		$data['pagination'] = $this->pagination->render();

		//Action Buttons
		$data['insert'] = site_url('admin/settings/url-alias/update');
		$data['delete'] = site_url('admin/settings/url-alias/delete');
		$data['cancel'] = site_url('admin/settings');

		//Render
		output($this->render('settings/url_alias/list', $data));
	}

	public function update()
	{
		if (IS_POST && $this->validateForm()) {
			//Insert
			if (empty($_GET['url_alias_id'])) {
				$this->Model_Setting_UrlAlias->addUrlAlias($_POST);
			} //Update
			else {
				$this->Model_Setting_UrlAlias->editUrlAlias($_GET['url_alias_id'], $_POST);
			}

			if (!$this->message->has('error', 'warning')) {
				message('success', _l("Success: You have modified the url alias table!"));

				redirect('admin/settings/url_alias');
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		if (!empty($_GET['url_alias_id']) && $this->validateDelete()) {
			$this->Model_Setting_UrlAlias->deleteUrlAlias($_GET['url_alias_id']);

			if (!$this->message->has('error', 'warning')) {
				message('success', _l("Success: You have modified the url alias table!"));

				redirect('admin/settings/url_alias');
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
				message('success', _l("Success: You have modified the url alias table!"));
			}
		}

		redirect('admin/settings/url_alias', $this->url->getQueryExclude('action', 'action_value'));
	}

	public function getForm()
	{
		//Page Head
		set_page_info('title', _l("URL Aliases"));

		//Insert or Update
		$url_alias_id = isset($_GET['url_alias_id']) ? (int)$_GET['url_alias_id'] : 0;

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("URL Aliases"), site_url('admin/settings/url-alias'));

		if (!$url_alias_id) {
			breadcrumb(_l("Add"), site_url('admin/settings/url-alias/udpate'));
		} else {
			breadcrumb(_l("Edit"), site_url('admin/settings/url_alias/update', 'url_alias_id=' . $url_alias_id));
		}

		//Load Information
		$url_alias = $_POST;

		if ($url_alias_id && !IS_POST) {
			$url_alias = $this->Model_Setting_UrlAlias->getUrlAlias($url_alias_id);
		}

		//Load Values or Defaults
		$defaults = array(
			'alias'    => '',
			'path'     => '',
			'query'    => '',
			'redirect' => '',
			'status'   => 1,
		);

		$url_alias += $defaults;

		$url_alias['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		//Action Buttons
		$url_alias['save']   = site_url('admin/settings/url_alias/update', 'url_alias_id=' . $url_alias_id);
		$url_alias['cancel'] = site_url('admin/settings/url-alias');

		//Render
		output($this->render('settings/url_alias/form', $url_alias));
	}

	private function validateForm()
	{
		if (!user_can('w', 'admin/settings/url_alias')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify url aliases!");
		}

		if (empty($_POST['alias'])) {
			$this->error['alias'];
		}

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!user_can('w', 'admin/settings/url_alias')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify url aliases!");
		}

		return empty($this->error);
	}
}
