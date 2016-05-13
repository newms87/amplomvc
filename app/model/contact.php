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
			if ($contact_id = $this->findRecord($contact)) {
				if (empty($contact['address'])) {
					return $contact_id;
				}
			}
		}

		if ($this->error) {
			return false;
		}

		if (!empty($contact['name']) && !isset($contact['first_name']) && !isset($contact['last_name'])) {
			$name_parts            = explode(' ', $contact['name'], 2);
			$contact['first_name'] = $name_parts[0];

			if (isset($name_parts[1])) {
				$contact['last_name'] = $name_parts[1];
			}
		}

		if (!empty($contact['address'])) {
			$first_name = !empty($contact['first_name']) ? $contact['first_name'] : '';
			$last_name  = !empty($contact['last_name']) ? $contact['last_name'] : '';
			$company    = !empty($contact['company']) ? $contact['company'] : '';

			$contact['address'] += array(
				'name'    => trim($first_name . ' ' . $last_name),
				'company' => $company,
			);

			$contact['address_id'] = $this->Model_Address->save($this->Model_Contact->getField($contact_id, 'address_id'), $contact['address']);

			if (!$contact['address_id']) {
				$this->error += $this->Model_Address->fetchError();

				if (!empty($contact['address_required'])) {
					return false;
				}
			}
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

	public function getContact($contact_id)
	{
		$contact = $this->getRecord($contact_id);

		if ($contact) {
			$contact['name'] = trim($contact['first_name'] . ' ' . $contact['last_name']);

			if ($contact['phone']) {
				$contact['phone'] = format('phone', $contact['phone']);
			}

			if ($contact['address_id']) {
				$contact['address']      = $this->getAddress($contact['address_id']);
				$contact['full_address'] = format('address', $contact['address']);
			}
		}

		return $contact;
	}

	public function getRecords($sort = array(), $filter = array(), $options = array(), $total = false)
	{
		if (!empty($filter['keywords'])) {
			$keywords = $this->escape($filter['keywords']);
			$phone    = preg_replace("/[^\\d]/", '', $keywords);

			$filter['#search'] = "(first_name like '%$keywords%' OR last_name like '%$keywords%' OR company like '%$keywords%' " . ($phone ? "OR phone like '%$phone%'" : '') . " OR email like '%$keywords%')";
		}

		return parent::getRecords($sort, $filter, $options, $total);
	}

	public function getContacts($sort = array(), $filter = array(), $options = array(), $total = false)
	{
		$records = $this->getRecords($sort, $filter, $options, $total);

		$total ? $contacts = &$records[0] : $contacts = &$records;

		foreach ($contacts as &$contact) {
			$contact['name'] = trim($contact['first_name'] . ' ' . $contact['last_name']);

			if ($contact['phone']) {
				$contact['phone'] = format('phone', $contact['phone']);
			}

			if ($contact['address_id']) {
				$contact['address']      = $this->getAddress($contact['address_id']);
				$contact['full_address'] = format('address', $contact['address']);
			}
		}
		unset($contact);

		return $records;
	}

	public function getAddress($address_id)
	{
		return $this->Model_Address->getRecord($address_id, 'address, address_2, city, country_id, zone_id, postcode');
	}
}
