<?php
class Mail extends Library
{
	private $handle;

	protected $to;
	protected $cc;
	protected $bcc;
	protected $from;
	protected $sender;
	protected $subject;
	protected $text;
	protected $html;
	protected $attachments;

	public $protocol;
	public $parameter;
	public $hostname;
	public $username;
	public $password;
	public $port;
	public $timeout;

	public $newline;
	public $crlf;
	public $verp;

	private $logging;
	private $log_entry = '';

	public function __construct($registry)
	{
		parent::__construct($registry);

		$this->protocol  = $this->config->get('config_mail_protocol');
		$this->parameter = $this->config->get('config_mail_parameter');
		$this->hostname  = $this->config->get('config_smtp_host');
		$this->username  = $this->config->get('config_smtp_username');
		$this->password  = $this->config->get('config_smtp_password');
		$this->port      = $this->config->get('config_smtp_port');
		$this->timeout   = $this->config->get('config_smtp_timeout');

		$this->logging = $this->config->get('config_mail_logging');

		if ($this->logging) {
			$this->registry->set('mail_log', new Log(DIR_LOGS . 'mail_log.txt'), $this->config->get('config_store_id'));
		}

		$this->init();
	}

	public function init($data = array())
	{
		//Defaults
		$data += array(
			'handle'      => null,
			'to'          => null,
			'cc'          => null,
			'bcc'         => null,
			'from'        => null,
			'sender'      => null,
			'subject'     => null,
			'text'        => null,
			'html'        => null,
			'attachments' => array(),
			'newline'     => "\n",
			'crlf'        => "\r\n",
			'verp'        => false,
		);

		foreach ($data as $key => $value) {
			$this->$key = $value;
		}
	}

	/**
	 * @param mixed $to - Can be a single email string, an array of emails or comma separated string of emails
	 */

	public function setTo($to)
	{
		if (is_array($to)) {
			array_walk($to, 'trim');
			$this->to = implode(',', $to);
		} else {
			$this->to = trim($to);
		}
	}

	/**
	 * @param mixed $to - Can be a single email string, an array of emails or comma separated string of emails
	 */

	public function setCc($to)
	{
		if (is_array($to)) {
			array_walk($to, 'trim');
			$this->cc = implode(',', $to);
		} else {
			$this->cc = trim($to);
		}
	}

	/**
	 * @param mixed $to - Can be a single email string, an array of emails or comma separated string of emails
	 */

	public function setBcc($to)
	{
		if (is_array($to)) {
			array_walk($to, 'trim');
			$this->bcc = implode(',', $to);
		} else {
			$this->bcc = trim($to);
		}
	}

	/**
	 * @param $from - The email address to be sent from
	 */
	public function setFrom($from)
	{
		$this->from = trim($from);
	}

	/**
	 * @param $sender - The name displayed for who the email was sent from
	 */
	public function setSender($sender)
	{
		$this->sender = trim($sender);
	}

	public function setSubject($subject)
	{
		$this->subject = trim($subject);
	}

	public function setText($text)
	{
		$this->text = $text;
	}

	public function setHtml($html)
	{
		$this->html = $html;
	}

	public function addAttachment($filename)
	{
		if (is_array($filename)) {
			$this->attachments = array_merge($this->attachments, $filename);
		} else {
			$this->attachments[] = $filename;
		}
	}

	public function sendTemplate($controller)
	{
		$args = func_get_args();
		array_shift($args);

		$action = new Action($this->registry, $controller, $args, 'catalog/controller/mail');

		//Set the Template to the Front End
		$action->getController()->template->setRootDirectory(SITE_DIR . 'catalog/view/theme/');

		if (!$action->execute()) {
			if ($controller === 'error') {
				trigger_error(_l("There was a problem while sending an email and the Error email template is missing!"));
				exit;
			}

			$this->sendTemplate('error', _l("Failed to call Mail Controller: %s!", $action->getClass()) . get_caller(0, 2));
		}
	}

