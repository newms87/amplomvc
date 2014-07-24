<?php

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

	public function convert($data, $settings = array())
	{
		$settings += array(
			'group_by'  => null,
			'data_cols' => null,
		);

		if (!$settings['group_by'] || !$settings['data_cols']) {
			return array();
		}

		if (!is_array($settings['group_by'])) {
			$data_index = $settings['group_by'];

			$settings['group_by'] = array();

			foreach ($data as $entry) {
				$settings['group_by'][] = $entry[$data_index];
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
				foreach ($settings['data_cols'] as $col => $col_name) {
					if (!isset($datasets[$col])) {
						$datasets[$col] = array(
								'label' => $col_name,
								'data'  => array(),
							) + self::$colors[($color_index++ % $max_color_index)];
					}

					$datasets[$col]['data'][] = isset($entry[$col_name]) ? $entry[$col_name] : $entry[$col];
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

		return array(
			'labels'   => $settings['group_by'],
			'datasets' => $datasets,
		);

	}
}