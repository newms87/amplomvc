<?php
class App_Controller_Admin_Design_Layout extends Controller
{
	public function index()
	{
		$this->getList();
	}

	public function update()
	{
		if (IS_POST && $this->validateForm()) {
			//Insert
			if (empty($_GET['layout_id'])) {
				$this->Model_Design_Layout->addLayout($_POST);
			} //Update
			else {
				$this->Model_Design_Layout->editLayout($_GET['layout_id'], $_POST);
			}

			if (!$this->message->has('error', 'warning')) {
				message('success', _l("Success: You have modified layouts!"));

				redirect('admin/design/layout');
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		if (isset($_GET['layout_id']) && $this->validateDelete()) {
			$this->Model_Design_Layout->deleteLayout($_GET['layout_id']);

			if (!$this->message->has('error', 'warning')) {
				message('success', _l("Success: You have modified layouts!"));

				redirect('admin/design/layout');
			}
		}

		$this->getList();
	}

	public function batch_update()
	{
		if (!empty($_GET['selected']) && isset($_GET['action']) && user_can('w', 'admin/design/layout')) {
			foreach ($_GET['selected'] as $layout_id) {
				switch ($_GET['action']) {
					case 'enable':
						$this->Model_Design_Layout->editLayout($layout_id, array('status' => 1));
						break;
					case 'disable':
						$this->Model_Design_Layout->editLayout($layout_id, array('status' => 0));
						break;
					case 'delete':
						$this->Model_Design_Layout->deleteLayout($layout_id);
						break;
					case 'copy':
						$this->Model_Design_Layout->copyLayout($layout_id);
						break;

					default:
						break 2; // Break For Loop
				}
			}

			if (!$this->error && !$this->message->has('error', 'warning')) {
				message('success', _l("Success: You have modified layouts!"));

				redirect('admin/design/layout', $this->url->getQueryExclude('action'));
			}
		}

		$this->getList();
	}

	private function getList()
	{
		//Page Head
		set_page_info('title', _l("Layouts"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Layouts"), site_url('admin/design/layout'));

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

		$layout_total = $this->Model_Design_Layout->getTotalLayouts($filter);
		$layouts      = $this->Model_Design_Layout->getLayouts($sort + $filter);

		$url_query = $this->url->getQueryExclude('layout_id');

		foreach ($layouts as &$layout) {
			$layout['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/design/layout/update', 'layout_id=' . $layout['layout_id'])
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/design/layout/delete', 'layout_id=' . $layout['layout_id'] . '&' . $url_query)
				)
			);

			$routes           = $this->Model_Design_Layout->getLayoutRoutes($layout['layout_id']);
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

		$data['batch_update'] = 'design/layout/batch_update';

		//Render Limit Menu
		$data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total  = $layout_total;
		$data['pagination'] = $this->pagination->render();

		//Action Buttons
		$data['insert'] = site_url('admin/design/layout/update');

		//Render
		output($this->render('design/layout_list', $data));
	}

	private function getForm()
	{
		//Page Head
		set_page_info('title', _l("Layouts"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Layouts"), site_url('admin/design/layout'));

		//Insert or Update
		$layout_id = isset($_GET['layout_id']) ? (int)$_GET['layout_id'] : 0;

		//Load Information
		if ($layout_id && !IS_POST) {
			$layout_info = $this->Model_Design_Layout->getLayout($layout_id);

			$layout_info['routes'] = $this->Model_Design_Layout->getLayoutRoutes($layout_id);
		}

		//Set Values or Defaults
		$defaults = array(
			'name'   => '',
			'routes' => array(),
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$data[$key] = $_POST[$key];
			} elseif (isset($layout_info[$key])) {
				$data[$key] = $layout_info[$key];
			} else {
				$data[$key] = $default;
			}
		}

		//Template Defaults
		$data['routes']['__ac_template__'] = array(
			'route'    => '',
		);

		//Template Data
		$data['data_stores'] = $this->Model_Setting_Store->getStores();

		//Action Buttons
		$data['save']   = site_url('admin/design/layout/update', 'layout_id=' . $layout_id);
		$data['cancel'] = site_url('admin/design/layout');

		//Render
		output($this->render('design/layout_form', $data));
	}

	private function validateForm()
	{
		if (!user_can('w', 'admin/design/layout')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify layouts!");
		}

		if (!validate('text', $_POST['name'], 3, 64)) {
			$this->error['name'] = _l("Layout Name must be between 3 and 64 characters!");
		}

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!user_can('w', 'admin/design/layout')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify layouts!");
		}

		if (option('config_default_layout_id') == $_GET['layout_id']) {
			$this->error['warning'] = _l("Warning: This layout cannot be deleted as it is currently assigned as the default store layout!");
		}

		return empty($this->error);
	}
}
