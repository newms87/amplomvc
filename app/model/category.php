<?php

class App_Model_Category extends App_Model_Table
{
	protected $table = 'category', $primary_key = 'category_id';

	public function save($category_id, $category)
	{
		if (isset($category['name'])) {
			if (!validate('text', $category['name'], 2, 128)) {
				$this->error['name'] = _l("Name must be between 2 and 128 characters!");
			}
		} elseif (!$category_id) {
			$this->error['name'] = _l("Name is required.");
		}

		if (!empty($category['parent_id']) && $category_id == $category['parent_id']) {
			$this->error['parent_id'] = _l("Cannot set category as parent of itself.");
		}

		if ($this->error) {
			return false;
		}

		if (!$category_id) {
			$category['date'] = $this->date->now();
		}

		return parent::save($category_id, $category);
	}

	public function getColumns($filter = array(), $merge = array())
	{
		$merge += array(
			'parent_id' => array(
				'type'   => 'select',
				'label'  => _l("Parent"),
				'build'  => array(
					'data'  => $this->Model_Category->getRecords(array('name' => 'ASC'), null, array('cache' => true)),
					'label' => 'name',
					'value' => 'category_id',
				),
				'filter' => 'multiselect',
				'sort'   => true,
			),
			'status'    => array(
				'type'   => 'select',
				'label'  => _l("Status"),
				'build'  => array(
					'data' => array(
						0 => _l("Disabled"),
						1 => _l("Enabled"),
					),
				),
				'filter' => true,
				'sort'   => true,
			),
		);

		return parent::getColumns($filter, $merge);
	}
}
