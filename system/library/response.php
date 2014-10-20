<?php

class Response extends Library
{
	private $headers = array();
	private $level = 0;
	private $output;

	public function addHeader($key, $value = null)
	{
		$this->headers[$key] = $value;
	}

	public function setHeader($header)
	{
		$this->headers = is_array($header) ? $header : array($header);
	}

	public function setCompression($level)
	{
		$this->level = $level;
	}

	public function getOutput()
	{
		return $this->output;
	}

	public function setOutput($output, $content_type = null)
	{
		$this->output = $output;

		if ($content_type) {
			$this->headers['Content-Type'] = $content_type;
		} elseif (empty($this->headers['Content-Type'])) {
			$this->headers['Content-Type'] = 'text/html; charset=UTF-8';
		}
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

		$this->addHeader('Content-Encoding', $encoding);

		return gzencode($data, (int)$level);
	}

	public function output()
	{
		if ($this->output) {
			if (preg_match("#^<\\?=[^?]+\\?>#", $this->output)) {
				$this->output = preg_replace("#^<\\?=[^?]+\\?>#", _l('<p>Please notify the web admin %s to enable short_open_tags for this server.</p>', option('config_email_error')), $this->output);
			}

			if ($this->level) {
				$output = $this->compress($this->output, $this->level);
			} else {
				$output = $this->output;
			}

			if (!headers_sent()) {
				foreach ($this->headers as $key => $value) {
					if ($value) {
						if (is_string($key)) {
							header($key . ': ' . $value, true);
						} else {
							header($value, true);
						}
					} else {
						header($key, true);
					}
				}
			}

			echo $output;
		}
	}
}
