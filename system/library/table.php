<?php
class Table extends Library
{
	private $file;
	private $template_data = array();

	private $columns;
	private $rows;

	private $path;

	public function init()
	{
		$this->path = '';
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

	public function setTemplate($file)
	{
		if (!preg_match("/\\.tpl$/", $file)) {
			$file .= '.tpl';
		}

		if (file_exists(DIR_THEME . $this->path . $file)) {
			$this->file = DIR_THEME . $this->path . $file;
		} elseif (file_exists(DIR_THEMES . 'default/template/' . $file)) {
			$this->file = DIR_THEMES . 'default/template/' . $file;
		} else {
			trigger_error(_l("%s(): Could not load form template %s!", __METHOD__, DIR_THEME . $this->path . $file));
			exit();
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

		//render the file
		ob_start();

		require_once(_ac_mod_file($this->file));

		return ob_get_clean();
	}

	private function prepare()
	{
		if (!$this->file || !file_exists($this->file)) {
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
			$this->template_data['filter_url'] =  site_url($this->template_data['listing_path'], $this->url->getQueryExclude('filter'));
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
			);

			foreach ($default_values as $key => $default) {
				if (!isset($column[$key])) {
					$column[$key] = $default;
				}
			}

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
			}

			switch ($column['type']) {
				case 'text':
					break;
				case 'multi':
					break;
				case 'image':
					if (!isset($column["sort_value"])) {
						$column['sort_value'] = "__image_sort__" . $slug;
					}
					break;
				case 'select':
					if (empty($column['build_data'])) {
						trigger_error(_l("You must specify build_data for the column %s of type select!", $slug));
						exit();
					}

					if (!isset($column['build_config'])) {
						if (is_array(current($column['build_data']))) {
							trigger_error(_l("You must specify build_config for the column %s of type select with this nature of build_data!", $slug));
							exit();
						}
					}

					if (!is_array(current($column['build_data']))) {
						//normalize the data for easier processing
						foreach ($column['build_data'] as $key => $bd_item) {
							$column['build_data'][$key] = array(
								'key'  => $key,
								'name' => $bd_item
							);
						}
						$column['build_config'] = array(
							'key',
							'name'
						);
					} elseif ($column['build_config'][0] === false) {
						//normalize the data for easier processing
						foreach ($column['build_data'] as $key => $bd_item) {
							$column['build_data'][$key] = array(
								'key'  => $key,
								'name' => $bd_item[$column['build_config'][1]],
							);
						}
						$column['build_config'] = array(
							'key',
							'name'
						);
					}

					break;
				default:
					break;
			}

			if (!isset($column["sort_value"])) {
				$column["sort_value"] = $slug;
			}

		}
	}
}
