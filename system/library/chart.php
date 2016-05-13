<?php

/**
 * @author  Daniel Newman
 * @date    3/20/2013
 * @package Amplo MVC
 * @link    http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */
class Chart extends Library
{
	static $colors = array(
		0 => array(
			'fillColor'            => 'rgba(255,0,42,.5)',
			'strokeColor'          => 'rgba(255,0,42,1)',
			'pointColor'           => 'rgba(255,0,42,1)',
			'pointStrokeColor'     => '#fff',
			'pointHighlightFill'   => '#fff',
			'pointHighlightStroke' => 'rgba(255,0,42,1)',
		),
		1 => array(
			'fillColor'            => 'rgba(71,129,243,.5)',
			'strokeColor'          => 'rgba(71,129,243,1)',
			'pointColor'           => 'rgba(71,129,243,1)',
			'pointStrokeColor'     => '#fff',
			'pointHighlightFill'   => '#fff',
			'pointHighlightStroke' => 'rgba(71,129,243,1)',
		),
		2 => array(
			'fillColor'            => 'rgba(107,191,62,.5)',
			'strokeColor'          => 'rgba(107,191,62,1)',
			'pointColor'           => 'rgba(107,191,62,1)',
			'pointStrokeColor'     => '#fff',
			'pointHighlightFill'   => '#fff',
			'pointHighlightStroke' => 'rgba(107,191,62,1)',
		),
		3 => array(
			'fillColor'            => 'rgba(229,152,36,.5)',
			'strokeColor'          => 'rgba(229,152,36,1)',
			'pointColor'           => 'rgba(229,152,36,1)',
			'pointStrokeColor'     => '#fff',
			'pointHighlightFill'   => '#fff',
			'pointHighlightStroke' => 'rgba(229,152,36,1)',
		),
		4 => array(
			'fillColor'            => 'rgba(159,97,233,.5)',
			'strokeColor'          => 'rgba(159,97,233,1)',
			'pointColor'           => 'rgba(159,97,233,1)',
			'pointStrokeColor'     => '#fff',
			'pointHighlightFill'   => '#fff',
			'pointHighlightStroke' => 'rgba(159,97,233,1)',
		)
	);

	static $discrete_colors = array(
		0 => array(
			'color'     => '#81E581',
			'highlight' => "#BEF3BE",
		),
		1 => array(
			'color'     => '#D34E4E',
			'highlight' => "#FFAFAF",
		),
		2 => array(
			'color'     => '#4E69D3',
			'highlight' => "#AABAF7",
		),
		3 => array(
			'color'     => '#D96E21',
			'highlight' => "#F1BD97",
		),
		4 => array(
			'color'     => '#773EB3',
			'highlight' => "#C2A5E1",
		),
		5 => array(
			'color'     => '#D3C414',
			'highlight' => "#E7E2A3",
		),
		6 => array(
			'color'     => '#933C3C',
			'highlight' => "#DB9E9E",
		),
		7 => array(
			'color'     => '#298329',
			'highlight' => "#7CBD7C",
		),
		8 => array(
			'color'     => '#182572',
			'highlight' => "#707AB3",
		),
		9 => array(
			'color'     => '#DF1EDF',
			'highlight' => "#E995E9",
		),
	);

	public function convert($data, $settings = array())
	{
		$settings += array(
			'group_by'  => null,
			'data_cols' => null,
			'build'     => null,
		);

		if (!$settings['group_by'] || !$settings['data_cols']) {
			return array();
		}

		if (!is_array($settings['group_by'])) {
			$data_index = $settings['group_by'];

			$settings['group_by'] = array();

			foreach ($data as $entry) {
				$settings['group_by'][] = !empty($entry[$data_index]) ? ($settings['build'] ? get_build_value($settings['build'], $entry[$data_index]) : $entry[$data_index]) : '';
			}
		}

		if (!is_array($settings['data_cols'])) {
			$settings['data_cols'] = array($settings['data_cols']);
		}

		$datasets = array();

		$color_index     = 0;
		$max_color_index = count(self::$colors);

		if ($data_index) {
			foreach ($data as $entry) {
				//So isset is not false if NULL
				foreach ($entry as &$e) {
					if ($e === null) {
						$e = 0;
					}
				}
				unset($e);

				foreach ($settings['data_cols'] as $col => $col_name) {
					if (!isset($datasets[$col])) {
						$datasets[$col] = array(
								'label' => $col_name,
								'data'  => array(),
							) + self::$colors[($color_index++ % $max_color_index)];
					}

					$datasets[$col]['data'][] = isset($entry[$col_name]) ? $entry[$col_name] : (isset($entry[$col]) ? $entry[$col] : '');
				}
			}
		} else {
			foreach ($data as $entry) {
				foreach ($settings['data_cols'] as $col => $col_name) {
					if (!isset($datasets[$col])) {
						$datasets[$col] = array(
								'label' => $col_name,
								'data'  => array(),
							) + self::$colors[($color_index++ % $max_color_index)];
					}

					$datasets[$col]['data'][] = $entry[$col];
				}
			}
		}

		//Convert to Discrete data (for Pie charts, etc..)
		$discrete        = array();
		$label           = $data_index ? $data_index : $settings['group_by'][0];
		$value           = $settings['data_cols'][0];
		$color_index     = 0;
		$max_color_index = count(self::$discrete_colors);

		foreach ($data as $entry) {
			$discrete[] = array(
					'value' => isset($entry[$value]) ? (int)$entry[$value] : '',
					'label' => !empty($entry[$label]) ? ($settings['build'] ? get_build_value($settings['build'], $entry[$label]) : $entry[$label]) : '',
				) + self::$discrete_colors[($color_index++ % $max_color_index)];
		}

		return array(
			'labels'   => $settings['group_by'],
			'datasets' => $datasets,
			'discrete' => $discrete,
		);

	}
}
