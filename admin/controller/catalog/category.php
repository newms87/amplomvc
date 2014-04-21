<?php
class Admin_Controller_Catalog_Category extends Controller
{
	public function index()
	{
		$this->getList();
	}

	public function update()
	{
		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['category_id'])) {
				$this->Model_Catalog_Category->addCategory($_POST);
			} //Update
			else {
				$this->Model_Catalog_Category->editCategory($_GET['category_id'], $_POST);
			}

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified categories!"));

				$this->url->redirect('catalog/category');
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		if (!empty($_GET['category_id']) && $this->validateDelete()) {
			$this->Model_Catalog_Category->deleteCategory($_GET['category_id']);

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified categories!"));

				$this->url->redirect('catalog/category');
			}
		}

		$this->getList();
	}

	public function batch_update()
	{
		if (!empty($_GET['selected']) && isset($_GET['action'])) {
			foreach ($_GET['selected'] as $category_id) {
				switch ($_GET['action']) {
					case 'enable':
						$this->Model_Catalog_Category->updateField($category_id, array('status' => 1));
						break;
					case 'disable':
						$this->Model_Catalog_Category->updateField($category_id, array('status' => 0));
						break;
					case 'delete':
						$this->Model_Catalog_Category->deleteCategory($category_id);
						break;
					case 'copy':
						$this->Model_Catalog_Category->copyCategory($category_id);
						break;
				}

				if ($this->error) {
					break;
				}
			}

			if (!$this->error && !$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified categories!"));

				$this->url->redirect('catalog/category', $this->url->getQueryExclude('action'));
			}
		}

		$this->getList();
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Category"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Category"), $this->url->link('catalog/category'));

		//The Table Columns
		$columns = array();

		$columns['thumb'] = array(
			'type'         => 'image',
			'display_name' => _l("Image"),
			'filter'       => false,
			'sortable'     => true,
			'sort_value'   => '__image_sort__image',
		);

		$columns['name'] = array(
			'type'         => 'text',
			'display_name' => _l("Category Name"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['stores'] = array(
			'type'         => 'multiselect',
			'display_name' => _l("Stores"),
			'filter'       => true,
			'build_config' => array(
				'store_id',
				'name'
			),
			'build_data'   => $this->Model_Setting_Store->getStores(),
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
		);

		//Get Sorted / Filtered Data
		$sort   = $this->sort->getQueryDefaults('name', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		$category_total = $this->Model_Catalog_Category->getTotalCategories($filter);
		$categories     = $this->Model_Catalog_Category->getCategoriesWithParents($sort + $filter);

		$url_query    = $this->url->getQueryExclude('category_id');
		$image_width  = $this->config->get('config_image_admin_list_width');
		$image_height = $this->config->get('config_image_admin_list_height');

		foreach ($categories as &$category) {
			$category['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => $this->url->link('catalog/category/update', 'category_id=' . $category['category_id'])
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => $this->url->link('catalog/category/delete', 'category_id=' . $category['category_id'] . '&' . $url_query)
				)
			);

			$category['name'] = $category['pathname'];

			$category['thumb'] = $this->image->resize($category['image'], $image_width, $image_height);

			$category['stores'] = $this->Model_Catalog_Category->getCategoryStores($category['category_id']);
		}
		unset($category);

		//Build The Table
		$tt_data = array(
			'row_id' => 'category_id',
		);

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($categories);
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

		$data['batch_update'] = 'catalog/category/batch_update';

		//Render Limit Menu
		$data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $category_total;

		$data['pagination'] = $this->pagination->render();

		//Action Buttons
		$data['insert'] = $this->url->link('catalog/category/update');

		//Render
		$this->response->setOutput($this->render('catalog/category_list', $data));
	}

	private function getForm()
	{
		//Page Head
		$this->document->setTitle(_l("Category"));

		//Insert or Update
		$category_id = $data['category_id'] = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Category"), $this->url->link('catalog/category'));

		if ($category_id) {
			$this->breadcrumb->add(_l("Edit"), $this->url->link('catalog/category/update', 'category_id=' . $category_id));
		} else {
			$this->breadcrumb->add(_l("Add"), $this->url->link('catalog/category/update'));
		}

		//Load Information
		$category_info = array();

		if ($this->request->isPost()) {
			$category_info = $_POST;
		} elseif ($category_id) {
			$category_info = $this->Model_Catalog_Category->getCategory($category_id);

			$category_info['stores']  = $this->Model_Catalog_Category->getCategoryStores($category_id);
			$category_info['layouts'] = $this->Model_Catalog_Category->getCategoryLayouts($category_id);
		}

		//Set Values or Defaults
		$defaults = array(
			'parent_id'        => 0,
			'name'             => '',
			'description'      => '',
			'meta_keywords'    => '',
			'meta_description' => '',
			'alias'            => '',
			'image'            => '',
			'sort_order'       => 0,
			'status'           => 1,
			'layouts'          => array(),
			'stores'           => array(0),
		);

		$data += $category_info + $defaults;

		//All other categories to select parent
		$categories = $this->Model_Catalog_Category->getCategoriesWithParents();

		// Remove own id from list
		foreach ($categories as $key => $category) {
			if ($category['category_id'] === $category_id) {
				unset($categories[$key]);
				break;
			}
		}

		//Translations
		$data['translations'] = $this->Model_Catalog_Category->getCategoryTranslations($category_id);

		//Template Data
		$data['data_categories'] = array_merge(array(0 => _l(" --- None --- ")), $categories);
		$data['data_stores']     = $this->Model_Setting_Store->getStores();
		$data['data_layouts']    = array('' => '') + $this->Model_Design_Layout->getLayouts();

		$data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		//Ajax Urls
		$data['url_generate_url'] = $this->url->link('catalog/category/generate_url');

		//Action Buttons
		$data['action'] = $this->url->link('catalog/category/update', 'category_id=' . $category_id);
		$data['cancel'] = $this->url->link('catalog/category');

		//Render
		$this->response->setOutput($this->render('catalog/category_form', $data));
	}

	public function generate_url()
	{
		if (!empty($_POST['name'])) {
			$category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : 0;

			$url = $this->Model_Setting_UrlAlias->getUniqueAlias($_POST['name'], 'catalog/category', 'category_id=' . $category_id);
		} else {
			$url = '';
		}

		$this->response->setOutput($url);
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'catalog/category')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify categories!");
		}

		if (!$this->validation->text($_POST['name'], 2, 64)) {
			$this->error['name'] = _l("Category Name must be between 2 and 64 characters!");
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'catalog/category')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify categories!");
		}

		return $this->error ? false : true;
	}
}
