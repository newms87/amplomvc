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
			'title'     => '',
			'questions' => array(
				'__ac_template__' => array(
					'question' => '',
					'answer'   => '',
				),
			),
		);

		return $this->render('block/widget/faq/settings', $block);
	}
}