	public function setData($data)
	{
		if ($data) {
			if (isset($data['sender'])) {
				$this->setSender($data['sender']);
			}

			if (isset($data['from'])) {
				$this->setFrom($data['from']);
			}

			if (isset($data['to'])) {
				$this->setTo($data['to']);
			}

			if (isset($data['cc'])) {
				$this->setCc($data['cc']);
			}

			if (isset($data['bcc'])) {
				$this->setBcc($data['bcc']);
			}

			if (isset($data['subject'])) {
				$this->setSubject($data['subject']);
			}

			if (!empty($data['html'])) {
				$this->setHtml($data['html']);
			} elseif (!empty($data['text'])) {
				$this->setText($data['text']);
			}

			if (!empty($data['attachment'])) {
				if (!is_array($data['attachment'])) {
					$data['attachment'] = array($data['attachment']);
				}

				foreach ($data['attachment'] as $attachment) {
					$this->addAttachment($attachment);
				}
			}
		}
	}

	public function send()
	{
		$errors = '';

		if (!$this->to) {
			$errors .= _l('E-Mail To required!');
		}

		if (!$this->from) {
			$errors .= _l('E-Mail From required!');
		}

		if (!$this->sender) {
			$this->sender = $this->from;
		}

		if (!$this->subject) {
			$this->subject = "(No Subject)";
		}

		if ((!$this->text) && (!$this->html)) {
			$this->text = ' ';
		}

		if ($errors) {
			$cc  = $this->cc ? "(CC: $this->cc)" : '';
			$bcc = $this->bcc ? "(BCC: $this->bcc)" : '';
			$msg = "There was a problem while sending an email to $this->to $cc $bcc<br />\r\n<br />\r\nThe Errors were as follows below: <br />\r\n<br />\r\n$errors";

			$this->trigger_error($msg);

			if (isset($this->config) && $this->config->get('config_email_error')) {
				$this->to      = $this->config->get('config_email_error');
				$this->cc      = '';
				$this->bcc     = '';
				$this->subject = "There was a problem sending out the email!";
				$this->text    = $msg;
			} else {
				$this->trigger_error("Please set the Error Email Address under settings!");
				return false;
			}
		}

		$boundary = '----=_NextPart_' . uniqid('np');

		$header = '';

		$header .= 'MIME-Version: 1.0' . $this->crlf;

		if ($this->protocol !== 'mail') {
			$header .= 'To: ' . $this->to . $this->crlf;
			$header .= 'Subject: ' . $this->subject . $this->crlf;
		}

		if ($this->cc) {
			$header .= 'Cc: ' . $this->cc . $this->crlf;
		}

		if ($this->bcc) {
			$header .= 'Bcc: ' . $this->bcc . $this->crlf;
		}

		$header .= 'Date: ' . date("D, d M Y H:i:s O") . $this->crlf;
		$header .= 'From: ' . '=?UTF-8?B?' . base64_encode($this->sender) . '?=' . '<' . $this->from . '>' . $this->crlf;
		$header .= 'Reply-To: ' . $this->sender . '<' . $this->from . '>' . $this->crlf;
		$header .= 'Return-Path: ' . $this->from . $this->crlf;
		$header .= 'X-Mailer: PHP/' . phpversion() . $this->crlf;
		$header .= 'Content-Type: multipart/related; boundary="' . $boundary . '"' . $this->crlf . $this->crlf;

		if (!$this->html) {
			$message = '--' . $boundary . $this->newline;
			$message .= 'Content-Type: text/plain; charset="utf-8"' . $this->newline;
			$message .= 'Content-Transfer-Encoding: 8bit' . $this->newline . $this->newline;
			$message .= $this->text . $this->newline;
		} else {
			$message = '--' . $boundary . $this->newline;
			$message .= 'Content-Type: multipart/alternative; boundary="' . $boundary . '_alt"' . $this->newline . $this->newline;
			$message .= '--' . $boundary . '_alt' . $this->newline;
			$message .= 'Content-Type: text/plain; charset="utf-8"' . $this->newline;
			$message .= 'Content-Transfer-Encoding: 8bit' . $this->newline . $this->newline;

			if ($this->text) {
				$message .= $this->text . $this->newline;
			} else {
				$message .= _l('This is a HTML email and your email client software does not support HTML email!') . $this->newline;
			}

			$message .= '--' . $boundary . '_alt' . $this->newline;
			$message .= 'Content-Type: text/html; charset="utf-8"' . $this->newline;
			$message .= 'Content-Transfer-Encoding: 8bit' . $this->newline . $this->newline;
			$message .= $this->html . $this->newline;
			$message .= '--' . $boundary . '_alt--' . $this->newline;
		}

		foreach ($this->attachments as $attachment) {
			if (file_exists($attachment)) {
				$contents = file_get_contents($attachment);

				$message .= '--' . $boundary . $this->newline;
				$message .= 'Content-Type: application/octet-stream; name="' . basename($attachment) . '"' . $this->newline;
				$message .= 'Content-Transfer-Encoding: base64' . $this->newline;
				$message .= 'Content-Disposition: attachment; filename="' . basename($attachment) . '"' . $this->newline;
				$message .= 'Content-ID: <' . basename(urlencode($attachment)) . '>' . $this->newline;
				$message .= 'X-Attachment-Id: ' . basename(urlencode($attachment)) . $this->newline . $this->newline;
				$message .= chunk_split(base64_encode($contents));
			}
		}

		$message .= '--' . $boundary . '--' . $this->newline;

		$this->log(__METHOD__ . "(): to ($this->to), from ($this->from)");

		if ($this->protocol === 'smtp') {
			$this->log("Sending via SMTP:");

			if ($this->sendSmtp($header, $message)) {
				$this->log("SMTP Mail Sent!", true);
				return true;
			}

			$this->log("SMTP Failed", true);
		}

		//Send via standard PHP
		$this->log("Sending via PHP mail():");

		ini_set('sendmail_from', $this->from);

		if (!$this->parameter) {
			$this->parameter = null;
		}

		if (is_array($this->to)) {
			$this->to = implode(', ', $this->to);
		}

		if (!mail($this->to, '=?UTF-8?B?' . base64_encode($this->subject) . '?=', $message, $header, $this->parameter)) {
			$this->trigger_error("There was an error while sending the email message to: $this->to -- from: $this->from");

			$this->log("PHP mail() Failed", true);

			return false;
		}

		$this->log("PHP mail() Sent!", true);

		return true;
	}

