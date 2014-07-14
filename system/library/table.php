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
		$this->file = is_file($file) ? $file : $this->theme->getFile($file, $theme);

		if (!$this->file) {
			echo $file  . ' is no a file<Br>';
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

	public function render()
	{
		$this->prepare();

		extract($this->template_data);

		$columns = $this->columns;
		$rows    = $this->rows;
		if (empty($row_id)) {
			$row_id = '';
		}

		//render the file
		ob_start();

		include(_ac_mod_file($this->file));

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
				'attrs'        => array(),
				'filter'       => false,
				'type'         => 'text',
				'align'        => 'center',
				'sortable'     => false,
				'editable'     => null,
			);

			$column += $default_values;

			//additional / overridden attributes
			foreach ($column as $attr => $value) {
				if (strpos($attr, '#') === 0) {
					$column['attrs'][substr($attr, 1)] = $value;
				}
			}

			$column['html_attrs'] = '';

			foreach ($column['attrs'] as $attr => $value) {
				$column['html_attrs'] .= $attr . '="' . $value . '" ';
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
			}

			//If Field is set, assume this came from Table Model, and therefore can be edited
			if (is_null($column['editable'])) {
				$column['editable'] = isset($column['Field']);
			}

			if ($column['editable'] && !is_string($column['editable'])) {
				$column['editable'] = $column['type'];
			}

			if (!isset($column['editable_data'])) {
				$column['editable_data'] = !empty($column['build_data']) ? $column['build_data'] : array();
			}

			if (!isset($column["sort_value"])) {
				$column["sort_value"] = $slug;
			}

			switch ($column['type']) {
				case 'text':
					if (isset($column['Length']) && ($column['Length'] == 0 || $column['Length'] > 100)) {
						$column['type'] = 'longtext';
					}
					break;
				case 'image':
					if (!isset($column["sort_value"])) {
						$column['sort_value'] = "__image_sort__" . $slug;
					}
					break;
				case 'multiselect':
					break;
				case 'select':
					if (empty($column['build_data'])) {
						trigger_error(_l("You must specify build_data for the column %s of type select!", $slug));
						exit();
					}

					if (!isset($column['build_config'])) {
						$column['build_config'] = array(
							'key',
							'name'
						);
					}

					$build_key   = $column['build_config'][0];
					$build_value = $column['build_config'][1];
					$build_data  = array();

					foreach ($column['build_data'] as $key => $bd_item) {
						if (is_array($bd_item)) {
							//Validate Data keys and values are set
							if (($build_key !== false && !isset($bd_item[$build_key])) || !isset($bd_item[$build_value])) {
								trigger_error(_l("You must set the build config to match a key and value in the build data for all values!", $slug));
								exit();
							}

							$build_data[$key] = array(
								'key'  => $build_key === false ? $key : $bd_item[$build_key],
								'name' => $bd_item[$build_value],
							);
						} else {
							$build_data[$key] = array(
								'key'  => $key,
								'name' => $bd_item,
							);
						}
					}

					$column['build_data']   = $build_data;
					$column['build_config'] = array(
						'key',
						'name'
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
				}
			}
		}
	}
}
