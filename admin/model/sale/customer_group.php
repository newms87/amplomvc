<?php
class Admin_Model_Sale_CustomerGroup extends Model
{
	public function addCustomerGroup($data)
	{
		$this->query("INSERT INTO " . DB_PREFIX . "customer_group SET name = '" . $this->escape($data['name']) . "'");
	}

	public function editCustomerGroup($customer_group_id, $data)
	{
		$this->query("UPDATE " . DB_PREFIX . "customer_group SET name = '" . $this->escape($data['name']) . "' WHERE customer_group_id = '" . (int)$customer_group_id . "'");
	}

	public function deleteCustomerGroup($customer_group_id)
	{
		$this->query("DELETE FROM " . DB_PREFIX . "customer_group WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "product_special WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE customer_group_id = '" . (int)$customer_group_id . "'");
	}

	public function getCustomerGroup($customer_group_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "customer_group WHERE customer_group_id = '" . (int)$customer_group_id . "'");

		return $query->row;
	}

	public function getCustomerGroups($data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
			$select = '*';
		}

		//From
		$from = DB_PREFIX . "customer_group";

		//Where
		$where = "1";

		//Order By and Limit
		if (!$total) {
			if (empty($data['sort'])) {
				$data['sort'] = 'name';
				$data['order'] = 'ASC';
			}

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

	public function getTotalCustomerGroups()
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_group");

		return $query->row['total'];
	}
}
