<?php

class App_Controller_Admin_Settings_Layout extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("Layouts"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Layouts"), site_url('admin/settings/layout'));

		//The Table Columns
		$columns = array();

		$columns['name'] = array(
			'type'         => 'text',
			'display_name' => _l("Layout Name"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['routes'] = array(
			'type'         => 'text',
			'display_name' => _l("Routes"),
			'filter'       => false,
			'sortable'     => false,
		);

		//Get Sorted / Filtered Data
		$sort   = $this->sort->getQueryDefaults('name', 'ASC');
		$filter = _get('filter', array());

		list($layouts, $layout_total) = $this->Model_Layout->getRecords($sort, $filter);

		$url_query = $this->url->getQueryExclude('layout_id');

		foreach ($layouts as &$layout) {
			$layout['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/settings/layout/form', 'layout_id=' . $layout['layout_id'])
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/settings/layout/delete', 'layout_id=' . $layout['layout_id'] . '&' . $url_query)
				)
			);

			$routes           = $this->Model_Layout->getRoutes($layout['layout_id']);
			$layout['routes'] = implode('<br />', array_column($routes, 'route'));
		}
		unset($layout);

		//Build The Table
		$tt_data = array(
			'row_id' => 'layout_id',
		);

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($layouts);
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
			'copy'    => array(
				'label' => _l("Copy"),
			),
			'delete'  => array(
				'label' => _l("Delete"),
			),
		);

		$data['batch_update'] = 'layout/batch_update';

		//Render Limit Menu
		$data['limits'] = $this->sort->renderLimits();
		$data['total']  = $layout_total;

		//Action Buttons
		$data['insert'] = site_url('admin/settings/layout/form');

		//Render
		output($this->render('layout/list', $data));
	}

	public function form()
	{
		//Page Head
		set_page_info('title', _l("Layouts"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Layouts"), site_url('admin/settings/layout'));

		//Insert or Update
		$layout_id = _get('layout_id');

		$layout = $_POST;

		//Load Information
		if ($layout_id && !IS_POST) {
			$layout           = $this->Model_Layout->getRecord($layout_id);
			$layout['routes'] = $this->Model_Layout->getRoutes($layout_id);
		}

		//Set Values or Defaults
		$defaults = array(
			'name'   => '',
			'routes' => array(),
		);

		$layout += $defaults;

		//Template Defaults
		$layout['routes']['__ac_template__'] = array(
			'route' => '',
		);

		//Template Data
		$layout['data_stores'] = $this->Model_Site->getRecords(array('cache' => true));

		//Action Buttons
		$layout['save']   = site_url('admin/settings/layout/save', 'layout_id=' . $layout_id);
		$layout['cancel'] = site_url('admin/settings/layout');

		//Render
		output($this->render('layout/form', $layout));
	}

	public function save()
	{
		if ($layout_id = $this->Model_Layout->save(_get('layout_id'), $_POST)) {
			message('success', _l("Success: You have modified layouts!"));
			message('data', array('layout_id' => $layout_id));
		} else {
			message('error', $this->Model_Layout->getError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/settings/layout');
		}
	}

	public function delete()
	{
		if ($this->Model_Layout->remove(_get('layout_id'))) {
			message('success', _l("The layout has been removed."));
		} else {
			message('error', $this->Model_Layout->getError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/settings/layout');
		}
	}

	public function batch_update()
	{
		if (!empty($_GET['selected']) && isset($_GET['action']) && user_can('w', 'admin/settings/layout')) {
			foreach ($_GET['selected'] as $layout_id) {
				switch ($_GET['action']) {
					case 'enable':
						$this->Model_Layout->save($layout_id, array('status' => 1));
						break;
					case 'disable':
						$this->Model_Layout->save($layout_id, array('status' => 0));
						break;
					case 'delete':
						$this->Model_Layout->remove($layout_id);
						break;
					case 'copy':
						$this->Model_Layout->copy($layout_id);
						break;

					default:
						break 2; // Break For Loop
				}
			}

			if (!$this->error && !$this->message->has('error', 'warning')) {
				message('success', _l("Success: You have modified layouts!"));

				redirect('admin/settings/layout', $this->url->getQueryExclude('action'));
			}
		}

		redirect('admin/settings/layout');
	}
}
