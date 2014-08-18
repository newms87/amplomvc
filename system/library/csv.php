<?php
class Export extends Library
{
	private $contents = '';

	public function getContents()
	{
		return $this->contents;
	}

	public function import($file)
	{
		$data = array();

		if (($handle = fopen($file, "r")) !== FALSE) {
			while (($data[] = fgetcsv($handle, 1000, ",")) !== FALSE) {
				html_dump($data, 'data');
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

	public function downloadFile($file, $filename = null, $type = null)
	{
		if (!is_file($file)) {
			message('warning', "Export: $file failed. File not found.");

			return false;
		}

		$this->contents = file_get_contents($file);

		if (!$type) {
			$type = preg_replace("/.*\\.([a-z0-9]+)$/i", '$1', $file);
		}

		if (!$filename) {
			$filename = basename($file);
		}

		$this->downloadContents($filename, $type);
	}

	public function downloadContents($filename = null, $type = null)
	{
		if (!$filename) {
			$filname = "file_download" . $type;
		}

		switch ($type) {
			case 'csv':
				$headers = array(
					"Content-type: text/csv",
					"Content-Disposition: attachment; filename=\"$filename\"",
					"Pragma: no-cache",
					"Expires: 0",
					"Content-Length: " . strlen($this->contents),
				);
				break;
			case 'xls':
			case 'xlsx':
			default:
				$headers = array(
					"Content-Type: application/octet-stream",
					"Content-Description: File Transfer",
					"Content-Disposition: attachment; filename=\"$filename\"",
					"Content-Transfer-Encoding: binary",
					"Cache-Control: must-revalidate, post-check=0, pre-check=0",
					"Pragma: public",
					"Expires: 0",
					"Content-Length: " . strlen($this->contents),
				);

				break;
		}

		$this->response->setHeader($headers);

		output($this->contents);

		$this->response->output();

		exit();
	}

	public function generateCsv($columns, $data, $row_headings = true)
	{
		$num_cols = count($columns);

		if ($row_headings) {
			$index = 0;
			foreach ($columns as $col) {
				$this->contents .= '"' . $col . '"' . ($index++ < $num_cols ? ',' : '');
			}

			$this->contents .= "\r\n";
		}

		foreach ($data as $d) {
			$index = 0;
			foreach (array_keys($columns) as $key) {
				$value = isset($d[$key]) ? $d[$key] : '';

				$this->contents .= '"' . $value . '"' . ($index++ < $num_cols ? ',' : '');
			}

			$this->contents .= "\r\n";
		}
	}
}
