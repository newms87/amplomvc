<?php

class Response extends Library
{
	private $headers = array();
	private $level = 0;
	private $output;

	public function addHeader($header)
	{
		$this->headers[] = $header;
	}

	public function setHeader($header)
	{
		$this->headers = is_array($header) ? $header : array($header);
	}

	public function redirect($url)
	{
		header('Location: ' . $url);
		exit;
	}

	public function setCompression($level)
	{
		$this->level = $level;
	}

	public function setOutput($output)
	{
		$this->output = $output;
	}

	private function compress($data, $level = 0)
	{
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false)) {
			$encoding = 'gzip';
		}

		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false)) {
			$encoding = 'x-gzip';
		}

		if (!isset($encoding)) {
			return $data;
		}

		if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
			return $data;
		}

		if (headers_sent()) {
			return $data;
		}

		if (connection_status()) {
			return $data;
		}

		$this->addHeader('Content-Encoding: ' . $encoding);

		return gzencode($data, (int)$level);
	}

	public function output()
	{
		//Database Profiling
		if (option('config_performance_log', true) && !$this->request->isAjax()) {
			$this->performance();
		}

		if ($this->output) {
			if ($this->level) {
				$ouput = $this->compress($this->output, $this->level);
			} else {
				$ouput = $this->output;
			}

			if (!headers_sent()) {
				foreach ($this->headers as $header) {
					header($header, true);
				}
			}

			echo $ouput;
		}
	}

	private function performance()
	{
		global $__start;

		$file = $this->theme->getFile('common/amplo_profile', 'fluid');

		if ($file) {
			if (DB_PROFILE) {
				$profile = $this->db->getProfile();

				$db_time = 0;

				usort($profile, function ($a, $b) {
						return $a['time'] < $b['time'];
					});

				foreach ($profile as $p) {
					$db_time += $p['time'];
				}

				$db_time = round($db_time, 6) . ' seconds';
			}

			$run_time = round(microtime(true) - $__start, 6);

			$mb          = 1024 * 1024;
			$memory      = round(memory_get_peak_usage() / $mb, 2) . " MB";
			$real_memory = round(memory_get_peak_usage(true) / $mb, 2) . " MB";

			$file_list   = get_included_files();
			$total_files = count($file_list);

			foreach ($file_list as &$f) {
				$f = array(
					'name' => $f,
				   'size' => filesize($f),
				);
			}
			unset($f);

			uasort($file_list, function ($a, $b) {return $a['size'] < $b['size'];});

			foreach ($file_list as &$f) {
				$f['size'] = round($f['size'] / 1024, 2) . " KB";
			}
			unset($f);

			ob_start();
			include($file);
			$html = ob_get_clean();

			$this->output = str_replace("</body>", $html . "</body>", $this->output);
		}
	}
}
