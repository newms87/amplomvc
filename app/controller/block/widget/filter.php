<?php

/**
 * Class App_Controller_Block_Widget_Filter
 * Name: List Filter
 */
class App_Controller_Block_Widget_Filter extends App_Controller_Block_Block
{
	public function build($settings)
	{
		//Default Settings
		$settings += array(
			'replace'  => false,
			'fields'   => array(),
			'values'   => (array)_request('filter'),
			'path'     => $this->route->getPath(),
			'query'    => $_GET,
			'template' => 'block/widget/filter',
		);

		if (empty($settings['fields'])) {
			return;
		}

		//Remove filter
		unset($settings['query']['filter']);

		foreach ($settings['fields'] as $key => &$field) {
			if (empty($field['filter'])) {
				unset($settings['fields'][$key]);
				continue;
			}

			if (!isset($field['name'])) {
				$field['name'] = $key;
			}

			if (!isset($field['id'])) {
				$field['id'] = uniqid('filter-' . $key . '-');
			}

			$type = is_string($field['filter']) ? $field['filter'] : 'text';

			switch ($type) {
				case 'pk':
				case 'pk-int':
					$type = 'number';
					break;

				case 'int':
				case 'float':
				case 'decimal':
					$type = 'range';
					break;

				case 'multiselect':
				case 'select':
				case 'textarea':
				case 'text';
					break;

				default:
					$type = 'text';
					break;
			}

			$field['type'] = $type;

			if (!isset($field['label'])) {
				$field['label'] = cast_title($field['name']);
			}

			if (!isset($field['placeholder'])) {
				switch ($field['type']) {
					case 'range':
					case 'date':
					case 'datetime':
					case 'time':
						$field['placeholder'] = array(
							'from' => _l("From"),
							'to'   => _l("To"),
						);
						break;

					default:
						$field['placeholder'] = $field['label'];
						break;
				}
			}

			if (isset($settings['values'][$field['name']])) {
				$field['value'] = $settings['values'][$field['name']];
			} elseif (!isset($field['value'])) {
				$field['value'] = '';
			}
		}
		unset($field);

		//Base URL
		$settings['url'] = site_url($settings['path'], $settings['query']);

		//Render
		$this->render($settings['template'], $settings);
	}
}
