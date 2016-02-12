<?php

class AmploSessionHandler implements SessionHandlerInterface
{
	protected $save_path, $name;

	public function __construct()
	{
		ini_set('session.use_cookies', 'On');
		ini_set('session.use_trans_sid', 'Off');

		ini_set("session.cookie_domain", COOKIE_DOMAIN);
		session_set_cookie_params(0, '/', COOKIE_DOMAIN, false, false);
	}

	public function open($save_path, $name)
	{
		$this->save_path = $save_path . '/' . $name;
		$this->name      = $name;

		if (!is_dir($this->save_path)) {
			mkdir($this->save_path, 0777, true);
		}

		return true;
	}

	public function close()
	{
		return true;
	}

	public function read($id)
	{
		return (string)@file_get_contents("$this->save_path/sess_$id");
	}

	public function write($id, $data)
	{
		return file_put_contents("$this->save_path/sess_$id", $data) === false ? false : true;
	}

	public function destroy($id)
	{
		$file = "$this->save_path/sess_$id";

		if (file_exists($file)) {
			unlink($file);
		}

		return true;
	}

	public function gc($maxlifetime)
	{
		foreach (glob("$this->save_path/sess_*") as $file) {
			if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
				unlink($file);
			}
		}

		return true;
	}
}
