<?php

/**
 * Class App_Controller_Block_Widget_Faq
 * Name: Frequently Asked Questions
 */
class App_Controller_Block_Widget_Faq extends App_Controller_Block_Block
{
	public function build($settings)
	{
		$this->render('block/widget/faq', $settings);
	}

	public function settings(&$block)
	{
		$block['settings']['faqs']['__ac_template__'] = array(
			'title' => '',
			'questions'   => array(
				'__ac_template__' => array(
					'question' => '',
					'answer'   => '',
				),
			),
		);

		return $this->render('block/widget/faq/settings', $block);
	}
}
