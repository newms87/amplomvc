<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

final class SQLite implements DatabaseInterface
{
	private $link;
	private $err_msg;

	public function __construct($hostname, $username, $password, $database)
	{


		if (!$this->link = sqlite_open($hostname, $username, $password)) {
			trigger_error('Error: Could not make a database link using ' . $username . '@' . $hostname);
		}

		if (!mysql_select_db($database, $this->link)) {
			trigger_error('Error: Could not connect to database ' . $database);
		}

		mysql_set_charset('utf8', $this->link);
		mysql_query("SET NAMES 'utf8'", $this->link);
		mysql_query("SET CHARACTER SET utf8", $this->link);
		mysql_query("SET SQL_MODE = ''", $this->link);
	}

	public function getError()
	{
		return $this->err_msg;
	}

	public function query($sql)
	{
		$resource = mysql_query($sql, $this->link);

		if ($resource) {
			if (is_resource($resource)) {
				$i = 0;

				$data = array();

				while ($result = mysql_fetch_assoc($resource)) {
					$data[$i] = $result;

					$i++;
				}

				mysql_free_result($resource);

				$query           = new stdclass();
				$query->row      = isset($data[0]) ? $data[0] : array();
				$query->rows     = $data;
				$query->num_rows = $i;

				unset($data);

				return $query;
			} else {
				return true;
			}
		} else {
			$this->err_msg = 'Error: ' . mysql_error($this->link) . '<br />Error No: ' . mysql_errno($this->link) . '<br />' . $sql;
			return false;
		}
	}

	public function escape($value)
	{
		return mysql_real_escape_string($value, $this->link);
	}

	public function escapeHtml($value)
	{
		return mysql_real_escape_string(htmlspecialchars_decode($value), $this->link);
	}

	public function countAffected()
	{
		return mysql_affected_rows($this->link);
	}

	public function getLastId()
	{
		return mysql_insert_id($this->link);
	}
}
