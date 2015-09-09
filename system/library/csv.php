<?php

class Csv extends Library
{
	private $contents = '';

	public function getContents()
	{
		return $this->contents;
	}

	public function setContents($contents)
	{
		$this->contents = $contents;
	}

	public function import($file)
	{
		$data = array();

		if (($handle = fopen($file, "r")) !== false) {
			if (($head = fgetcsv($handle, 1000, ",")) !== false) {
				while (($row = fgetcsv($handle, 1000, ",")) !== false) {
					$data[] = array_combine($head, $row);
				}
			}

			fclose($handle);
		}

		return $data;
	}

	public function saveFile($file)
	{
		_is_writable($file);

		file_put_contents($file, $this->contents);
	}

	public function download($filename, $type = 'csv')
	{
		output_as_file($this->contents, $type, $filename);
	}

	/**
	 * Generates a CSV string using $columns for the first row (optional) and $rows for the rest of the rows. Saves the
	 * string to $this->content, to be exported to a file, downloaded, or use $this->csv->getContents() to retrieve the
	 * csv string.
	 *
	 * @param array $columns      - $key => $value pairs, where $key matches the associated column index in $data, and
	 *                            $value is the Title for the column
	 * @param array $rows         - an array where each element is the row as an associative array matching the $columns
	 *                            index
	 * @param bool  $row_headings - include the column names in the first row.
	 */

	public function generateCsv($columns, $rows, $row_headings = true)
	{
		$num_cols = count($columns);

		if ($row_headings) {
			$index = 0;
			foreach ($columns as $col) {
				$this->contents .= '"' . $col . '"' . ($index++ < $num_cols ? ',' : '');
			}

			$this->contents .= "\r\n";
		}

		foreach ($rows as $row) {
			$index = 0;
			foreach (array_keys($columns) as $key) {
				$value = isset($row[$key]) ? $row[$key] : '';

				$this->contents .= '"' . str_replace('"', '""', $value) . '"' . ($index++ < $num_cols ? ',' : '');
			}

			$this->contents .= "\r\n";
		}
	}

	public function extractZip($zip_file, $destination = null, $entries = null)
	{
		if (!is_file($zip_file)) {
			$this->error['file'] = _l("The Zip file %s does not exist.", $zip_file);

			return false;
		}

		$zip = new ZipArchive;

		if (!$zip->open($zip_file)) {
			$this->error['open'] = _l("Unable to open zip archive %s.", $zip_file);

			return false;
		}

		if (!$destination) {
			$pathinfo    = pathinfo($zip_file);
			$destination = $pathinfo['dirname'] . '/' . $pathinfo['filename'];
		}

		if (!_is_writable($destination)) {
			$this->error['destination'] = _l("The Destination folder %s is not writable.", $destination);

			return false;
		}

		$zip->extractTo($destination, $entries);
		$zip->close();

		return $destination;
	}
}
