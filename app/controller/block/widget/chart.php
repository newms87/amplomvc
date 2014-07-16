<?php

/**
 * Class App_Controller_Block_Widget_Listing
 * Name: The Listings for Admin
 */
class App_Controller_Block_Widget_Chart extends App_Controller_Block_Block
{
	public function build($settings = array())
	{
		$defaults = array(
			'type'     => 'line',
			'labels'   => 'months',
			'datasets' => array(),
			'options'  => array(),
			'colors'   => array(
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
				),
			),
		);

		$settings += $defaults;

		$settings['labels']   = array(
			'January',
			'February',
			'March',
			'April'
		);
		$settings['datasets'] = array(
			array(
				'label' => 'first',
				'data'  => array(
					45,
					54,
					65,
					23,
					49,
					69,
					80
				),
			),
			array(
				'label' => 'second',
				'data'  => array(
					33,
					43,
					55,
					46,
					69,
					72,
					40
				),
			),

			array(
				'label' => 'second',
				'data'  => array(
					22,
					23,
					55,
					36,
					39,
					32,
					20
				),
			),

			array(
				'label' => 'second',
				'data'  => array(
					83,
					83,
					45,
					26,
					19,
					82,
					80
				),
			),

			array(
				'label' => 'second',
				'data'  => array(
					53,
					63,
					95,
					106,
					29,
					32,
					50
				),
			),

			array(
				'label' => 'second',
				'data'  => array(
					26,
					46,
					65,
					43,
					71,
					71,
					01,
				),
			),

			array(
				'label' => 'second',
				'data'  => array(
					100,
					29,
					33,
					65,
					54,
					45,
					23,
				),
			),
		);

		$max_index = count($settings['colors']);
		foreach ($settings['datasets'] as $index => &$dataset) {
			$index = $index % $max_index;
			$dataset += isset($settings['colors'][$index]) ? $settings['colors'][$index] : $settings['colors'][0];
		}
		unset($dataset);

		$this->theme->addTheme('admin');

		//Render
		output($this->render('block/widget/chart', $settings));
	}
}
