<?php

/**
 * Class App_Controller_Block_Widget_Listing
 * Name: The Listings for Admin
 */
class App_Controller_Block_Widget_Listing extends App_Controller_Block_Block
{
	public function build($settings)
	{
		$defaults = array(
			'extra_cols'      => array(),
			'template'        => 'table/list_view',
			'ajax'            => 1,
			'rows'            => array(),
			'template_data'   => array(),
			'filter_value'    => array(),
			'show_pagination' => true,
			'show_limits'     => true,
			'limits'          => null,
			'show_messages'   => null,
			'listing_path'    => $this->route->getPath(),
			'save_path'       => false,
			'row_id'          => '',
			'view_id'         => _request('view_id'),
			'chart'           => array(),
		);

		$settings += $defaults;

		$template_defaults = array(
			'listing_path' => $settings['listing_path'],
			'save_path'    => $settings['save_path'],
			'row_id'       => $settings['row_id'],
		);

		$settings['template_data'] += $template_defaults;

		if (!isset($settings['show_messages'])) {
			$settings['show_messages'] = $settings['ajax'] && IS_AJAX;
		}

		if ($settings['view_id']) {
			$settings = $this->Model_View->getViewSettings($settings['view_id']) + $settings;

			$settings['chart'] = array(
				'type'      => 'Bar',
				'group_by'  => 'Scope',
				'data_cols' => 'Total',
			);
		}

		//Normalize Extra Cols
		foreach ($settings['extra_cols'] as $key => &$ec) {
			if (!isset($ec['Field'])) {
				$ec['Field'] = $key;
			}

			if (!isset($ec['display_name'])) {
				$ec['display_name'] = $ec['Field'];
			}
		}
		unset($ec);

		$this->table->init();
		$this->table->setTemplate('table/list_view', isset($settings['theme']) ? $settings['theme'] : null);
		$this->table->setColumns($settings['columns']);
		$this->table->setRows($settings['rows']);
		$this->table->setTemplateData($settings['template_data']);

		$filter_values = array();
		$filter_types  = array();

		foreach ($settings['filter_value'] as $key => $fv) {
			if (strpos($key, '!') === 0) {
				$key  = substr($key, 1);
				$type = 'not';
			} else {
				$type = 'equals';
			}

			$filter_values[$key] = $fv;
			$filter_types[$key]  = $type;
		}

		$this->table->mapAttribute('filter_value', $filter_values);
		$this->table->mapAttribute('filter_type', $filter_types);

		$settings['listing'] = $this->table->render();

		//Limits
		if ($settings['show_limits']) {
			$settings['limit_settings'] = array(
				'path' => $settings['listing_path'],
			);

			if ($settings['limits']) {
				$settings['limit_settings']['limits'] = $settings['limits'];
			}
		}

		//Pagination
		if ($settings['show_pagination']) {
			$settings['pagination_settings'] = array(
				'total' => $settings['total_listings'],
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

				return strtolower($settings['extra_cols'][$a]['display_name']) > strtolower($settings['extra_cols'][$b]['display_name']);
			});
		}

		//Template Data
		$settings['data_chart_types'] = array(
			'Line' => _l('Line Chart'),
			'Bar'  => _l('Bar Chart'),
			'Pie'  => _l('Pie Chart'),
		);

		//Action
		$settings['refresh'] = site_url($settings['listing_path'], $_GET);

		//Render
		$this->render('block/widget/listing', $settings);
	}
}
