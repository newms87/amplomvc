<?php
class Admin_Controller_Design_Layout extends Controller
{
	public function index()
	{
		$this->language->load('design/layout');

		$this->getList();
	}

	public function update()
	{
		$this->language->load('design/layout');

		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['layout_id'])) {
				$this->Model_Design_Layout->addLayout($_POST);
			} //Update
			else {
				$this->Model_Design_Layout->editLayout($_GET['layout_id'], $_POST);
			}

			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));

				$this->url->redirect('design/layout');
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->language->load('design/layout');

		if (isset($_GET['layout_id']) && $this->validateDelete()) {
			$this->Model_Design_Layout->deleteLayout($_GET['layout_id']);

			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));

				$this->url->redirect('design/layout');
			}
		}

		$this->getList();
	}

	public function batch_update()
	{
		$this->language->load('design/layout');

		if (!empty($_GET['selected']) && isset($_GET['action']) && $this->user->hasPermission('modify', 'design/layout')) {
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

			if (!$this->error && !$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));

				$this->url->redirect('design/layout', $this->url->getQueryExclude('action'));
			}
		}

		$this->getList();
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle($this->_('head_title'));

		//The Template
		$this->template->load('design/layout_list');

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('design/layout'));

		//The Table Columns
		$columns = array();

		$columns['name'] = array(
			'type'         => 'text',
			'display_name' => $this->_('column_name'),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['routes'] = array(
			'type'         => 'text',
			'display_name' => $this->_('column_routes'),
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
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('design/layout/update', 'layout_id=' . $layout['layout_id'])
				),
				'delete' => array(
					'text' => $this->_('text_delete'),
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
				'label' => $this->_('text_enable')
			),
			'disable' => array(
				'label' => $this->_('text_disable'),
			),
			'copy'    => array(
				'label' => $this->_('text_copy'),
			),
			'delete'  => array(
				'label' => $this->_('text_delete'),
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
		$this->document->setTitle($this->_('head_title'));

		//The Template
		$this->template->load('design/layout_form');

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('design/layout'));

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

		//Additional Data
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
		if (!$this->user->hasPermission('modify', 'design/layout')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if (!$this->validation->text($_POST['name'], 3, 64)) {
			$this->error['name'] = $this->_('error_name');
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'design/layout')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if ($this->config->get('config_default_layout_id') == $_GET['layout_id']) {
			$this->error['warning'] = $this->_('error_default');
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
			$this->error[$layout_id]['warning_product'] = $this->_('error_product', $product_total);
		}

		if ($this->Model_Catalog_Category->getTotalCategories($filter)) {
			$this->error[$layout_id]['warning_category'] = $this->_('error_category', $category_total);
		}

		if ($this->Model_Catalog_Information->getTotalInformations($filter)) {
			$this->error[$layout_id]['warning_information'] = $this->_('error_information', $information_total);
		}

		return !isset($this->error[$layout_id]);
	}
}
