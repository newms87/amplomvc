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
		if (DB_PROFILE && !$this->request->isAjax()) {
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

		$html = "<script>show_msg('success', '$html')</script>";

		$this->output = str_replace("</body>", $html . "</body>", $this->output);
	}

	private function dbProfile()
	{
		$profile = $this->db->getProfile();

		$total = 0;

		$html = '';

		usort($profile, function ($a, $b) { return $a['time'] < $b['time']; });

		foreach ($profile as $p) {
			$total += $p['time'];
			$html .= "<div>$p[time]<span>$p[query]</span></div>";
		}

		$total = _l("Total Time: %s in %s transactions", $total, count($profile));

		$html = <<<HTML
<div id="db_profile_box" style="display:none">
<style>
	#db_profile{
		clear:both;
	}
	#db_profile .profile_list{
		position:relative;
	}
	#db_profile .profile_list div{
		position:relative;
		margin: 10px 0 10px 15px;
		cursor: pointer;
		padding: 5px 10px;
		background: #38B0E3;
		border-radius: 5px;
		width: 200px;
	}
	#db_profile .profile_list div span {
		position:absolute;
		width: 400px;
		top:0;
		left: 10%;
		display:none;
		background:white;
		padding: 10px 20px;
		border-radius: 10px;
		box-shadow: 5px 5px 5px rgba(0,0,0,.6);
		z-index: 10;
	}
	#db_profile .profile_list div:hover span{
		display:block;
	}
</style>

	<div id="db_profile">
		<div class="total">$total</div>
		<div class="profile_list">$html</div>
	</div>
</div>

<script type="text/javascript">
var w = window.open(null, "DB_Profiler");

if (w) {
	w.document.title = "DB Profile";
	w.document.body.innerHTML = document.getElementById('db_profile_box').innerHTML;
}
</script>
HTML;

		$this->output = str_replace("</body>", $html . "</body>", $this->output);
	}
}
