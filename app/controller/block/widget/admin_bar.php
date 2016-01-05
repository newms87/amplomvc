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
 * Class App_Controller_Block_Widget_AdminBar
 * Name: Admin Bar for Front End
 */
class App_Controller_Block_Widget_AdminBar extends App_Controller_Block_Block
{
	public function build($settings)
	{
		if (_cookie('disable_admin_bar')) {
			return;
		}

		$data = $settings;

		$data += array(
			'admin_bar'  => option('admin_bar'),
			'admin_link' => site_url('admin'),
			'clock_time' => $this->date->now('datetime_long'),
		);

		$time_inc            = 3600 * 24;
		$data['sim_forward'] = $this->url->here('sim_time=' . $time_inc);
		$data['sim_back']    = $this->url->here('sim_time=-' . $time_inc);
		$data['sim_reset']   = $this->url->here('sim_time=reset');

		//Render
		$this->render('block/widget/admin_bar', $data);
	}
}
