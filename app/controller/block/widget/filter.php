<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

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
			'options'  => array(),
			'fields'   => array(),
			'values'   => (array)_request('filter'),
			'path'     => $this->router->getPath(),
			'query'    => $_GET,
			'template' => 'block/widget/filter',
		);

		if (empty($settings['fields'])) {
			return;
		}

		//Remove filter
		unset($settings['query']['filter']);

		foreach ($settings['fields'] as $key => &$field) {
			if (empty($field['filter']) || (!empty($field['build']) && empty($field['build']['data']))) {
				unset($settings['fields'][$key]);
				continue;
			}

			if (!isset($field['name'])) {
				$field['name'] = $key;
			}

			if (!isset($field['id'])) {
				$field['id'] = uniqid('filter-' . $key . '-');
			}

			if (isset($settings['values'][$field['name']])) {
				$field['value'] = $settings['values'][$field['name']];
			} elseif (!isset($field['value'])) {
				$field['value'] = null;
			}

			$field['enabled'] = isset($field['value']);

			$type = is_string($field['filter']) ? $field['filter'] : 'text';

			switch ($type) {
				case 'pk':
				case 'pk-int':
					$type = 'number';
					break;

				case 'int':
				case 'float':
				case 'decimal':
				case 'range':
					$type = 'range';

				case 'date':
				case 'datetime':
				case 'time':
					if (!is_array($field['value'])) {
						$field['value'] = array(
							'gte' => '',
							'lte' => '',
						);
					}
					break;


				case 'multiselect':
				case 'select':
				case 'textarea':
				case 'text':
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
		}
		unset($field);

		//Base URL
		$settings['url'] = site_url($settings['path'], $settings['query']);

		//Render
		$this->render($settings['template'], $settings);
	}
}
