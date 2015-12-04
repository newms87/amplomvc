<?php

class App_Model_Meta extends App_Model_Table
{
	protected $table = 'meta', $primary_key = 'meta_id', $meta = array();

	public function set($type, $record_id, $key, $value)
	{
		$serialized = (int)_is_object($value);

		$meta = array(
			'type'       => $type,
			'record_id'  => $record_id,
			'key'        => $key,
			'value'      => $serialized ? serialize($value) : $value,
			'serialized' => $serialized,
			'date'       => $this->date->now(),
		);

		$filter = array(
			'type'      => $type,
			'record_id' => $record_id,
			'key'       => $key,
		);

		$meta_id = $this->findRecord($filter);

		clear_cache('meta.rows.' . $record_id);

		if ($meta_id !== null) {
			if ($value === null) {
				return $this->delete($this->table, $meta_id);
			} else {
				return $this->update($this->table, $meta, $meta_id);
			}
		} else {
			return $this->insert($this->table, $meta);
		}
	}

	public function setAll($type, $record_id, $data)
	{
		foreach ($data as $key => $value) {
			$this->set($type, $record_id, $key, $value);
		}

		return true;
	}

	public function get($type, $record_id, $key = null, $default = null)
	{
		$meta = cache('meta.rows.' . $record_id);

		if (!$meta) {
			$rows = $this->queryRows("SELECT * FROM {$this->t['meta']} WHERE `type` = '" . $this->escape($type) . "' AND record_id = " . (int)$record_id);

			if ($rows) {
				$meta = array();

				foreach ($rows as $row) {
					$meta[$row['key']] = $row['serialized'] ? unserialize($row['value']) : $row['value'];
				}

				cache('meta.rows.' . $record_id, $meta);
			}
		}

		if ($key) {
			return isset($meta[$key]) ? $meta[$key] : $default;
		} elseif ($meta) {
			return $meta;
		} else {
			return $default === null ? array() : $default;
		}
	}

	public function reverseLookup($type, $key, $value)
	{
		$filter = array(
			'type'  => $type,
			'key'   => $key,
			'value' => $value,
		);

		return $this->findRecord($filter);
	}

	public function removeKey($type, $record_id, $key, $value = null)
	{
		$where = array(
			'type'      => $type,
			'record_id' => $record_id,
			'key'       => $key,
		);

		//Only Remove key if value matches as well.
		if ($value !== null) {
			$where['value'] = $value;
		}

		return $this->delete($this->table, $where);
	}

	public function clear($type, $record_id)
	{
		$where = array(
			'type'      => $type,
			'record_id' => $record_id,
		);

		return $this->delete($this->table, $where);
	}
}
