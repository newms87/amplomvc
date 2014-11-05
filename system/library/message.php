<?php

class Message extends Library
{
	function __construct()
	{
		parent::__construct();

		if (!isset($_SESSION['message'])) {
			$_SESSION['message'] = array();
		}
	}

	public function add($type, $message)
	{
		if ($type === 'data') {
			$_SESSION['message']['data'] = $message;
		} elseif (is_array($message)) {
			array_walk_recursive($message, function ($value, $key) use ($type) {
				if (is_string($key)) {
					$_SESSION['message'][$type][$key] = $value;
				} else {
					$_SESSION['message'][$type][] = $value;
				}
			});
		} else {
			$_SESSION['message'][$type][] = $message;
		}
	}

	//TODO: Need to add system messages to backend!
	public function system($type, $message)
	{
		if (is_array($message)) {
			array_walk_recursive($message, function ($value, $key, $data) {
				$_SESSION['system_messages'][$data[1]][] = $data[0]->language->get($value);
			}, array(
				$this,
				$type
			));
		} else {
			$_SESSION['system_messages'][$type][] = $message;
		}
	}

	/**
	 * @param $type - message type to check for, pass as many $type parameters as needed
	 *
	 * @return bool
	 */
	public function has()
	{
		$types = func_get_args();

		if (empty($types)) {
			return !empty($_SESSION['message']);
		}

		foreach ($types as $type) {
			if (is_array($type)) {
				foreach ($type as $t) {
					if ($this->has($t)) {
						return true;
					}
				}
			} else {
				if (!empty($_SESSION['message'][$type]) || !empty($_SESSION['message'][$type])) {
					return true;
				}
			}
		}

		return false;
	}

	public function get($type = null)
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
		if (empty($_SESSION['message'])) {
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

		unset($_SESSION['message']);

		return $msgs;
	}

	public function render($type = null, $close = true)
	{
		$messages = $this->fetch($type);

		$html = '';

		foreach ($messages as $t => $msgs) {
			$html .= "<div class =\"messages " . ($type ? $type : $t) . "\">";

			if (!is_array($msgs)) {
				$msgs = array($msgs);
			}

			foreach ($msgs as $msg) {
				if (!empty($msg)) {
					$html .= "<div class=\"message\">$msg</div>";
				}
			}

			if ($close && option('config_allow_close_message')) {
				$html .= "<span class =\"close\" onclick=\"$(this).closest('.messages').remove()\"></span>";
			}

			$html .= "</div>";
		}

		return $html;
	}
}
