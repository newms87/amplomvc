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
		if (SHOW_DB_PROFILE && !$this->request->isAjax()) {
			$this->dbProfile();
		}

		//Performance Logging
		if (option('config_performance_log')) {
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
		$stats = array(
			'peak_memory'          => $this->tool->bytes2str(memory_get_peak_usage(true)),
			'count_included_files' => count(get_included_files()),
			'execution_time'       => microtime(true) - $__start,
		);

		$html = "<div class=\"title\">" . _l("System Performance:") . "</div>";

		foreach ($stats as $key => $s) {
			$html .= "<div>$key = $s</div>";
		}

		$html = "<div class=\"performance\">$html</div>";

		$html = "<script>$.ac_msg('success', '$html')</script>";

		$this->output = str_replace("</body>", $html . "</body>", $this->output);
	}

	private function dbProfile()
	{
		global $__start;

		$file = $this->theme->getFile('common/amplo_profile', 'fluid');

		if ($file) {
			$profile = $this->db->getProfile();

			$db_time = 0;

			usort($profile, function ($a, $b) {
					return $a['time'] < $b['time'];
				});

			foreach ($profile as $p) {
				$db_time += $p['time'];
			}

			$run_time = microtime(true) - $__start;

			$mb          = 1024 * 1024;
			$memory      = (memory_get_peak_usage() / $mb) . " MB";
			$real_memory = (memory_get_peak_usage(true) / $mb) . " MB";

			$file_list   = get_included_files();
			$total_files = count($file_list);
			ob_start();
			include($file);
			$html = ob_get_clean();

			$this->output = str_replace("</body>", $html . "</body>", $this->output);
		}
	}
}
