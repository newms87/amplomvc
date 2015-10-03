<?php

class App_Controller_Admin_Category extends App_Controller_Table
{
	protected $model = array(
		'title' => 'Category',
		'class' => 'App_Model_Category',
		'path'  => 'admin/category',
		'label' => 'name',
		'value' => 'category_id',
	);

	public function index($options = array())
	{
		$options += array(
			'batch_action' => array(
				'actions' => array(
					'enable'  => array(
						'label' => _l("Enable"),
					),
					'disable' => array(
						'label' => _l("Disable"),
					),
					'delete'  => array(
						'label' => _l("Delete"),
					),
				),
			),
		);

		return parent::index($options);
	}

	public function listing($options = array())
	{
		$options += array(
			'sort_default' => array('name' => 'ASC'),
		);

		return parent::listing($options);
	}

	public function form($options = array())
	{
		$options = array(
			'defaults' => array(
				'category_id' => 0,
				'type'        => 'page',
				'name'        => '',
				'parent_id'   => 0,
				'title'       => 'New Category',
				'status'      => 1,
				'sort_order'  => 0,
			),
			'template' => 'category/form',
			'data'     => array(
				'data_parents' => array('' => '(None)') + $this->Model_Category->getRecords(array('name' => 'ASC'), array('!category_id' => _request('category_id')), array('cache' => true)),
			),
		);

		return parent::form($options);
	}

	public function batch_action($options = array())
	{
		$options += array(
			'callback' => function ($batch, $action, $value) {
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
			},
		);

		return parent::batch_action($options);
	}
}
