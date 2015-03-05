<?php

class Response extends Library
{
	private $headers = array();
	private $output;

	public function addHeader($key, $value = null)
	{
		$this->headers[$key] = $value;
	}

	public function setHeader($header)
	{
		$this->headers = is_array($header) ? $header : array($header);
	}

	public function getOutput()
	{
		return $this->output;
	}

	public function setOutput($output, $headers = null)
	{
		$this->output = $output;

		if ($headers) {
			if (is_array($headers)) {
				$this->headers = $headers + $this->headers;
			} else {
				$this->headers['Content-Type'] = $headers;
			}
		} elseif (empty($this->headers['Content-Type'])) {
			$this->headers['Content-Type'] = 'text/html; charset=UTF-8';
		}
	}

	private function compress($data, $level = 9)
	{
		if (headers_sent() || connection_status() || !extension_loaded('zlib') || ini_get('zlib.output_compression')) {
			return $data;
		}

		if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
			if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
				$encoding = 'x-gzip';
			} elseif (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
				$encoding = 'gzip';
			}
		}

		if (empty($encoding)) {
			return $data;
		}

		$this->addHeader('Content-Encoding', $encoding);

		return gzencode($data, (int)$level);
	}

	public function output()
	{
		if ($this->output) {
			if (!ini_get('short_open_tag') && (!defined('AMPLO_REWRITE_SHORT_TAGS') || !AMPLO_REWRITE_SHORT_TAGS) && preg_match("#<\\?=[^?]+\\?>#", $this->output)) {
				echo _l('<p>Please notify the web admin %s to enable short_open_tag (eg: add "short_open_tag = on" in the php.ini file) on this server. Alternatively, adding "define(\'AMPLO_REWRITE_SHORT_TAGS\', true);" to the config.php file and removing all cache files in "system/cache/templates/" should solve the problem forcing Amplo MVC to rewrite "&lt;?=" as "&lt;?php echo".</p>', option('site_email_error'));
				return;
			}

			if ($level = option('config_compression')) {
				$output = $this->compress($this->output, $level);
			} else {
				$output = $this->output;
			}

			if (!headers_sent($file, $line)) {
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
			} elseif (defined('AMPLO_HEADERS_DEBUG') && AMPLO_HEADERS_DEBUG) {
				echo "\n\n<BR><BR>HEADERS STARTED at $file on line $line<BR><BR>\n\n";
			}

			echo $output;
		}
	}
}
