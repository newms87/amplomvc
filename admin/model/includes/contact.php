<?php
class Admin_Model_Includes_Contact extends Model
{
	public function addContact($type, $type_id, $data)
	{
		if (isset($data['phone'])) {
			$data['phone'] = json_encode($this->normalize_phone($data['phone']));
		}

		$contact_id = $this->insert('contact', $data);

		$data['type']       = $type;
		$data['type_id']    = $type_id;
		$data['contact_id'] = $contact_id;

		$this->insert('type_to_contact', $data);
	}

	public function editContact($contact_id, $data)
	{
		if (isset($data['phone'])) {
			$data['phone'] = json_encode($this->normalize_phone($data['phone']));
		}

		$this->update('contact', $data, "contact_id='" . (int)$contact_id . "'");
	}

	public function deleteContact($contact_id)
	{
		$this->delete('contact', array('contact_id' => $contact_id));
		$this->delete('type_to_contact', array('contact_id' => $contact_id));
	}

	public function deleteContactByType($type, $type_id)
	{
		$contacts = $this->getContactsByType($type, $type_id);

		foreach ($contacts as $contact) {
			$this->deleteContact($contact['contact_id']);
		}
	}

	public function getContact($contact_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "contact HWERE contact_id = " . (int)$contact_id);
	}

	public function getContactsByType($type, $type_id)
	{
		$contacts = $this->queryRows("SELECT c.* FROM " . DB_PREFIX . "type_to_contact t2c JOIN " . DB_PREFIX . "contact c ON (t2c.contact_id=c.contact_id) WHERE t2c.type='$type' AND t2c.type_id='$type_id'");

		foreach ($contacts as &$contact) {
			$row['phone'] = json_decode($contact['phone']);
		}
		unset($contact);

		return $contacts;
	}

	private function normalize_phone($phone)
	{
		if (!is_array($phone)) {
			$phone = array(
				'number' => $phone,
				'type'   => "primary"
			);
		} else {
			foreach ($phone as &$p) {
				$p['number'] = preg_replace("/[^\d]/", "", $p['number']);
			}
		}
		return $phone;
	}
}
