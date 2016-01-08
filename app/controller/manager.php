<?php

/**
 * @author  Daniel Newman
 * @date    1/8/2016
 * @package Amplo MVC
 * @link    http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */
class App_Controller_Manager extends App_Controller_Table
{
	protected $model = array(
		'class' => 'App_Model_Address',
		'path'  => 'manager',
		'label' => 'address',
		'value' => 'address_id',
		'title' => 'Address',
	);

	public function index($options = array())
	{
		//Page Head
		if (!$this->is_ajax) {
			set_page_info('title', _l("My Addresses"));
		}

		$options += array(
			'template' => !empty($_REQUEST['template']) ? $_REQUEST['template'] : 'manager/record',
		) + $this->model;

		output($this->render($options['template'], $options));
	}
}
