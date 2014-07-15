<?php
class mysqlidb implements Database
{
	private $mysqli;
	private $err_msg;
	private $last_id;
	private $affected_rows;

	private $hostname;
	private $username;
	private $password;
	private $database;

	public function __construct($hostname, $username, $password, $database)
	{
		$this->hostname = $hostname;
		$this->username = $username;
		$this->password = $password;
		$this->database = $database;

		$this->mysqli = new mysqli($hostname, $username, $password, $database);

		if ($this->mysqli->connect_error) {
			$this->err_msg = 'Connect Error (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error;
		} else {
			$this->query("SET NAMES 'utf8'");
			$this->query("SET CHARACTER SET utf8");
			$this->query("SET CHARACTER_SET_CONNECTION=utf8");
			$this->query("SET SQL_MODE = ''");
		}
	}

	public function getName()
	{
		return $this->database;
	}

	public function getError()
	{
		return $this->err_msg;
	}

	public function query($sql)
	{
		$result = $this->mysqli->query($sql);

		if ($result) {
			if (is_object($result)) {
				$data = array();

				while ($row = $result->fetch_assoc()) {
					$data[] = $row;
				}

				$result->free();

				$query           = new stdclass();
				$query->row      = isset($data[0]) ? $data[0] : array();
				$query->rows     = $data;
				$query->num_rows = count($data);

				return $query;
			} else {
				$this->last_id = $this->mysqli->insert_id;
				$this->affected_rows = $this->mysqli->affected_rows;
				return true;
			}
		} else {
			$this->err_msg = "<strong>MySQLi Error (" . $this->mysqli->errno . "):</strong> " . $this->mysqli->error . "<br /><br />$sql";

			return false;
		}
	}

	public function multi_query($sql)
	{
		$this->mysqli->multi_query($sql);

		while ($this->mysqli->more_results() && $this->mysqli->next_result()) {
		}

		if ($this->mysqli->errno) {
			$this->err_msg = "<strong>MySQLi Error (" . $this->mysqli->errno . "):</strong> " . $this->mysqli->error . "<br /><br />$sql";

			return false;
		}

		return true;
	}

	public function setAutoincrement($table, $value)
	{
		return $this->query("ALTER TABLE " . DB_PREFIX . "$table AUTO_INCREMENT=" . (int)$value . "");
	}

	public function escape($value)
	{
		return $this->mysqli->real_escape_string($value);
	}

	public function escapeHtml($value)
	{
		return $this->mysqli->real_escape_string(htmlspecialchars_decode($value));
	}

	public function countAffected()
	{
		return $this->affected_rows;
	}

	public function getLastId()
	{
		return $this->last_id;
	}

	public function __destruct()
	{
		static $called = false;

		if (!$called && !empty($this->mysqli)) {
			$called = true;
			$this->mysqli->close();
		}
	}
}
