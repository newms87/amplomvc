<?php

class App_Controller_Admin_Localisation_Country extends App_Controller_Table
{
	protected $model = array(
		'title' => 'Country',
		'class' => 'App_Model_Localisation_Country',
		'path'  => 'admin/localisation_country',
		'label' => 'name',
		'value' => 'country_id',
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
				'country_id' => 0,
				'name'              => '',
				'iso_code_2'        => '',
				'iso_code_3'        => '',
				'address_format'    => '',
				'postcode_required' => '',
				'status'            => 1,
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
							$this->instance->save($category_id, array('status' => 1));
							break;

						case 'disable':
							$this->instance->save($category_id, array('status' => 0));
							break;

						case 'delete':
							$this->instance->remove($category_id);
							break;
					}
				}
			},
		);

		return parent::batch_action($options);
	}
}
