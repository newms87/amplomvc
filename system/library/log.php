<?php
class Log
{
	private $filename;
	private $store_name;

	public function __construct($filename, $store_name = 'Default')
	{
		$this->filename = $filename;
		$this->store_name = $store_name;
	}

	public function write($message)
	{
		$file = DIR_LOGS . $this->filename;

		$handle = fopen($file, 'a+');

		$log = date('Y-m-d G:i:s');
		$log .= "\t" . $_SERVER['REMOTE_ADDR'];
		$log .= "\t" . preg_replace("/\?.*/", "", $_SERVER['REQUEST_URI']);
		$log .= "\t" . $_SERVER['QUERY_STRING'];
		$log .= "\t" . $this->store_name;
		$log .= "\t" . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
		$log .= "\t" . str_replace(array(
		                                "\r\n",
		                                "\r",
		                                "\n"
		                           ), '<br />', $message);

		fwrite($handle, $log . "\r\n");

		fclose($handle);
	}
}