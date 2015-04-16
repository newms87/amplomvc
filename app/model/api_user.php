<?php

class App_Model_ApiUser extends App_Model_Table
{
	protected $table = 'api_user', $primary_key = 'api_user_id';

	public function __construct()
	{
		parent::__construct();
		if (!defined('AMPLO_SECRET_KEY')) {
			define('AMPLO_SECRET_KEY', md5(time() . rand()));
		}
	}

	public function save($api_user_id, $api_user)
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

		if ($api_user_id) {

		} else {
			$api_user['date_added'] = $this->date->now();
			$api_user['api_key'] = hash('sha256', AMPLO_SECRET_KEY . time() . serialize($api_user) . rand());
		}

		return parent::save($api_user_id, $api_user);


		if (!empty($api_user['meta_exactly']) && !isset($api_user['meta'])) {
			$api_user['meta'] = array();
		}

		if (isset($api_user['meta'])) {
			$this->setMeta($api_user_id, $api_user['meta'], !empty($api_user['meta_exactly']));
		}

		return $api_user_id;
	}

	public function remove($api_user_id)
	{
		clear_cache('user');

		return $this->delete('user', $api_user_id);
	}

	public function addMeta($api_user_id, $key, $value, $multi = false)
	{
		$serialized = (int)_is_object($value);

		if ($serialized) {
			$value = serialize($value);
		}

		if (!$multi) {
			$this->deleteMeta($api_user_id, $key);
		}

		$data = array(
			'user_id'    => $api_user_id,
			'key'        => $key,
			'value'      => $value,
			'serialized' => $serialized,
		);

		$this->insert('user_meta', $data);
	}

	public function setMeta($api_user_id, $meta, $exactly = false)
	{
		if ($exactly) {
			$this->delete('user_meta', array('user_id' => $api_user_id));
		}

		foreach ($meta as $key => $value) {
			$serialized = (int)_is_object($value);

			if ($serialized) {
				$value = serialize($value);
			}

			if (!$exactly) {
				$this->deleteMeta($api_user_id, $key);
			}

			//Add new value
			$data = array(
				'user_id'    => $api_user_id,
				'key'        => $key,
				'value'      => $value,
				'serialized' => $serialized,
			);

			$this->insert('user_meta', $data);
		}

		return true;
	}

	public function deleteMeta($api_user_id, $key)
	{
		$where = array(
			'user_id' => $api_user_id,
			'key'     => $key,
		);

		return $this->delete('user_meta', $where);
	}

	public function getMeta($api_user_id, $key = null)
	{
		if ($key) {
			$value = $this->queryRow("SELECT `value`, serialized FROM {$this->t['user_meta']} WHERE user_id = " . (int)$api_user_id . " AND `key` = '" . $this->escape($key) . "' LIMIT 1");

			if ($value) {
				return $value['serialized'] ? unserialize($value['value']) : $value['value'];
			}

			return null;
		}

		$meta = $this->queryRows("SELECT * FROM {$this->t['user_meta']} WHERE user_id = " . (int)$api_user_id, 'key');

		foreach ($meta as &$m) {
			$m = $m['serialized'] ? unserialize($m['value']) : $m['value'];
		}
		unset($m);

		return $meta;
	}

	public function getUserByUsername($username)
	{
		return $this->queryRow("SELECT * FROM `{$this->t['user']}` WHERE username = '" . $this->escape($username) . "'");
	}

	public function getRecords($sort = array(), $filter = array(), $options = array(), $total = false)
	{
		//Where
		if (isset($filter['user_role'])) {
			$options['join']      = "LEFT JOIN {$this->t['user_role']} ur USING (user_role_id)";
			if (is_string($filter['user_role'])) {
				$filter['#user_role'] = "AND ur.`name` like '%" . $this->escape($filter['user_role']) . "%'";
			} else {
				$filter['#user_role'] = "AND ur.`name` IN ('" . implode("','", $this->escape($filter['user_role'])) . "')";
			}
		}

		if (isset($filter['name'])) {
			$filter['#name'] = "AND CONCAT(first_name, ' ', last_name) like '%" . $this->escape($filter['name']) . "%'";
		}

		//Order and Limit
		if (!empty($filter['sort']['name'])) {
			$ord = $filter['sort']['name'];
			unset($filter['sort']['name']);

			$filter['sort'] += array(
				'last_name'  => $ord,
				'first_name' => $ord,
			);
		}

		return parent::getRecords($sort, $filter, $options, $total);
	}

	public function getColumns($filter = array())
	{
		$columns = array(
			'user_role_id' => array(
				'type'         => 'select',
				'display_name' => _l("Role"),
				'build_data'   => $this->Model_UserRole->getRecords(null, null, array('cache' => true)),
				'build_config' => array(
					'user_role_id',
					'name'
				),
				'filter'       => 'select',
				'sort'         => true,
			),
			'status'       => array(
				'type'         => 'select',
				'display_name' => _l("Status"),
				'build_data'   => array(
					0 => _l("Disabled"),
					1 => _l("Enabled"),
				),
				'filter'       => true,
				'sort'         => true,
			),
		);

		$columns = $this->getTableColumns('user', $columns, $filter);

		unset($columns['password']);

		return $columns;
	}
}
