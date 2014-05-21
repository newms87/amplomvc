<?php
class App_Model_User_User extends Model
{
	public function addUser($data)
	{
		$data['date_added'] = $this->date->now();

		if (!empty($data['password'])) {
			$data['password'] = $this->user->encrypt($data['password']);
		}

		$user_id = $this->insert('user', $data);

		return $user_id;
	}

	public function editUser($user_id, $data)
	{
		if (!empty($data['password'])) {
			$data['password'] = $this->user->encrypt($data['password']);
		}

		$this->update('user', $data, $user_id);
	}

	public function editPassword($user_id, $password)
	{
		$data = array(
			'password' => $this->user->encrypt($password),
		);

		$this->update('user', $data, $user_id);
	}

	public function deleteUser($user_id)
	{
		$this->delete('user', $user_id);
	}

	public function getUser($user_id)
	{
		return $this->queryRow("SELECT * FROM `" . DB_PREFIX . "user` WHERE user_id = '" . (int)$user_id . "'");
	}

	public function getUserByUsername($username)
	{
		return $this->queryRow("SELECT * FROM `" . DB_PREFIX . "user` WHERE username = '" . $this->escape($username) . "'");
	}

	public function getUsers($data = array(), $select = '*', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		}

		//From
		$from = DB_PREFIX . "user";

		//Where
		$where = "1";

		//Order and Limit
		if (!$total) {
			$order = $this->extractOrder($data);
			$limit = $this->extractLimit($data);
		} else {
			$order = '';
			$limit = '';
		}

		//The Query
		$query = "SELECT $select FROM $from WHERE $where $order $limit";

		$result = $this->query($query);

		if ($total) {
			return $result->row['total'];
		}

		return $result->rows;
	}

	public function getTotalUsers($data = array())
	{
		return $this->getUsers($data, '', true);
	}

	public function getTotalUsersByGroupId($user_group_id)
	{
		return $this->queryVar("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "user` WHERE user_group_id = '" . (int)$user_group_id . "'");
	}

	public function getTotalUsersByEmail($email)
	{
		return $this->queryVar("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "user` WHERE email = '" . $this->escape($email) . "'");
	}
}
