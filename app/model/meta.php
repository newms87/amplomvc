<?php

class App_Model_Meta extends App_Model_Table
{
	protected $table = 'meta', $primary_key = 'meta_id', $meta = array();

	public function set($type, $record_id, $key, $value)
	{
		$this->clear($type, $record_id, $key);

		$serialized = (int)_is_object($value);

		$meta = array(
			'type'       => $type,
			'record_id'  => $record_id,
			'key'        => $key,
			'value'      => $serialized ? serialize($value) : $value,
			'serialized' => $serialized,
			'date'       => $this->date->now(),
		);

		return $this->insert($this->table, $meta);
	}

	public function get($type, $record_id, $key = null, $default = null)
	{
		if ($key) {
			$row = $this->queryRow("SELECT `value`, `serialized` FROM {$this->t['meta']} WHERE `type` = '" . $this->escape($type) . "' AND record_id = " . (int)$record_id . " AND `key` = '" . $this->escape($key) . "'");

			if ($row) {
				return $row['serialized'] ? unserialize($row['value']) : $row['value'];
			} else {
				return $default;
			}
		} else {
			$rows = $this->queryRows("SELECT * FROM {$this->t['meta']} WHERE `type` = '" . $this->escape($type) . "' AND record_id = " . (int)$record_id);

			if ($rows) {
				$meta = array();

				foreach ($rows as $row) {
					$meta[$row['key']] = $row['serialized'] ? unserialize($row['value']) : $row['value'];
				}

				return $meta;
			} else {
				return $default === null ? array() : $default;
			}
		}
	}

	public function clear($type, $record_id, $key = null)
	{
		$where = array(
			'type'      => $type,
			'record_id' => $record_id,
			'key'       => $key,
		);

		return $this->delete($this->table, $where);
	}
}
