<?php
class Message extends Library
{
	function __construct($registry)
	{
		parent::__construct($registry);

		if (!$this->session->has('messages')) {
			$this->session->set('messages', array());
		}
	}

	public function add($type, $message)
	{
		if (is_string($message)) {
			$_SESSION['messages'][$type][] = $message;
		} elseif (is_array($message)) {
			array_walk_recursive($message, function ($value, $key) use ($type) {
				$_SESSION['messages'][$type][] = $value;
			});
		}
	}

	//TODO: Need to add system messages to backend!
	public function system($type, $message)
	{
		if (is_string($message)) {
			$_SESSION['system_messages'][$type][] = $message;
		} elseif (is_array($message)) {
			array_walk_recursive($message, function ($value, $key, $data) {
				$_SESSION['system_messages'][$data[1]][] = $data[0]->language->get($value);
			}, array(
				$this,
				$type
			));
		}
	}

	public function hasError($type = null)
	{
		if ($type) {
			return isset($_SESSION['message']['error'][$type]) || isset($_SESSION['message']['warning'][$type]);
		}

		return isset($_SESSION['message']['error']) || isset($_SESSION['message']['warning']);
	}

	public function peek($type = null)
	{
		if ($type) {
			if (isset($_SESSION['message'][$type])) {
				return $_SESSION['message'][$type];
			} else {
				return array();
			}
		}

		return $_SESSION['message'];
	}

	public function fetch($type = '')
	{
		if (!$this->session->has('message')) {
			return array();
		}

		if ($type) {
			if (isset($_SESSION['message'][$type])) {
				$msgs = $_SESSION['message'][$type];

				unset($_SESSION['message'][$type]);

				return $msgs;
			} else {
				return array();
			}
		}

		$msgs = $_SESSION['message'];

		$this->session->delete('messages');

		return $msgs;
	}

	public function toJSON($type = '')
	{
		return json_encode($this->fetch($type));
	}
}
