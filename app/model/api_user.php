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

class App_Model_ApiUser extends App_Model_Table
{
	protected $table = 'api_user', $primary_key = 'api_user_id';

	public function save($api_user_id, $api_user, &$private_key = null)
	{
		if (isset($api_user['user_id'])) {
			if (!$api_user['user_id']) {
				$this->error['user_id'] = _l("User ID must not be empty.");
			}
		} elseif (!$api_user_id) {
			$this->error['user_id'] = _l("Please provide a User ID.");
		}

		if (isset($api_user['username'])) {
			if (!validate('text', $api_user['username'], 3, 128)) {
				$this->error['username'] = _l("Username must be between 3 and 128 characters!");
			}
		} elseif (!$api_user_id) {
			$this->error['username'] = _l("Please provide a Username!");
		}

		if ($this->error) {
			return false;
		}

		if (!$api_user_id) {
			$api_user['date_added'] = $this->date->now();

			if (!$this->api->generateKeys($private_key, $public_key)) {
				$this->error['key_pair'] = $this->api->fetchError();

				return false;
			}

			$api_user['api_key']     = $this->api->generateApiKey();
			$api_user['public_key']  = $public_key;
			$api_user['private_key'] = $private_key;
		}

		return parent::save($api_user_id, $api_user);
	}

	public function getRecords($sort = array(), $filter = array(), $options = array(), $total = false)
	{
		//Where
		if (isset($filter['user_role'])) {
			$options['join']['user_role'] = array(
				'type'  => 'LEFT JOIN',
				'alias' => 'ur',
				'on'    => 'user_role_id',
			);

			if (is_string($filter['user_role'])) {
				$filter['#user_role'] = "ur.`name` like '%" . $this->escape($filter['user_role']) . "%'";
			} else {
				$filter['#user_role'] = "ur.`name` IN ('" . implode("','", $this->escape($filter['user_role'])) . "')";
			}
		}

		return parent::getRecords($sort, $filter, $options, $total);
	}

	public function getColumns($filter = array(), $merge = array())
	{
		$merge += array(
			'user_id'      => array(
				'type'   => 'select',
				'label'  => _l("User Account"),
				'build'  => array(
					'data'  => $this->Model_User->getRecords(null, null, array('cache' => true)),
					'label' => 'username',
					'value' => 'user_id',
				),
				'filter' => 'select',
				'sort'   => true,
			),
			'user_role_id' => array(
				'type'   => 'select',
				'label'  => _l("Role"),
				'build'  => array(
					'data'  => $this->Model_UserRole->getRecords(null, array('type' => App_Model_UserRole::TYPE_API), array('cache' => true)),
					'label' => 'name',
					'value' => 'user_role_id',
				),
				'filter' => 'select',
				'sort'   => true,
			),
			'status'       => array(
				'type'   => 'select',
				'label'  => _l("Status"),
				'build'  => array(
					'data' => array(
						0 => _l("Deactivated"),
						1 => _l("Active"),
					),
				),
				'filter' => true,
				'sort'   => true,
			),
		);

		$columns = parent::getColumns($filter, $merge);

		//Never allow private_key as a column.
		unset($columns['private_key']);

		return $columns;
	}
}
