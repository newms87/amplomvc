<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


if (!isset($_GET['scope_id'])) {
	echo "NO SCOPE ID TO RESEND";
	exit;
}

$hostname = 'localhost';
$username = 'root';
$password = 'rgm22';
$database = 'polyscope';

$mysqli = new mysqli($hostname, $username, $password, $database);

function query($sql)
{
	global $mysqli;

	$result = $mysqli->query($sql);

	if ($result) {
		if (is_object($result)) {
			$data = array();

			while ($row = $result->fetch_assoc()) {
				$data[] = $row;
			}

			$query           = new stdclass();
			$query->num_rows = $result->num_rows;
			$query->row      = isset($data[0]) ? $data[0] : array();
			$query->rows     = $data;

			$result->free();

			return $query;
		} else {
			return true;
		}
	} else {
		echo "<strong>MySQLi Error (" . $mysqli->errno . "):</strong> " . $mysqli->error . "<br /><br />$sql";

		return false;
	}
}

$scope_id = $_GET['scope_id'];

$result = query("SELECT * FROM scopes WHERE id = " . (int)$scope_id);

if ($result) {
	$scope = $result->row;

	$result = query("SELECT * FROM scopemeta WHERE scope_id = " . (int)$scope_id);

	if ($result) {
		foreach ($result->rows as $row) {
			$scope[$row['meta_key']] = $row['meta_value'];
		}
	}
}

echo json_encode($scope);
