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
class App_Model_History extends App_Model_Table
{
	protected $table = 'history', $primary_key = 'history_id';

	public function restore($history_ids)
	{
		$updates = array();

		$records = $this->getRecords(array('history_id' => 'DESC'), array('history_id' => $history_ids));

		foreach ($records as $record) {
			if (empty($updates[$record['table']][$record['record_id']])) {
				$updates[$record['table']][$record['record_id']] = array();
			}

			$updates[$record['table']][$record['record_id']] += json_decode($record['data'], true);
		}

		foreach ($updates as $table => $table_records) {
			foreach ($table_records as $record_id => $update) {
				$this->update($table, $update, $record_id);
			}
		}

		return true;
	}

	public function getColumns($filter = array(), $merge = array())
	{
		$user_columns = array(
			'user_id' => 1,
			'#user'   => "CONCAT (username, ' (', user_id, ')') as user"
		);

		$merge += array(
			'data'    => array(
				'align' => 'left',
			),
			'message' => array(
				'align' => 'left',
			),
			'user_id' => array(
				'type'   => 'select',
				'build'  => array(
					'data'  => $this->Model_User->getRecords(null, null, array(
						'cache'   => 1,
						'columns' => $user_columns
					)),
					'label' => 'user',
					'value' => 'user_id',
				),
				'filter' => 'multiselect',
			),
		);

		return parent::getColumns($filter, $merge);
	}
}
