<?php

class mysqlidb implements DatabaseInterface
{
	private $mysqli;
	private $error;
	private $last_id;
	private $affected_rows;

	private $hostname;
	private $username;
	private $password;
	private $database;

	static $field_types = array(
		MYSQLI_TYPE_DECIMAL     => 'float',
		MYSQLI_TYPE_TINY        => 'int',
		MYSQLI_TYPE_SHORT       => 'int',
		MYSQLI_TYPE_LONG        => 'int',
		MYSQLI_TYPE_FLOAT       => 'float',
		MYSQLI_TYPE_DOUBLE      => 'float',
		MYSQLI_TYPE_NULL        => 'null',
		MYSQLI_TYPE_TIMESTAMP   => 'int',
		MYSQLI_TYPE_LONGLONG    => 'string',
		MYSQLI_TYPE_INT24       => 'int',
		MYSQLI_TYPE_DATE        => 'string',
		MYSQLI_TYPE_TIME        => 'string',
		MYSQLI_TYPE_DATETIME    => 'string',
		MYSQLI_TYPE_YEAR        => 'string',
		MYSQLI_TYPE_NEWDATE     => 'string',
		MYSQLI_TYPE_BIT         => 'bool',
		MYSQLI_TYPE_NEWDECIMAL  => 'float',
		MYSQLI_TYPE_ENUM        => 'string',
		MYSQLI_TYPE_SET         => 'string',
		MYSQLI_TYPE_TINY_BLOB   => 'string',
		MYSQLI_TYPE_MEDIUM_BLOB => 'string',
		MYSQLI_TYPE_LONG_BLOB   => 'string',
		MYSQLI_TYPE_BLOB        => 'string',
		MYSQLI_TYPE_VAR_STRING  => 'string',
		MYSQLI_TYPE_STRING      => 'string',
		MYSQLI_TYPE_GEOMETRY    => 'string',
	);

	public function __construct($hostname, $username, $password, $database)
	{
		$this->hostname = $hostname;
		$this->username = $username;
		$this->password = $password;
		$this->database = $database;

		$this->mysqli = new mysqli($hostname, $username, $password, $database);

		if ($this->mysqli->connect_error) {
			$this->error = 'Connect Error (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error;
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
		return $this->error;
	}

	public function query($sql, $cast_type = true)
	{
		$result = $this->mysqli->query($sql);

		if ($result) {
			if (is_object($result)) {
				$data = array();

				while ($row = $result->fetch_assoc()) {
					$data[] = $row;
				}

				if ($data && $cast_type) {
					$fields = array();

					foreach ($result->fetch_fields() as $field) {
						$fields[$field->name] = isset(self::$field_types[$field->type]) ? self::$field_types[$field->type] : '';
					}

					foreach ($data as &$row) {
						foreach ($row as $key => &$value) {
							if (!is_null($value)) {
								switch ($fields[$key]) {
									case 'float':
										$value = (float)$value;
										break;
									case 'int':
										$value = (int)$value;
										break;
									case 'null':
										$value = null;
										break;
									case 'bool':
										$value = (bool)$value;
										break;
								}
							}
						}
						unset($value);
					}
					unset($row);
				}

				$query           = new stdclass();
				$query->num_rows = $result->num_rows;
				$query->row      = isset($data[0]) ? $data[0] : array();
				$query->rows     = $data;

				$result->free();

				return $query;
			} else {
				$this->last_id       = $this->mysqli->insert_id;
				$this->affected_rows = $this->mysqli->affected_rows;
				return true;
			}
		} else {
			$this->error = "<strong>MySQLi Error (" . $this->mysqli->errno . "):</strong> " . $this->mysqli->error . "<br /><br />$sql";

			return false;
		}
	}

	public function multi_query($sql)
	{
		$this->mysqli->multi_query($sql);

		while ($this->mysqli->more_results() && $this->mysqli->next_result()) {
		}

		if ($this->mysqli->errno) {
			$this->error = "<strong>MySQLi Error (" . $this->mysqli->errno . "):</strong> " . $this->mysqli->error . "<br /><br />$sql";

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
}
