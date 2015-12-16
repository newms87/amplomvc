<?php

class App_Model_Contact extends App_Model_Table
{
	protected $table = 'contact', $primary_key = 'contact_id';

	public function save($contact_id, $contact)
	{
		$contact['customer_id'] = customer_info('customer_id');

		if (!$contact_id) {
			if (empty($contact['company']) && empty($contact['first_name']) && empty($contact['last_name'])) {
				$this->error['info'] = _l("Please provide either the name or company or both.");
			}

			//Return existing record if found
			if ($record_id = $this->findRecord($contact)) {
				return $record_id;
			}
		}

		if ($this->error) {
			return false;
		}

		if (!$contact_id) {
			$contact += array(
				'type' => '',
			);
		}

		if (!empty($contact['phone'])) {
			$contact['phone'] = preg_replace("/[^\\d]/", '', $contact['phone']);
		}

		return parent::save($contact_id, $contact);
	}

	public function getRecords($sort = array(), $filter = array(), $options = array(), $total = false)
	{
		if (!empty($filter['keywords'])) {
			$keywords = $this->escape($filter['keywords']);
			$phone    = preg_replace("/[^\\d]/", '', $keywords);

			$filter['#search'] = "AND (first_name like '%$keywords%' OR last_name like '%$keywords%' OR company like '%$keywords%' " . ($phone ? "OR phone like '%$phone%'" : '') . " OR email like '%$keywords%')";
		}

		return parent::getRecords($sort, $filter, $options, $total);
	}
}
