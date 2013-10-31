<?php
//TODO: Move this to System/Model.
class Admin_Model_User_User extends Model
{
	public function addUser($data)
	{
		$data['date_added'] = $this->date->now();

		if (!empty($data['password'])) {
			$data['password'] = $this->user->encrypt($data['password']);
		}

		$user_id = $this->insert('user', $data);

		if (isset($data['contact'])) {
			foreach ($data['contact'] as $contact) {
				$this->Model_Includes_Contact->addContact('user', $user_id, $contact);
			}
		}

		return $user_id;
	}

	public function editUser($user_id, $data)
	{
		if (!empty($data['password'])) {
			$data['password'] = $this->user->encrypt($data['password']);
		}

		$this->update('user', $data, $user_id);

		$this->Model_Includes_Contact->deleteContactByType('user', $user_id);

		if (isset($data['contact'])) {
			foreach ($data['contact'] as $contact) {
				$this->Model_Includes_Contact->addContact('user', $user_id, $contact);
			}
		}
	}

	public function editPassword($user_id, $password)
	{
		$data = array(
			'password' => $this->user->encrypt($password),
		);

		$this->update('user', $data, $user_id);
	}

	public function editCode($email, $code)
	{
		$this->update('user', array('code' => $code), array('email' => $email));
	}

	public function deleteUser($user_id)
	{
		$this->delete('user', $user_id);

		$this->Model_Includes_Contact->deleteContactByType('user', $user_id);
	}

	public function getUser($user_id)
	{
		return $this->queryRow("SELECT * FROM `" . DB_PREFIX . "user` WHERE user_id = '" . (int)$user_id . "'");
	}

	public function getUserByUsername($username)
	{
		return $this->queryRow("SELECT * FROM `" . DB_PREFIX . "user` WHERE username = '" . $this->escape($username) . "'");
	}

	public function getUserByCode($code)
	{
		return $this->queryRow("SELECT * FROM `" . DB_PREFIX . "user` WHERE code = '" . $this->escape($code) . "' AND code != ''");
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
			$order = $this->extract_order($data);
			$limit = $this->extract_limit($data);
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

	public function getUserContactInfo($user_id)
	{
		return $this->Model_Includes_Contact->getContactsByType('user', $user_id);
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
