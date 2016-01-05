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

class App_Model_History extends App_Model_Table
{
	protected $table = 'history', $primary_key = 'history_id';

	public function getColumns($filter = array(), $merge = array())
	{
		$merge += array(
			'data'    => array(
				'align' => 'left',
			),
			'message' => array(
				'align' => 'left',
			),
		);

		return parent::getColumns($filter, $merge);
	}
}
