<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

final class MSSQL implements DatabaseInterface
{
	private $link;
	private $err_msg;

	public function __construct($hostname, $username, $password, $database)
	{
		if (!$this->link = mssql_connect($hostname, $username, $password)) {
			exit('Error: Could not make a database connection using ' . $username . '@' . $hostname);
		}

		if (!mssql_select_db($database, $this->link)) {
			exit('Error: Could not connect to database ' . $database);
		}

		mssql_query("SET NAMES 'utf8'", $this->link);
		mssql_query("SET CHARACTER SET utf8", $this->link);
	}

	public function getError()
	{
		return $this->err_msg;
	}

	public function query($sql)
	{
		$resource = mssql_query($sql, $this->link);

		if ($resource) {
			if (is_resource($resource)) {
				$i = 0;

				$data = array();

				while ($result = mssql_fetch_assoc($resource)) {
					$data[$i] = $result;

					$i++;
				}

				mssql_free_result($resource);

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
			$this->err_msg = 'Error: ' . mssql_get_last_message($this->link) . '<br />' . $sql;
			return false;
		}
	}

	public function escape($value)
	{
		$unpacked = unpack('H*hex', $value);

		return '0x' . $unpacked['hex'];
	}

	public function escapeHtml($value)
	{
		return $this->escape(htmlspecialchars_decode($value), $this->link);
	}

	public function countAffected()
	{
		return mssql_rows_affected($this->link);
	}

	public function getLastId()
	{
		$last_id = false;

		$resource = mssql_query("SELECT @@identity AS id", $this->link);

		if ($row = mssql_fetch_row($resource)) {
			$last_id = trim($row[0]);
		}

		mssql_free_result($resource);

		return $last_id;
	}
}