	private function sendSmtp($header, $message)
	{
		if (!$this->hostname) {
			$this->hostname = '127.0.0.1';
		}

		if (!$this->port) {
			$this->port = 25;
		}

		if ((int)$this->timeout <= 0) {
			$this->timeout = 5;
		}

		$this->handle = fsockopen($this->hostname, $this->port, $errno, $errstr, $this->timeout);

		if (!$this->handle) {
			$this->trigger_error('' . $errstr . ' (' . $errno . ')');
			return false;
		}

		stream_set_timeout($this->handle, $this->timeout, 0);

		//Clear Socket
		$this->getReply();

		if (substr($this->hostname, 0, 3) === 'tls') {
			if (!$this->talk("STARTTLS", 220)) {
				$this->trigger_error('STARTTLS not accepted from server!');
				return false;
			}
		}

		if (!empty($this->username) && !empty($this->password)) {

			if (!$this->talk('EHLO ' . getenv('SERVER_NAME'), 250)) {
				$this->trigger_error('EHLO not accepted from server!');
				return false;
			}

			if (!$this->talk('AUTH LOGIN', 334)) {
				$this->trigger_error('AUTH LOGIN not accepted from server!');
				return false;
			}

			if (!$this->talk(base64_encode($this->username), 334)) {
				$this->trigger_error('Username not accepted from server!');
				return false;
			}

			if (!$this->talk(base64_encode($this->password), 235)) {
				$this->trigger_error('Password not accepted from server!');
				return false;
			}
		} else {
			if (!$this->talk('HELO ' . getenv('SERVER_NAME'), 250)) {
				$this->trigger_error('HELO not accepted from server!');
				return false;
			}
		}

		if (!$this->talk('MAIL FROM: <' . $this->from . '>' . ($this->verp ? 'XVERP' : ''), 250)) {
			$this->trigger_error('MAIL FROM not accepted from server!');
			return false;
		}

		$reply_codes = array(
			250,
			251
		);

		if (!is_array($this->to)) {
			$this->to = explode(',', $this->to);
		}

		foreach ($this->to as $key => $recipient) {
			if (!$this->talk('RCPT TO: <' . $recipient . '>', $reply_codes)) {
				$this->message->add('warning', _l("%s is an invalid recipient. Mail was not delivered to this address.", $recipient));
				unset($this->to[$key]);
			}
		}

		if (!$this->talk('DATA', 354)) {
			$this->trigger_error('DATA not accepted from server!');
			return false;
		}

		// RFC 821 - do not send more than 1000 characters including the CRLF
		$message = str_replace("\r\n", "\n", $header . $message);
		$message = str_replace("\r", "\n", $message);

		$lines = explode("\n", $message);

		foreach ($lines as $line) {
			$parts = str_split($line, 998);

			foreach ($parts as $part) {
				$this->talk($part);
			}
		}

		if (!$this->talk('.', 250)) {
			$this->trigger_error('DATA not accepted from server!');
			return false;
		}

		if (!$this->talk('QUIT', 221)) {
			$this->trigger_error('QUIT not accepted from server!');
			return false;
		}

		fclose($this->handle);

		return true;
	}

