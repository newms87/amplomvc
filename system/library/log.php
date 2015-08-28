<?php

class Log extends Library
{
	private $file;
	private $name;

	static $cols = array(
		'date',
		'ip',
		'uri',
		'query',
		'agent',
		'message',
	);

	public function __construct($name)
	{
		parent::__construct();
		$this->name = $name;
		$this->file = DIR_LOGS . (defined('SITE_PREFIX') ? SITE_PREFIX : DB_PREFIX) . '/' . $name . '.txt';

		if (!_is_writable(dirname($this->file))) {
			trigger_error(_l("Log file directory was not writable: %s", $this->file));
			$this->file = '';
		} elseif (!is_file($this->file)) {
			touch($this->file);
			chmod($this->file, 0755);
		}
	}

	public function write($message)
	{
		$fields = array(
			'name'       => $this->name,
			'user_id'    => IS_ADMIN ? _session('user_id') : _session('customer_id'),
			'date'       => date('Y-m-d G:i:s'),
			'ip'         => $_SERVER['REMOTE_ADDR'],
			'uri'        => preg_replace("/\\?.*/", "", $_SERVER['REQUEST_URI']),
			'query'      => $_SERVER['QUERY_STRING'],
			'user_agent' => (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''),
			'message'    => str_replace("\n", "__nl__", str_replace("\r", '', $message)),
		);

		if ($this->file) {
			$handle = fopen($this->file, 'a+');

			fwrite($handle, implode("\t", $fields) . "\r\n");

			fclose($handle);
		}

		$this->insert('log', $fields);
	}
}
