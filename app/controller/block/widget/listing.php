<?php
/**
 * @author  Daniel Newman
 * @date    3/20/2013
 * @package Amplo MVC
 * @link    http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

/**
 * Class App_Controller_Block_Widget_Listing
 * Name: The Listings for Admin
 */
class App_Controller_Block_Widget_Listing extends App_Controller_Block_Block
{
	public function build($settings)
	{
		$settings += array(
			'columns'             => array(),
			'extra_cols'          => array(),
			'template'            => 'table/list_view',
			'ajax'                => 1,
			'records'             => array(),
			'total'               => 0,
			'template_data'       => array(),
			'sort'                => array(),
			'filter_value'        => array(),
			'pagination_settings' => array(),
			'limit_settings'      => array(),
			'show_pagination'     => true,
			'show_limits'         => 'top',
			'show_controls'       => true,
			'show_charts'         => true,
			'show_actions'        => true,
			'show_messages'       => null,
			'listing_path'        => $this->router->getPath(),
			'save_path'           => false,
			'index'               => '',
			'view_id'             => _request('view_id'),
			'chart'               => array(),
			//TODO: Move theme to global: With the default AmploMVC installation, only admin theme has a template for listing
			'theme'               => 'admin',
			'filter_style'        => 'switch',
			'export'              => isset($_GET['export']),
		);

		if ($settings['export']) {
			return $this->export($settings);
		}

		if (!empty($settings['records'])) {
			$settings['show_limits'] = $settings['show_limits'] === 'bottom' ? 'bottom' : 'top';
		} else {
			$settings['show_limits'] = false;
		}

		$settings['template_data'] += array(
			'listing_path' => $settings['listing_path'],
			'save_path'    => $settings['save_path'],
			'index'        => $settings['index'],
		);

		if (!isset($settings['show_messages'])) {
			$settings['show_messages'] = $settings['ajax'] && $this->is_ajax;
		}

		if ($settings['view_id']) {
			$settings += $this->Model_View->getRecord($settings['view_id']);

			$settings = $this->Model_View->getViewSettings($settings['view_id']) + $settings;
		}

		//Default Settings
		$settings += array(
			'view_type' => '',
		);

		//Normalize Extra Cols
		foreach ($settings['extra_cols'] as $key => &$ec) {
			if (!isset($ec['Field'])) {
				$ec['Field'] = $key;
			}

			if (!isset($ec['label'])) {
				$ec['label'] = $ec['Field'];
			}
		}
		unset($ec);

		$this->table->init();
		$this->table->setTemplate('table/list_view', isset($settings['theme']) ? $settings['theme'] : null);
		$this->table->setColumns($settings['columns']);
		$this->table->setRows($settings['records']);

		$filter_values = array();
		$filter_types  = array();

		foreach ($settings['filter_value'] as $key => $fv) {
			if (strpos($key, '!') === 0) {
				$key  = substr($key, 1);
				$type = 'not';
			} else {
				$type = 'equals';
			}

			//Find Overriding filter key
			if (!isset($settings['columns'][$key])) {
				foreach ($settings['columns'] as $fkey => $c) {
					if (isset($c['filter_key']) && $c['filter_key'] === $key) {
						$key = $fkey;
						break;
					}
				}
			}

			$filter_values[$key] = $fv;
			$filter_types[$key]  = $type;
		}

		$this->table->mapAttribute('filter_value', $filter_values);
		$this->table->mapAttribute('filter_type', $filter_types);

		$table_settings = $settings['template_data'] + array(
				'show_actions' => $settings['show_actions'],
				'filter_style' => $settings['filter_style'],
				'sort'         => $settings['sort'],
			);

		$settings['listing'] = $this->table->render($table_settings);

		//Limits
		if ($settings['show_limits']) {
			$settings['limit_settings'] += array(
				'path'  => $settings['listing_path'],
				'limit' => isset($settings['limit']) ? $settings['limit'] : null,
				'page'  => isset($settings['page']) ? $settings['page'] : null,
				'total' => $settings['total'],
			);
		}

		//Pagination
		if ($settings['show_pagination']) {
			$settings['pagination_settings'] += array(
				'total' => $settings['total'],
				'path'  => $settings['listing_path'],
			);
		}

		//Sort Columns
		if (!empty($settings['extra_cols'])) {
			uksort($settings['extra_cols'], function ($a, $b) use ($settings) {
				foreach ($settings['columns'] as $field => $col) {
					if ($field === $a) {
						return -1;
					} elseif ($field === $b) {
						return 1;
					}
				}

				return strtolower($settings['extra_cols'][$a]['label']) > strtolower($settings['extra_cols'][$b]['label']);
			});
		}

		//Template Data
		if ($settings['show_charts']) {
			$settings['data_chart_types'] = array(
				''     => _l("Listing"),
				'Line' => _l('Line Chart'),
				'Bar'  => _l('Bar Chart'),
				'Pie'  => _l('Pie Chart'),
			);
		}

		if ($group_by = !empty($settings['chart']['group_by']) ? $settings['chart']['group_by'] : false) {
			$settings['chart']['build'] = !empty($settings['columns'][$group_by]['build']) ? $settings['columns'][$group_by]['build'] : array();
		}

		//Render
		$this->render('block/widget/listing', $settings, $settings['theme']);
	}

	public function export($settings)
	{
		$columns = array();

		foreach ($settings['columns'] as $col => $col_info) {
			if (is_string($col_info)) {
				$columns[$col] = $col_info;
			} elseif (!empty($col_info['label'])) {
				$columns[$col] = $col_info['label'];
			} elseif (isset($col_info['Field'])) {
				$columns[$col] = $col_info['Field'];
			} else {
				$columns[$col] = $col;
			}

			if (is_string($col_info) || empty($col_info['html_export'])) {
				foreach ($settings['records'] as &$r) {
					if (isset($r[$col])) {
						if ($build = isset($settings['columns'][$col]['build']['data']) ? $settings['columns'][$col]['build'] : false) {
							$v = value2label($r[$col], $build['data'], isset($build['label']) ? $build['label'] : null, isset($build['value']) ? $build['value'] : null);

							if ($v !== null) {
								$r[$col] = $v;
							}
						}

						if (is_array($r[$col])) {
							$r[$col] = json_encode($r[$col]);
						} else {
							$r[$col] = strip_tags($r[$col]);
						}
					}
				}
				unset($r);
			}
		}

		$filename = !empty($settings['listing_path']) ? slug($settings['listing_path']) : 'listing';

		$this->csv->generateCsv($columns, $settings['records']);
		$this->csv->download($filename . '.csv', 'csv');
	}

	public function save_settings()
	{
		$view_id = _request('view_id');

		if ($view_id) {
			$view = array(
				'view_type' => _post('view_type', ''),
			);

			if ($this->Model_View->save($view_id, $view)) {
				$settings = $this->Model_View->getViewSettings($view_id);

				if (!is_array($settings)) {
					$settings = array();
				}

				$settings['chart'] = _post('chart', array());

				message('notify', print_r($settings['chart'], true));
				$this->Model_View->saveViewSettings($view_id, $settings);
			}

			if ($this->Model_View->hasError()) {
				message('error', $this->Model_View->fetchError());
			} else {
				message('success', _l("The Settings have been saved."));
			}
		}

		output_message();
	}
}
