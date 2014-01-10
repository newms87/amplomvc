<?php
class Admin_Controller_Catalog_ProductClass extends Controller
{
	public function index()
	{
		$this->language->load('catalog/product_class');

		$this->getList();
	}

	public function update()
	{
		$this->language->load('catalog/product_class');

		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['product_class_id'])) {
				$this->Model_Catalog_ProductClass->addProductClass($_POST);
			} //Update
			else {
				$this->Model_Catalog_ProductClass->editProductClass($_GET['product_class_id'], $_POST);
			}

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("You have successfully updated the Product Classes!"));

				$this->url->redirect('catalog/product_class');
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->language->load('catalog/product_class');

		if (!empty($_GET['product_class_id']) && $this->validateDelete()) {
			$this->Model_Catalog_ProductClass->deleteProductClass($_GET['product_class_id']);

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("You have successfully updated the Product Classes!"));

				$this->url->redirect('catalog/product_class');
			}
		}

		$this->getList();
	}

	public function batch_update()
	{
		$this->language->load('catalog/product_class');

		if (!empty($_GET['selected']) && isset($_GET['action'])) {
			foreach ($_GET['selected'] as $product_class_id) {
				switch ($_GET['action']) {
					case 'delete':
						$this->Model_Catalog_ProductClass->deleteProductClass($product_class_id);
						break;

					default:
						break 2; //Break For Loop
				}
			}

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("You have successfully updated the Product Classes!"));
			}
		}

		$this->url->redirect('catalog/product_class', $this->url->getQueryExclude('action', 'action_value'));
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Product Classes"));

		//The Template
		$this->template->load('catalog/product_class_list');

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Product Classes"), $this->url->link('catalog/product_class'));

		//The Table Columns
		$columns = array();

		$columns['name'] = array(
			'type'         => 'text',
			'display_name' => _l("Name:"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['front_template'] = array(
			'type'         => 'text',
			'display_name' => _l("Front End Template:"),
		);

		$columns['admin_template'] = array(
			'type'         => 'text',
			'display_name' => _l("Admin Template:"),
		);


		//Get Sorted / Filtered Data
		$sort   = $this->sort->getQueryDefaults('name', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		$product_class_total = $this->Model_Catalog_ProductClass->getTotalProductClasses($filter);
		$product_classes     = $this->Model_Catalog_ProductClass->getProductClasses($sort + $filter);

		$url_query = $this->url->getQueryExclude('product_class_id');

		foreach ($product_classes as &$product_class) {
			$product_class['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => $this->url->link('catalog/product_class/update', 'product_class_id=' . $product_class['product_class_id'])
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => $this->url->link('catalog/product_class/delete', 'product_class_id=' . $product_class['product_class_id'] . '&' . $url_query)
				)
			);

			$front_list = '';

			if (!empty($product_class['front_template'])) {
				foreach ($product_class['front_template'] as $theme => $template) {
					$front_list .= _l("Template (<strong>%s</strong>): <strong>%s</strong>", $theme, ($template ? $template : _l("Default Template"))) . '<br />';
				}
			}

			$product_class['front_template'] = $front_list;

			$admin_list = '';

			if (!empty($product_class['front_template'])) {
				foreach ($product_class['admin_template'] as $theme => $template) {
					$admin_list .= _l("Template (<strong>%s</strong>): <strong>%s</strong>", $theme, ($template ? $template : _l("Default Template"))) . '<br />';
				}
			}

			$product_class['admin_template'] = $admin_list;
		}
		unset($product_class);

		//Build The Table
		$tt_data = array(
			'row_id' => 'product_class_id',
		);

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($product_classes);
		$this->table->setTemplateData($tt_data);
		$this->table->mapAttribute('filter_value', $filter);

		$this->data['list_view'] = $this->table->render();

		//Batch Actions
		$this->data['batch_actions'] = array(
			'delete' => array(
				'label' => _l("Delete"),
			),
		);

		$this->data['batch_update'] = 'catalog/product_class/batch_update';

		//Render Limit Menu
		$this->data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $product_class_total;

		$this->data['pagination'] = $this->pagination->render();

		//Action Buttons
		$this->data['insert'] = $this->url->link('catalog/product_class/update');

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
		$this->document->setTitle(_l("Product Classes"));

		//The template
		$this->template->load('catalog/product_class_form');

		//Insert or Update
		$product_class_id = isset($_GET['product_class_id']) ? (int)$_GET['product_class_id'] : 0;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Product Classes"), $this->url->link('catalog/product_class'));

		if ($product_class_id) {
			$this->breadcrumb->add(_l("Edit"), $this->url->link('catalog/product_class/update', 'product_class_id=' . $product_class_id));
		} else {
			$this->breadcrumb->add(_l("Add"), $this->url->link('catalog/product_class/update'));
		}

		//Load Information
		if ($product_class_id && !$this->request->isPost()) {
			$product_class_info = $this->Model_Catalog_ProductClass->getProductClass($product_class_id);
		}
		else {
			$product_class_info = $_POST;
		}

		//Set Values or Defaults
		$defaults = array(
			'name'             => '',
			'front_template'   => array(),
			'front_controller' => array(),
			'admin_template'   => array(),
			'admin_controller' => array(),
		);

		$this->data += $product_class_info + $defaults;

		//Template Data
		$this->data['data_front_templates'] = $this->Model_Catalog_ProductClass->getFrontTemplates();
		$this->data['data_admin_templates'] = $this->Model_Catalog_ProductClass->getAdminTemplates();

		//Action Buttons
		$this->data['save']   = $this->url->link('catalog/product_class/update', 'product_class_id=' . $product_class_id);
		$this->data['cancel'] = $this->url->link('catalog/product_class');

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
		if (!$this->user->can('modify', 'catalog/product_class')) {
			$this->error['warning'] = _l("You do not have permission to modify Product Classes");
		}

		if (!$this->validation->text($_POST['name'], 3, 45)) {
			$this->error['name'] = _l("The name for the Product Class must be between 3 and 45 characters!");
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'catalog/product_class')) {
			$this->error['warning'] = _l("You do not have permission to modify Product Classes");
		}

		return $this->error ? false : true;
	}
}