	private function talk($msg, $code = null)
	{
		fputs($this->handle, $msg . $this->crlf);

		if ($code) {
			$reply      = $this->getReply();
			$reply_code = (int)substr($reply, 0, 3);

			if (is_array($code)) {
				return in_array($reply_code, $code);
			}

			return (int)$code === (int)$reply_code;
		}

		return true;
	}

	private function getReply()
	{
		$reply = '';

		while ($line = fgets($this->handle, 515)) {
			$reply .= $line;

			if (substr($line, 3, 1) == ' ') {
				break;
			}
		}

		return $reply;
	}

	private function trigger_error($msg)
	{
		$this->log("MAIL ERROR: " . $msg, true);

		//Hide Mail errors when ajax pages are requested
		if ($this->request->isAjax() && $this->config->get('config_error_display')) {
			$this->config->set('config_error_display', false);
			trigger_error($msg);
			$this->config->set('config_error_display', true);
		} else {
			trigger_error($msg);
		}

		if ($this->config->get('config_error_display')) {
			$view_mail_errors = $this->url->admin('mail/error');
			$this->message->system('warning', "There was an error while sending an email <a href=\"$view_mail_errors\">(review all mail errors)</a>: " . $msg);
		}

		$mail_fail = array(
			'mail'       => $this,
			'error'      => $msg,
			'to'         => $this->to,
			'cc'         => $this->cc,
			'bcc'        => $this->bcc,
			'from'       => $this->from,
			'sender'     => $this->sender,
			'subject'    => $this->subject,
			'html'       => $this->html,
			'text'       => $this->text,
			'attachment' => $this->attachments,
			'store_id'   => $this->config->get('config_store_id'),
			'time'       => _time(),
		);

		$temp = $this->registry;
		unset($this->registry);
		$mail_fail      = serialize($mail_fail);
		$this->registry = $temp;

		$mail_fail = $this->escape($mail_fail);

		$this->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `group` = 'mail_fail', `key` = 'mail_fail', value = '$mail_fail', serialized = '1', auto_load = '0'");
	}

	private function log($msg, $flush = false)
	{
		if (!$this->logging) {
			return;
		}

		$this->log_entry .= $msg . "\r\n";

		if ($flush) {
			$this->mail_log->write($this->log_entry);
		}
	}
}
