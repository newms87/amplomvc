<?php

class Table extends Library
{
	private $file;
	private $columns;
	private $rows;

	private $path;

	public function init()
	{
		$this->file    = '';
		$this->columns = array();
		$this->rows    = array();
		$this->path    = '';
	}

	public function setColumns($columns)
	{
		foreach ($columns as $key => $col) {
			if (!is_array($col)) {
				unset($columns[$key]);
			}
		}

		$this->columns = $columns;
	}

	public function setRows($rows)
	{
		$this->rows = $rows;
	}

	public function setTemplate($file, $theme = null)
	{
		$this->file = is_file($file) ? $file : $this->theme->getFile('template/' . $file, $theme);

		if (!$this->file) {
			trigger_error(_l("The template file %s does not exist.", $file));
		}
	}

	public function mapAttribute($attr, $values)
	{
		if (is_array($values) && !empty($this->columns)) {
			foreach ($this->columns as $slug => &$column) {
				$column[$attr] = isset($values[$slug]) ? $values[$slug] : null;
			}
		}
	}

	public function render($data = array())
	{
		$this->prepare($data);

		$data['columns'] = $this->columns;
		$data['rows']    = $this->rows;

		$data += array(
			'row_id' => '',
		);

		return render_file($this->file, $data);
	}

	private function prepare(&$data)
	{
		$data += array(
			'index' => null,
			'sort'  => array(),
		);

		if (empty($data['listing_path'])) {
			$data['listing_path'] = $this->route->getPath();
		}

		if (!$this->file || !is_file($this->file)) {
			trigger_error(_l("You must set the template for the form before building!"));
			exit();
		}

		//Normalize Columns
		foreach ($this->columns as $slug => &$column) {
			$column += array(
				'type'     => 'text',
				'label'    => isset($column['display_name']) ? $column['display_name'] : $slug,
				'sort'     => false,
				'filter'   => false,
				'align'    => 'center',
				'editable' => null,
			);

			//Set Class
			$column['#class'] = (isset($column['#class']) ? $column['#class'] . ' ' : '') . $slug . ' ' . $column['align'];

			if ($column['sort']) {
				$sort_class = '';

				if (!is_array($column['sort'])) {
					$column['sort'] = is_string($column['sort']) ? (array)$column['sort'] : array($slug => 'ASC');
				}

				foreach ($data['sort'] as $s => $ord) {
					if (isset($column['sort'][$s])) {
						$sort_class         = strtoupper($ord) === 'DESC' ? 'DESC' : 'ASC';
						$column['sort'][$s] = $sort_class === 'DESC' ? 'ASC' : 'DESC';
					}
				}

				if (!isset($column['sort_class'])) {
					$column['sort_class'] = strtolower($sort_class);
				}
			}

			//This sets a blank option in a dropdown by default
			if ($column['filter']) {
				if (in_array($column['type'], array(
						'select',
						'multiselect'
					)) && !isset($column['filter_blank'])
				) {
					$column['filter_blank'] = true;
				}

				if ($column['filter'] === true) {
					$column['filter'] = $column['type'];
				}

				if (!isset($column['filter_key'])) {
					$column['filter_key'] = $slug;
				}
			}

			if (empty($column['build'])) {
				$column['build'] = array();
			}

			//If Field is set, assume this came from Table Model, and therefore can be edited
			if ($column['editable'] === null) {
				$column['editable'] = isset($column['Field']);
			}

			if ($column['editable'] && !is_string($column['editable'])) {
				$column['editable'] = $column['type'];
			}

			if (!isset($column['editable_data'])) {
				$column['editable_data'] = !empty($column['build']['data']) ? $column['build']['data'] : array();
			}

			switch ($column['type']) {
				case 'pk':
					$column['editable'] = false;
					break;

				case 'text':
					if (isset($column['Length']) && ($column['Length'] == 0 || $column['Length'] > 100)) {
						$column['type'] = 'longtext';
					}
					break;
				case 'image':
				case 'link-image':
					if (!isset($column["sort"])) {
						$column['sort'] = array("__image_sort__" . $slug => 'ASC');
					}
					break;

				case 'multiselect':
					foreach ($this->rows as &$row) {
						if (!is_array($row[$slug])) {
							$row[$slug] = array($row[$slug]);
						}
					}
					unset($row);

				case 'select':
					$column['build'] += array(
						'data'  => array('' => _l('(None)')),
						'value' => 'key',
						'label' => 'name',
					);

					$build_value = $column['build']['value'];
					$build_label = $column['build']['label'];
					$build_data  = array();

					foreach ($column['build']['data'] as $key => $bd_item) {
						if (is_array($bd_item)) {
							//Validate Data keys and values are set
							if (($build_value !== false && !isset($bd_item[$build_value]))) {
								trigger_error(_l("Build Error: Row %s does not have index %s to use as the value!", $key, $build_value));
								exit();
							} elseif (!isset($bd_item[$build_label])) {
								trigger_error(_l("Build Error: Row %s does not have index %s to use as the label!", $key, $build_label));
								exit();
							}

							$build_data[$key] = array(
								'key'  => $build_value === false ? $key : $bd_item[$build_value],
								'name' => $bd_item[$build_label],
							);
						} else {
							$build_data[$key] = array(
								'key'  => $key,
								'name' => $bd_item,
							);
						}
					}

					if ($column['filter_blank'] && !isset($build_data[''])) {
						$build_data = array(
								'' => array(
									'key'  => '',
									'name' => '&nbsp;'
								)
							) + $build_data;
					}

					$column['build'] = array(
						'type'   => $column['type'],
						'name'   => !empty($column['Field']) ? $column['Field'] : '',
						'data'   => $build_data,
						'select' => '',
						'value'  => 'key',
						'label'  => 'name',
					);

					break;
			}

			if (!empty($column['filter']) && is_string($column['filter'])) {
				switch ($column['filter']) {
					case 'multiselect':
						if (!empty($column['filter_value']) && !is_array($column['filter_value'])) {
							$column['filter_value'] = array($column['filter_value']);
						}
						break;

					case 'pk':
					case 'pk-int':
					case 'int':
					case 'float':
					case 'decimal':
						if (is_numeric($column['filter_value'])) {
							$column['filter_value'] = array(
								'gte' => $column['filter_value'],
								'lte' => $column['filter_value'],
							);
						}
						break;

					case 'date':
					case 'time':
					case 'datetime':
						if (is_string($column['filter_value'])) {
							$column['filter_value'] = array(
								'gte' => $column['filter_value'],
								'lte' => $column['filter_value'],
							);
						}
						break;

				}
			}

			if ($column['editable']) {
				$column['#class'] .= ' editable';
			}
		}
		unset($column);
	}
}
