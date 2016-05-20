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
class App_Model_Log extends App_Model_Table
{
	protected $table = 'log', $primary_key = 'log_id';

	public function getRecords($sort = array(), $filter = array(), $options = array(), $total = false)
	{
		$aliases = array(
			'user' => 'user_id',
		);

		$this->mapAliasToKey($aliases, $sort, $filter, $options);

		$options['sql_calc_found_rows'] = true;

		return parent::getRecords($sort, $filter, $options, $total);
	}

	public function getLogs()
	{
		return $this->queryColumn("SELECT DISTINCT name FROM {$this->t['log']}", null, true);
	}

	public function getColumns($filter = array(), $merge = array())
	{
		$merge += array(
			'message' => array(
				'align' => 'left',
			),
			'user'    => array(
				'type'         => 'select',
				'display_name' => _l("User"),
				'build'        => array(
					'name'  => 'user_id',
					'data'  => array(),
					'label' => 'username',
					'value' => 'user_id',
				),
				'filter'       => 'multiselect',
				'sort'         => true,
				'editable'     => false,
			),
		);

		$columns = parent::getColumns($filter, $merge);

		//Initialize User data only if necessary
		if (isset($columns['user'])) {
			$columns['user']['build']['data'] = $this->Model_User->getRecords(array('username' => 'ASC'), null, array('cache' => true));
		}

		return $columns;
	}

	public function remove($log_id)
	{
		$this->delete('log', $log_id);
	}

	public function clear($name = '')
	{
		if ($name) {
			$this->delete('log', array('name' => $name));
		} else {
			$this->delete('log');
		}
	}
}
