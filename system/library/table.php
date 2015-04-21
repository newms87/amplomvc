<?php

class Table extends Library
{
	private $file;
	private $template_data;

	private $columns;
	private $rows;

	private $path;

	public function init()
	{
		$this->file          = '';
		$this->template_data = array();
		$this->columns       = array();
		$this->rows          = array();
		$this->path          = '';
	}

	public function setColumns($columns)
	{
		$this->columns = $columns;
	}

	public function setRows($rows)
	{
		$this->rows = $rows;
	}

	public function setTemplateData($template_data)
	{
		$this->template_data = $template_data;
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
		if (empty($this->columns)) {
			trigger_error(_l("%s(): You must set the Columns (eg: \$this->table->setColumns(\$columns); ) before mapping data!", __METHOD__));
			exit();
		}

		foreach ($this->columns as $slug => &$column) {
			$column[$attr] = isset($values[$slug]) ? $values[$slug] : null;
		}
	}

	public function render($data = array())
	{
		$this->prepare();

		extract($this->template_data);
		extract($data);

		$columns = $this->columns;
		$rows    = $this->rows;
		if (empty($row_id)) {
			$row_id = '';
		}

		//render the file
		ob_start();

		include(_mod($this->file));

		return ob_get_clean();
	}

	private function prepare()
	{
		if (!$this->file || !is_file($this->file)) {
			trigger_error(_l("You must set the template for the form before building!"));
			exit();
		}

		//Add Sort data
		$this->template_data += $this->sort->getSortData();

		if (empty($this->template_data['listing_path'])) {
			$this->template_data['listing_path'] = $this->route->getPath();
		}

		if (empty($this->template_data['sort_url'])) {
			$this->template_data['sort_url'] = site_url($this->template_data['listing_path'], $this->url->getQueryExclude('sort', 'order', 'page'));
		}

		if (empty($this->template_data['filter_url'])) {
			$this->template_data['filter_url'] = site_url($this->template_data['listing_path'], $this->url->getQueryExclude('filter', 'page'));
		}

		//Normalize Columns
		foreach ($this->columns as $slug => &$column) {

			if (!isset($column['type'])) {
				trigger_error(_l("Invalid table column! The type was not set for %s!", $slug));
				exit();
			}

			$default_values = array(
				'display_name' => $slug,
				'filter'       => false,
				'type'         => 'text',
				'align'        => 'center',
				'sortable'     => false,
				'editable'     => null,
			);

			$column += $default_values;

			//Set Class
			$column['#class'] = (isset($column['#class']) ? $column['#class'] . ' ' : '') . $slug . ' ' . $column['align'];

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
			}

			//Backwards compat w/ build_config / build_data
			if (empty($column['build'])) {
				$column['build'] = array();

				if (!empty($column['build_data'])) {
					$column['build']['data'] = $column['build_data'];
				}

				if (!empty($column['build_config'])) {
					list($column['build']['value'], $column['build']['label']) = $column['build_config'];
				}
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

			if (!isset($column["sort_value"])) {
				$column["sort_value"] = $slug;
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
					if (!isset($column["sort_value"])) {
						$column['sort_value'] = "__image_sort__" . $slug;
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
								'low'  => $column['filter_value'],
								'high' => $column['filter_value'],
							);
						}
						break;

					case 'date':
					case 'time':
					case 'datetime':
						if (is_string($column['filter_value'])) {
							$column['filter_value'] = array(
								'start' => $column['filter_value'],
								'end'   => $column['filter_value'],
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
