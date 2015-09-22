<?php

class App_Controller_Admin_Category extends App_Controller_Table
{
	protected $model = array(
		'class' => 'App_Model_Category',
		'path'  => 'admin/category',
		'label' => 'name',
		'value' => 'category_id',
	);

	public function index()
	{
		//Page Head
		set_page_info('title', _l("Categories"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Categories"), site_url('admin/category'));

		//Batch Actions
		$actions = array(
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

		$data['batch_action'] = array(
			'actions' => $actions,
			'url'     => site_url('admin/category/batch-action'),
		);

		//Response
		output($this->render('category/list', $data));
	}

	public function listing($options = array())
	{
		$options += array(
			'sort_default' => array('name' => 'ASC'),
		);

		return parent::listing($options);
	}

	public function form()
	{
		//Page Head
		set_page_info('title', _l("Category"));

		//Insert or Update
		$category_id = _get('category_id', null);

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Categories"), site_url('admin/category'));
		breadcrumb($category_id ? _l("Edit") : _l("New"), site_url('admin/category/form', 'category_id=' . $category_id));

		//The Data
		$category = $_POST;

		if ($category_id && !IS_POST) {
			$category = $this->Model_Category->getRecord($category_id);
		}

		$defaults = array(
			'category_id' => $category_id,
			'type'        => 'page',
			'name'        => '',
			'parent_id'   => 0,
			'title'       => 'New Category',
			'status'      => 1,
		);

		$category += $defaults;

		$category['data_parents'] = array('' => '(None)') + $this->Model_Category->getRecords(array('name' => 'ASC'), array('!category_id' => $category_id), array('cache' => true));

		//Response
		output($this->render('category/form', $category));
	}

	public function batch_action()
	{
		$batch  = (array)_request('batch');
		$action = _request('action');
		$value  = _request('value');

		foreach ($batch as $category_id) {
			switch ($action) {
				case 'enable':
					$this->Model_Category->save($category_id, array('status' => 1));
					break;

				case 'disable':
					$this->Model_Category->save($category_id, array('status' => 0));
					break;

				case 'delete':
					$this->Model_Category->remove($category_id);
					break;
			}
		}

		if ($this->Model_Category->hasError()) {
			message('error', $this->Model_Category->fetchError());
		} else {
			message('success', _l("Users were updated successfully!"));
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/category');
		}
	}
}
