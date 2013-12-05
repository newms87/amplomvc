<?php
class Admin_Model_Mail_Newsletter extends Model
{
	public function addNewsletter($data)
	{

		$data['data'] = serialize($data['newsletter']);

		$newsletter_id = $this->insert('newsletter', $data);

		return $newsletter_id;
	}

	public function editNewsletter($newsletter_id, $data)
	{

		if (isset($data['newsletter'])) {
			$data['data'] = serialize($data['newsletter']);
		}

		$this->update('newsletter', $data, $newsletter_id);
	}

	public function copyNewsletter($newsletter_id)
	{
		$newsletter = $this->getNewsletter($newsletter_id);

		$newsletter['name'] .= ' - Copy (' . uniqid() . ')';

		$this->insert('newsletter', $newsletter);
	}

	public function deleteNewsletter($newsletter_id)
	{
		$this->delete('newsletter', $newsletter_id);
	}

	public function getNewsletter($newsletter_id)
	{
		$newsletter = $this->queryRow("SELECT * FROM " . DB_PREFIX . "newsletter WHERE newsletter_id = " . (int)$newsletter_id);

		if ($newsletter) {
			$newlsetter['newsletter'] = unserialize($newsletter['data']);

			unset($newsletter['data']);
		}

		return $newsletter;
	}

	public function getNewsletters($data = array(), $select = '*', $total = false)
	{
		if ($total) {
			$select = 'COUNT(*) as total';
		} elseif (!$select) {
			$select = '*';
		}

		$from = DB_PREFIX . "newsletter n";

		$where = "1";

		if (isset($data['name'])) {
			$where .= " AND name like '%" . $this->escape($data['name']) . "%'";
		}

		if (isset($data['send_date']['start'])) {
			$where .= " AND send_date >= '" . $this->escape($data['send_date']['start']) . "'";
		}

		if (isset($data['send_date']['end'])) {
			$where .= " AND send_date <= '" . $this->escape($data['send_date']['end']) . "'";
		}

		if (isset($data['status'])) {
			$where .= " AND status = '" . ($data['status'] ? 1 : 0) . "'";
		}

		if (!$total) {
			$order = $this->extractOrder($data);
			$limit = $this->extractLimit($data);
		} else {
			$order = '';
			$limit = '';
		}

		$query = "SELECT $select FROM $from WHERE $where $order $limit";

		$result = $this->query($query);

		if ($total) {
			return $result->row['total'];
		}

		foreach ($result->rows as $key => &$row) {
			$row['newsletter'] = unserialize($row['data']);
			unset($result->rows[$key]['data']);
		}

		return $result->rows;
	}

	public function getEmailList($store_id = null)
	{
		if (!is_null($store_id)) {
			$store = "AND store_id = '" . (int)$store_id . "'";
		} else {
			$store = '';
		}

		$query = $this->query("SELECT * FROM " . DB_PREFIX . "customer WHERE status='1' AND newsletter = '1' $store");

		return $query->rows;
	}

	public function getTotalNewsletters($data)
	{
		return $this->getNewsletters($data, '', true);
	}
}
