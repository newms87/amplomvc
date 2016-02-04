<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

class Message extends Library
{
	protected $messages = array();

	function __construct($session = true)
	{
		parent::__construct();

		if ($session) {
			if (!isset($_SESSION['message'])) {
				$_SESSION['message'] = array();
			}

			$this->messages = & $_SESSION['message'];
		}
	}

	public function set($messages)
	{
		$this->messages = $messages;
	}

	public function add($type, $message, $key = null)
	{
		if ($type === 'data') {
			$this->messages['data'] = $message;
		} elseif (is_array($message)) {
			foreach ($message as $key => $m) {
				$this->add($type, $m, $key);
			}
		} else {
			if ($key) {
				$this->messages[$type][$key] = $message;
			} else {
				$this->messages[$type][] = $message;
			}
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
			return !empty($this->messages);
		}

		foreach ($types as $type) {
			if (is_array($type)) {
				foreach ($type as $t) {
					if ($this->has($t)) {
						return true;
					}
				}
			} else {
				if (!empty($this->messages[$type]) || !empty($this->messages[$type])) {
					return true;
				}
			}
		}

		return false;
	}

	public function get($type = null)
	{
		if ($type) {
			if (isset($this->messages[$type])) {
				return $this->messages[$type];
			} else {
				return array();
			}
		}

		return $this->messages;
	}

	public function fetch($type = null)
	{
		if (empty($this->messages)) {
			return array();
		}

		if ($type) {
			if (isset($this->messages[$type])) {
				$msgs = $this->messages[$type];

				unset($this->messages[$type]);

				return $msgs;
			} else {
				return array();
			}
		}

		$msgs = $this->messages;
		$this->messages = array();

		return $msgs;
	}

	public function clear($type = null)
	{
		if ($type) {
			unset($this->messages[$type]);
		} else {
			$this->messages = array();
		}

		return true;
	}

	public function render($type = null, $close = true, $style = null)
	{
		if ($style === null) {
			$style = IS_ADMIN ? option('admin_message_style', 'stacked') : option('site_message_style', 'inline');
		}

		$messages = $this->fetch($type);

		$html = '';

		foreach ($messages as $t => $msgs) {
			//Data type messages should not be displayed.
			if ($t === 'data') {
				continue;
			}

			$html .= "<div class =\"messages $style " . ($type ? $type : $t) . "\">";

			if (!is_array($msgs)) {
				$msgs = array($msgs);
			}

			foreach ($msgs as $msg) {
				if (!empty($msg)) {
					$html .= "<div class=\"message\">$msg</div>";
				}
			}

			if ($close && option('config_allow_close_message', true)) {
				$html .= "<span class =\"close\" onclick=\"$(this).closest('.messages').find('.message').hide_msg()\"></span>";
			}

			$html .= "</div>";
		}

		return $html;
	}
}
