<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

function _count_depth($array)
{
	$count     = 0;
	$max_depth = 0;
	foreach ($array as $a) {
		if (is_array($a)) {
			list($cnt, $depth) = _count_depth($a);
			$count += $cnt;
			$max_depth = max($max_depth, $depth);
		} else {
			$count++;
		}
	}

	return array(
		$count,
		$max_depth + 1,
	);
}

function run_test($file)
{
	$memory     = memory_get_usage();
	$test_array = unserialize(file_get_contents($file));
	$memory     = round((memory_get_usage() - $memory) / 1024, 2);

	if (empty($test_array) || !is_array($test_array)) {
		return;
	}

	list($count, $depth) = _count_depth($test_array);

	//JSON encode test
	$start            = microtime(true);
	$json_encoded     = json_encode($test_array);
	$json_encode_time = microtime(true) - $start;

	//JSON decode test
	$start = microtime(true);
	json_decode($json_encoded);
	$json_decode_time = microtime(true) - $start;

	//serialize test
	$start          = microtime(true);
	$serialized     = serialize($test_array);
	$serialize_time = microtime(true) - $start;

	//unserialize test
	$start = microtime(true);
	unserialize($serialized);
	$unserialize_time = microtime(true) - $start;

	return array(
		'Name'               => basename($file),
		'json_encode() Time' => $json_encode_time,
		'json_decode() Time' => $json_decode_time,
		'serialize() Time'   => $serialize_time,
		'unserialize() Time' => $unserialize_time,
		'Elements'           => $count,
		'Memory'             => $memory,
		'Max Depth'          => $depth,
		'json_encode() Win'  => ($json_encode_time > 0 && $json_encode_time < $serialize_time) ? number_format(($serialize_time / $json_encode_time - 1) * 100, 2) : '',
		'serialize() Win'    => ($serialize_time > 0 && $serialize_time < $json_encode_time) ? number_format(($json_encode_time / $serialize_time - 1) * 100, 2) : '',
		'json_decode() Win'  => ($json_decode_time > 0 && $json_decode_time < $serialize_time) ? number_format(($serialize_time / $json_decode_time - 1) * 100, 2) : '',
		'unserialize() Win'  => ($unserialize_time > 0 && $unserialize_time < $json_decode_time) ? number_format(($json_decode_time / $unserialize_time - 1) * 100, 2) : '',
	);
}

$files = glob(dirname(__FILE__) . '/system/cache/*');

$data = array();

foreach ($files as $file) {
	if (is_file($file)) {
		$result = run_test($file);

		if ($result) {
			$data[] = $result;
		}
	}
}

$fields = array_keys($data[0]);
?>

<table>
	<thead>
	<tr>
		<?php foreach ($fields as $f) { ?>
			<td style="border:1px solid black;padding: 4px 8px;font-weight:bold;font-size:1.1em"><?= $f; ?></td>
		<?php } ?>
	</tr>
	</thead>

	<tbody>
	<?php foreach ($data as $d) { ?>
		<tr>
			<?php foreach ($d as $key => $value) { ?>
				<?php $color = (strpos($key, 'Win') && $value) ? 'color: green;font-weight:bold;' : ''; ?>
				<td style="text-align: center; vertical-align: middle; padding: 3px 6px; border: 1px solid gray; <?= $color; ?>"><?= $value; ?></td>
			<?php } ?>
		</tr>
	<?php } ?>
	</tbody>
</table>

