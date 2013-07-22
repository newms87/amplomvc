<?php
class Admin_Model_Sale_Return extends Model
{
	public function addReturn($data)
	{
		$data['date_added'] = $this->date->now();
		$data['date_modified'] = $this->date->now();
		
		$return_id = $this->insert('return', $data);
		
		return $return_id;
	}
	
	public function editReturn($return_id, $data)
	{
		$data['date_modified'] = $this->date->now();
		
		$this->update('return', $data, $return_id);
	}
	
	public function editReturnAction($return_id, $return_action_id)
	{
		$data = array(
			'return_action_id' => $return_action_id,
		);
		
		$this->update('return', $data, $return_id);
	}
		
	public function deleteReturn($return_id)
	{
		$this->delete('return', $return_id);
		$this->delete('return_history', array('return_id' => $return_id));
	}
	
	public function getReturn($return_id)
	{
		return $this->queryRow("SELECT *, CONCAT(firstname, ' ', lastname) as customer FROM " . DB_PREFIX . "return WHERE return_id = '" . (int)$return_id . "'");
	}
		
	public function getReturns($data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
			$select = "*, CONCAT(r.firstname, ' ', r.lastname) as customer";
		}
		
		//From
		$from = DB_PREFIX . "return r";
		
		//Where
		$where = "1";
		
		if (!empty($data['return_reason_ids'])) {
			$where .= " AND return_reason_id IN (" . implode(',', $data['return_reason_ids']) . ")";
		}
		
		if (!empty($data['return_action_ids'])) {
			$where .= " AND return_action_id IN (" . implode(',', $data['return_action_ids']) . ")";
		}
		
		if (!empty($data['return_status_ids'])) {
			$where .= " AND return_status_id IN (" . implode(',', $data['return_status_ids']) . ")";
		}
		
		if (!empty($data['return_ids'])) {
			$where .= " AND return_id IN (" . implode(',', $data['return_ids']) . ")";
		}
		
		if (!empty($data['order_ids'])) {
			$where .= " AND order_id IN (" . implode(',', $data['order_ids']) . ")";
		}
		
		if (!empty($data['product_ids'])) {
			$where .= " AND product_id IN (" . implode(',', $data['product_ids']) . ")";
		}
		
		if (!empty($data['customer'])) {
			$where .= " AND LCASE(CONCAT(r.firstname, ' ', r.lastname)) like '%" . $this->db->escape(strtolower($data['customer'])) . "%'";
		}
		
		if (!empty($data['date_added'])) {
			$where .= " AND DATE(r.date_added) = DATE('" . $this->db->escape($data['date_added']) . "')";
		}

		if (!empty($data['date_modified'])) {
			$where .= " AND DATE(r.date_modified) = DATE('" . $this->db->escape($data['date_modified']) . "')";
		}
		
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
						
	public function getTotalReturns($data = array()) {
		return $this->getReturns($data, '', true);
	}
	
	public function addReturnHistory($return_id, $data)
	{
		$return_data = array(
			'return_status_id' => $data['return_status_id'],
			'date_modified' => $this->date->now(),
		);
		
		$this->update('return', $return_data, $return_id);
		
		$data['return_id'] = $return_id;
		
		if (!isset($data['notify'])) {
			$data['notify'] = 0;
		}
		
		$data['date_added'] = $this->date->now();
		
		$return_history_id = $this->insert('return_history', $data);
		
		//TODO: move this either the controller or to an emailer system
		if ($data['notify']) {
			$return_query = $this->query("SELECT *, rs.name AS status FROM `" . DB_PREFIX . "return` r LEFT JOIN " . DB_PREFIX . "return_status rs ON (r.return_status_id = rs.return_status_id) WHERE r.return_id = '" . (int)$return_id . "' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
			if ($return_query->num_rows) {
				$this->language->load('mail/return');

				$subject = sprintf($this->_('text_subject'), $this->config->get('config_name'), $return_id);

				$message  = $this->_('text_return_id') . ' ' . $return_id . "\n";
				$message .= $this->_('text_date_added') . ' ' . $this->date->format($return_query->row['date_added'], 'short') . "\n\n";
				$message .= $this->_('text_return_status') . "\n";
				$message .= $return_query->row['status'] . "\n\n";

				if ($data['comment']) {
					$message .= $this->_('text_comment') . "\n\n";
					$message .= strip_tags(html_entity_decode($data['comment'], ENT_QUOTES, 'UTF-8')) . "\n\n";
				}

				$message .= $this->_('text_footer');

				$this->mail->init();
				
				$this->mail->setTo($return_query->row['email']);
				$this->mail->setFrom($this->config->get('config_email'));
				$this->mail->setSender($this->config->get('config_name'));
				$this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$this->mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
				$this->mail->send();
			}
		}
	}
	
	public function getReturnHistories($return_id, $data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
			$select = "*";
		}
		
		//From
		$from = DB_PREFIX . "return_history";
		
		//Where
		$where = "1";
		
		if (!empty($data['return_status_ids'])) {
			$where .= " AND return_status_id IN (" . implode(',', $data['return_status_ids']) . ")";
		}
		
		if (!empty($data['date_added'])) {
			$where .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['date_added']) . "')";
		}
		
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
	
	public function getTotalReturnHistories($return_id, $data = array())
	{
		return $this->getReturnHistories($return_id, $data);
	}
}