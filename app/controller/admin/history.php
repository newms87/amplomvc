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
class App_Controller_Admin_History extends App_Controller_Table
{
	protected $model = array(
		'class'         => 'App_Model_History',
		'path'          => 'admin/history',
		'label'         => 'message',
		'value'         => 'history_id',
		'title'         => 'History',
		'listing_group' => 'DB History',
		'save_path'     => false,
		'form_path'     => false,
	);

	public function index($options = array())
	{
		$options['batch_action'] = array();

		return parent::index($options);
	}

	public function listing($options = array())
	{
		$options += array(
			'sort_default' => array('history_id' => 'DESC'),
			'actions'      => false,
		);

		return parent::listing($options);
	}

	public function save($options = array())
	{
		trigger_error(_l("Attempt to modify the history table! This is not a valid action."));
	}

	public function remove($options = array())
	{
		trigger_error(_l("Attempt to remove an entry from the history table! This is not a valid action."));
	}

	public function batch_action($options = array())
	{
		trigger_error(_l("Attempt to apply a batch action on the history table! This is not a valid action."));
	}
}
