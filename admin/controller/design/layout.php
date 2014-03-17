<?php
class Admin_Controller_Design_Layout extends Controller
{
	public function index()
	{
		$this->getList();
	}

	public function update()
	{
		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['layout_id'])) {
				$this->Model_Design_Layout->addLayout($_POST);
			} //Update
			else {
				$this->Model_Design_Layout->editLayout($_GET['layout_id'], $_POST);
			}

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified layouts!"));

				$this->url->redirect('design/layout');
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		if (isset($_GET['layout_id']) && $this->validateDelete()) {
			$this->Model_Design_Layout->deleteLayout($_GET['layout_id']);

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified layouts!"));

				$this->url->redirect('design/layout');
			}
		}

		$this->getList();
	}

	public function batch_update()
	{
		if (!empty($_GET['selected']) && isset($_GET['action']) && $this->user->can('modify', 'design/layout')) {
			foreach ($_GET['selected'] as $layout_id) {
				switch ($_GET['action']) {
					case 'enable':
						$this->Model_Design_Layout->editLayout($layout_id, array('status' => 1));
						break;
					case 'disable':
						$this->Model_Design_Layout->editLayout($layout_id, array('status' => 0));
						break;
					case 'delete':
						if ($this->canDelete($layout_id)) {
							$this->Model_Design_Layout->deleteLayout($layout_id);
						}
						break;
					case 'copy':
						$this->Model_Design_Layout->copyLayout($layout_id);
						break;

					default:
						break 2; // Break For Loop
				}
			}

			if (!$this->error && !$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified layouts!"));

				$this->url->redirect('design/layout', $this->url->getQueryExclude('action'));
			}
		}

		$this->getList();
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Layouts"));

		//The Template
		$this->view->load('design/layout_list');

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Layouts"), $this->url->link('design/layout'));

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
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		$layout_total = $this->Model_Design_Layout->getTotalLayouts($filter);
		$layouts      = $this->Model_Design_Layout->getLayouts($sort + $filter);

		$url_query = $this->url->getQueryExclude('layout_id');

		foreach ($layouts as &$layout) {
			$layout['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => $this->url->link('design/layout/update', 'layout_id=' . $layout['layout_id'])
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => $this->url->link('design/layout/delete', 'layout_id=' . $layout['layout_id'] . '&' . $url_query)
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

		$this->data['list_view'] = $this->table->render();

		//Batch Actions
		$this->data['batch_actions'] = array(
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

		$this->data['batch_update'] = 'design/layout/batch_update';

		//Render Limit Menu
		$this->data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total  = $layout_total;
		$this->data['pagination'] = $this->pagination->render();

		//Action Buttons
		$this->data['insert'] = $this->url->link('design/layout/update');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		//Page Head
		$this->document->setTitle(_l("Layouts"));

		//The Template
		$this->view->load('design/layout_form');

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Layouts"), $this->url->link('design/layout'));

		//Insert or Update
		$layout_id = isset($_GET['layout_id']) ? (int)$_GET['layout_id'] : 0;

		//Load Information
		if ($layout_id && !$this->request->isPost()) {
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
				$this->data[$key] = $_POST[$key];
			} elseif (isset($layout_info[$key])) {
				$this->data[$key] = $layout_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		//Template Defaults
		$this->data['routes']['__ac_template__'] = array(
			'store_id' => 1,
			'route'    => '',
		);

		//Template Data
		$this->data['data_stores'] = $this->Model_Setting_Store->getStores();

		//Action Buttons
		$this->data['save']   = $this->url->link('design/layout/update', 'layout_id=' . $layout_id);
		$this->data['cancel'] = $this->url->link('design/layout');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'design/layout')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify layouts!");
		}

		if (!$this->validation->text($_POST['name'], 3, 64)) {
			$this->error['name'] = _l("Layout Name must be between 3 and 64 characters!");
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'design/layout')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify layouts!");
		}

		if ($this->config->get('config_default_layout_id') == $_GET['layout_id']) {
			$this->error['warning'] = _l("Warning: This layout cannot be deleted as it is currently assigned as the default store layout!");
		}

		$this->canDelete($_GET['layout_id']);

		return $this->error ? false : true;
	}

	private function canDelete($layout_id)
	{
		$filter = array(
			'layouts' => array($layout_id),
		);

		if ($this->Model_Catalog_Product->getTotalProducts($filter)) {
			$this->error[$layout_id]['warning_product'] = _l("Warning: This layout cannot be deleted as it is currently assigned to %s products!", $product_total);
		}

		if ($this->Model_Catalog_Category->getTotalCategories($filter)) {
			$this->error[$layout_id]['warning_category'] = _l("Warning: This layout cannot be deleted as it is currently assigned to %s categories!", $category_total);
		}

		if ($this->Model_Catalog_Information->getTotalInformations($filter)) {
			$this->error[$layout_id]['warning_information'] = _l("Warning: This layout cannot be deleted as it is currently assigned to %s information pages!", $information_total);
		}

		return !isset($this->error[$layout_id]);
	}
}
