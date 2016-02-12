<?php

interface SessionHandlerInterface
{
	public function close();

	public function destroy($session_id);

	public function gc($maxlifetime);

	public function open($save_path, $name);

	public function read($session_id);

	public function write($session_id, $session_data);
}
