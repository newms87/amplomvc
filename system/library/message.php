<?php
class Message extends Library
{
	function __construct($registry)
	{
		parent::__construct($registry);

		if (!isset($this->session->data['messages'])) {
			$this->session->set('messages', array());
		}
	}

	public function add($type, $message)
	{
		if (is_string($message)) {
			$this->session->data['messages'][$type][] = $$message;
		} elseif (is_array($message)) {
			array_walk_recursive($message, function ($value, $key) use($type) {
				$_SESSION['messages'][$type][] = $value;
			});
		}
	}

	//TODO: Need to add system messages to backend!
	public function system($type, $message)
	{
		if (is_string($message)) {
			$this->session->data['system_messages'][$type][] = $this->language->get($message);
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
			return isset($this->session->data['messages']['error'][$type]) || isset($this->session->data['messages']['warning'][$type]);
		}

		return isset($this->session->data['messages']['error']) || isset($this->session->data['messages']['warning']);
	}

	public function peek($type = null)
	{
		if ($type) {
			if (isset($this->session->data['messages'][$type])) {
				return $this->session->data['messages'][$type];
			} else {
				return array();
			}
		}

		return $this->session->data['messages'];
	}

	public function fetch($type = '')
	{
		if (empty($this->session->data['messages'])) {
			return array();
		}

		if ($type) {
			if (isset($this->session->data['messages'][$type])) {
				$msgs = $this->session->data['messages'][$type];

				unset($this->session->data['messages'][$type]);

				return $msgs;
			} else {
				return array();
			}
		}

		$msgs = $this->session->data['messages'];

		unset($this->session->data['messages']);

		return $msgs;
	}

	public function toJSON($type = '')
	{
		return json_encode($this->fetch($type));
	}
}
