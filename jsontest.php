<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

function run_test($file)
{
	echo 'TESTING ' . $file . '<BR>';

	$testArray = unserialize(file_get_contents($file));

	$start        = microtime(true);
	$json_encoded = json_encode($testArray);
	$json_e       = microtime(true) - $start;

	$start = microtime(true);
	json_decode($json_encoded);
	$json_d = microtime(true) - $start;

	echo "JSON encoded in $json_e seconds / decoded in $json_d seconds<br>";

//  Time serialization
	$start       = microtime(true);
	$serialized  = serialize($testArray);
	$serialize_e = microtime(true) - $start;

	$start = microtime(true);
	unserialize($serialized);
	$serialize_d = microtime(true) - $start;

	echo "PHP serialized in $serialize_e seconds / unserialized in $serialize_d seconds<br>";

//  Compare them
	if ($json_e < $serialize_e) {
		echo "json_encode() was roughly " . number_format(($serialize_e / $json_e - 1) * 100, 2) . "% faster than serialize()<Br>";
	} else if ($serialize_e < $json_e) {
		echo "serialize() was roughly " . number_format(($json_e / $serialize_e - 1) * 100, 2) . "% faster than json_encode()<Br>";
	} else {
		echo 'JSON encode / serialize === Unpossible!<Br>';
	}

	if ($json_d < $serialize_d) {
		echo "json_decode() was roughly " . number_format(($serialize_d / $json_d - 1) * 100, 2) . "% faster than unserialize()";
	} else if ($serialize_d < $json_d) {
		echo "unserialize() was roughly " . number_format(($json_d / $serialize_d - 1) * 100, 2) . "% faster than json_decode()";
	} else {
		echo 'JSON decode / unserialize === Unpossible!';
	}

	echo "<BR><BR>";
}

$files = glob(dirname(__FILE__) . '/system/cache/*');

foreach ($files as $file) {
	if (is_file($file)) {
		run_test($file);
	}
}
