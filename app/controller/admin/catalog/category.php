<?php
class App_Controller_Admin_Catalog_Category extends Controller
{
	static $can_modify = array(
		'update',
	   'delete',
	   'form',
	   'batch_update',
	);

	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Category"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Category"), site_url('catalog/category'));

		//Batch Actions
		$actions = array(
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

		$data['batch_action'] = array(
			'actions' => $actions,
		   'path' => 'catalog/category/batch_update',
		);

		//The Listing
		$data['listing'] = $this->listing();

		//Permission
		$data['can_modify'] = $this->user->can('modify', 'catalog/category');

		//Action Buttons
		$data['insert'] = site_url('catalog/category/form');

		//Render
		$this->response->setOutput($this->render('catalog/category_list', $data));
	}

	public function update()
	{
		//Insert
		if (empty($_GET['category_id'])) {
			$this->Model_Catalog_Category->add($_POST);
		} //Update
		else {
			$this->Model_Catalog_Category->edit($_GET['category_id'], $_POST);
		}

		if ($this->Model_Catalog_Category->hasError()) {
			$this->message->add('error', $this->Model_Catalog_Category->getError());
		} else {
			$this->message->add('success', _l("The Category has been updated!"));
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} elseif ($this->message->has('error')) {
			$this->form();
		} else {
			redirect('catalog/category');
		}
	}

	public function delete()
	{
		$this->Model_Catalog_Category->remove($_GET['category_id']);

		if ($this->Model_Catalog_Category->hasError()) {
			$this->message->add('error', $this->Model_Catalog_Category->getError());
		} else {
			$this->message->add('notify', _l("Category was deleted!"));
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} else {
			redirect('catalog/category');
		}
	}

	public function listing()
	{
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
		$image_width  = option('config_image_admin_list_width');
		$image_height = option('config_image_admin_list_height');

		foreach ($categories as &$category) {
			if ($this->user->can('modify', 'catalog/category')) {
				$category['actions'] = array(
					'edit'   => array(
						'text' => _l("Edit"),
						'href' => site_url('catalog/category/form', 'category_id=' . $category['category_id'])
					),
					'delete' => array(
						'text' => _l("Delete"),
						'href' => site_url('catalog/category/delete', 'category_id=' . $category['category_id'] . '&' . $url_query)
					)
				);
			}

			$category['name'] = $category['pathname'];

			$category['thumb'] = $this->image->resize($category['image'], $image_width, $image_height);

			$category['stores'] = $this->Model_Catalog_Category->getCategoryStores($category['category_id']);
		}
		unset($category);

		$listing = array(
			'row_id'         => 'category_id',
			'columns'        => $columns,
			'rows'           => $categories,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $category_total,
			'listing_path'   => 'catalog/category/listing',
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
		$this->document->setTitle(_l("Category"));

		//Insert or Update
		$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Category"), site_url('catalog/category'));

		if ($category_id) {
			$this->breadcrumb->add(_l("Edit"), site_url('catalog/category/form', 'category_id=' . $category_id));
		} else {
			$this->breadcrumb->add(_l("Add"), site_url('catalog/category/form'));
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

		$data = $category_info + $defaults;

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
		$data['url_generate_url'] = site_url('catalog/category/generate_url');

		//Action Buttons
		$data['action'] = site_url('catalog/category/update', 'category_id=' . $category_id);
		$data['cancel'] = site_url('catalog/category');

		//Render
		$this->response->setOutput($this->render('catalog/category_form', $data));
	}

	public function batch_update()
	{
		foreach ($_POST['batch'] as $category_id) {
			switch ($_POST['action']) {
				case 'enable':
					$this->Model_Catalog_Category->edit($category_id, array('status' => 1));
					break;

				case 'disable':
					$this->Model_Catalog_Category->edit($category_id, array('status' => 0));
					break;

				case 'delete':
					$this->Model_Catalog_Category->remove($category_id);
					break;

				case 'copy':
					$this->Model_Catalog_Category->copy($category_id);
					break;
			}
		}

		if ($this->Model_Catalog_Category->hasError()) {
			$this->message->add('error', $this->Model_Catalog_Category->getError());
		} else {
			$this->message->add('success', _l("Categories have been modified!"));
		}

		if ($this->request->isAjax()) {
			$this->listing();
		} else {
			redirect('catalog/category');
		}
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
}
