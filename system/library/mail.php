<?php
class Mail extends Library
{
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

		$this->init();
	}

	public function init()
	{
		$this->to          = null;
		$this->cc          = null;
		$this->bcc         = null;
		$this->from        = null;
		$this->sender      = null;
		$this->subject     = null;
		$this->text        = null;
		$this->html        = null;
		$this->attachments = array();
		$this->newline     = "\n";
		$this->crlf        = "\r\n";
		$this->verp        = false;
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

	public function callController($controller)
	{
		$args = func_get_args();
		array_shift($args);

		$action = new Action($this->registry, $controller, $args, 'catalog/controller/mail');

		//Set the language and Template to the Front End
		$this->language->setRoot(SITE_DIR . 'catalog/language/');
		$action->getController()->template->setRootDirectory(SITE_DIR . 'catalog/view/theme/');

		if (!$action->execute()) {
			$this->mail->callController('error', "Failed to call Mail Controller: " . $action->getClass() . "! " . get_caller(0, 2));
		}

		$this->language->setRoot(DIR_LANGUAGE);
	}

	public function send($data = null)
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

			if (isset($data['attachment'])) {
				if (!empty($_FILES['attachment']) && empty($_FILES['attachment']['error'])) {
					$files = $_FILES['attachment'];

					for ($i = 0; $i < count($files['name']); $i++) {
						$file_name = dirname($files['tmp_name'][$i]) . '/' . $files['name'][$i];
						rename($files['tmp_name'][$i], $file_name);
						$this->addAttachment($file_name);
					}
				}
			}
		}

		$errors = '';
		if (!$this->to) {
			$msg = 'E-Mail To required!';
			$this->trigger_error($msg);
			$errors .= $msg;
		}

		if (!$this->from) {
			$msg = 'E-Mail From required!';
			$this->trigger_error($msg);
			$errors .= $msg;
		}

		if (!$this->sender) {
			$this->sender = $this->from;
		}

		if (!$this->subject) {
			$msg = 'E-Mail subject required!';
			$this->trigger_error($msg);
			$errors .= $msg;
		}

		if ((!$this->text) && (!$this->html)) {
			$msg = 'E-Mail message required!';
			$this->trigger_error($msg);
			$errors .= $msg;
		}

		if ($errors) {
			$cc  = $this->cc ? "(CC: $this->cc)" : '';
			$bcc = $this->bcc ? "(BCC: $this->bcc)" : '';
			$msg = "There was a problem while sending an email to $this->to $cc $bcc<br />\r\n<br />\r\nThe Errors were as follows below: <br />\r\n<br />\r\n$errors";

			$msg .= get_caller();

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


		$boundary = '----=_NextPart_' . md5(time());

		$header = '';

		$header .= 'MIME-Version: 1.0' . $this->crlf;

		if ($this->protocol != 'mail') {
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
				$message .= 'This is a HTML email and your email client software does not support HTML email!' . $this->newline;
			}

			$message .= '--' . $boundary . '_alt' . $this->newline;
			$message .= 'Content-Type: text/html; charset="utf-8"' . $this->newline;
			$message .= 'Content-Transfer-Encoding: 8bit' . $this->newline . $this->newline;
			$message .= $this->html . $this->newline;
			$message .= '--' . $boundary . '_alt--' . $this->newline;
		}

		foreach ($this->attachments as $attachment) {
			if (file_exists($attachment)) {
				$handle = fopen($attachment, 'r');

				$content = fread($handle, filesize($attachment));

				fclose($handle);

				$message .= '--' . $boundary . $this->newline;
				$message .= 'Content-Type: application/octet-stream; name="' . basename($attachment) . '"' . $this->newline;
				$message .= 'Content-Transfer-Encoding: base64' . $this->newline;
				$message .= 'Content-Disposition: attachment; filename="' . basename($attachment) . '"' . $this->newline;
				$message .= 'Content-ID: <' . basename(urlencode($attachment)) . '>' . $this->newline;
				$message .= 'X-Attachment-Id: ' . basename(urlencode($attachment)) . $this->newline . $this->newline;
				$message .= chunk_split(base64_encode($content));
			}
		}

		$message .= '--' . $boundary . '--' . $this->newline;

		if ($this->protocol == 'mail') {
			ini_set('sendmail_from', $this->from);

			if (!$this->parameter) {
				$this->parameter = null;
			}

			if (!mail($this->to, '=?UTF-8?B?' . base64_encode($this->subject) . '?=', $message, $header, $this->parameter)) {
				$this->trigger_error("There was an error while sending the email message to: $this->to  -- from: $this->from");
			}
		} elseif ($this->protocol == 'smtp') {
			$handle = fsockopen($this->hostname, $this->port, $errno, $errstr, $this->timeout);

			if (!$handle) {
				$this->trigger_error('' . $errstr . ' (' . $errno . ')');
				return false;
			}

			if (substr(PHP_OS, 0, 3) != 'WIN') {
				socket_set_timeout($handle, $this->timeout, 0);
			}

			while ($line = fgets($handle, 515)) {
				if (substr($line, 3, 1) == ' ') {
					break;
				}
			}

			if (substr($this->hostname, 0, 3) == 'tls') {
				fputs($handle, 'STARTTLS' . $this->crlf);

				while ($line = fgets($handle, 515)) {
					$reply .= $line;

					if (substr($line, 3, 1) == ' ') {
						break;
					}
				}

				if (substr($reply, 0, 3) != 220) {
					$this->trigger_error('STARTTLS not accepted from server!');
					return false;
				}
			}

			if (!empty($this->username) && !empty($this->password)) {
				fputs($handle, 'EHLO ' . getenv('SERVER_NAME') . $this->crlf);

				$reply = '';

				while ($line = fgets($handle, 515)) {
					$reply .= $line;

					if (substr($line, 3, 1) == ' ') {
						break;
					}
				}

				if (substr($reply, 0, 3) != 250) {
					$this->trigger_error('EHLO not accepted from server!');
					return false;
				}

				fputs($handle, 'AUTH LOGIN' . $this->crlf);

				$reply = '';

				while ($line = fgets($handle, 515)) {
					$reply .= $line;

					if (substr($line, 3, 1) == ' ') {
						break;
					}
				}

				if (substr($reply, 0, 3) != 334) {
					$this->trigger_error('AUTH LOGIN not accepted from server!');
					return false;
				}

				fputs($handle, base64_encode($this->username) . $this->crlf);

				$reply = '';

				while ($line = fgets($handle, 515)) {
					$reply .= $line;

					if (substr($line, 3, 1) == ' ') {
						break;
					}
				}

				if (substr($reply, 0, 3) != 334) {
					$this->trigger_error('Username not accepted from server!');
					return false;
				}

				fputs($handle, base64_encode($this->password) . $this->crlf);

				$reply = '';

				while ($line = fgets($handle, 515)) {
					$reply .= $line;

					if (substr($line, 3, 1) == ' ') {
						break;
					}
				}

				if (substr($reply, 0, 3) != 235) {
					$this->trigger_error('Password not accepted from server!');
					return false;
				}
			} else {
				fputs($handle, 'HELO ' . getenv('SERVER_NAME') . $this->crlf);

				$reply = '';

				while ($line = fgets($handle, 515)) {
					$reply .= $line;

					if (substr($line, 3, 1) == ' ') {
						break;
					}
				}

				if (substr($reply, 0, 3) != 250) {
					$this->trigger_error('HELO not accepted from server!');
					return false;
				}
			}

			if ($this->verp) {
				fputs($handle, 'MAIL FROM: <' . $this->from . '>XVERP' . $this->crlf);
			} else {
				fputs($handle, 'MAIL FROM: <' . $this->from . '>' . $this->crlf);
			}

			$reply = '';

			while ($line = fgets($handle, 515)) {
				$reply .= $line;

				if (substr($line, 3, 1) == ' ') {
					break;
				}
			}

			if (substr($reply, 0, 3) != 250) {
				$this->trigger_error('MAIL FROM not accepted from server!');
				return false;
			}

			if (!is_array($this->to)) {
				fputs($handle, 'RCPT TO: <' . $this->to . '>' . $this->crlf);

				$reply = '';

				while ($line = fgets($handle, 515)) {
					$reply .= $line;

					if (substr($line, 3, 1) == ' ') {
						break;
					}
				}

				if ((substr($reply, 0, 3) != 250) && (substr($reply, 0, 3) != 251)) {
					$this->trigger_error('RCPT TO not accepted from server!');
					return false;
				}
			} else {
				foreach ($this->to as $recipient) {
					fputs($handle, 'RCPT TO: <' . $recipient . '>' . $this->crlf);

					$reply = '';

					while ($line = fgets($handle, 515)) {
						$reply .= $line;

						if (substr($line, 3, 1) == ' ') {
							break;
						}
					}

					if ((substr($reply, 0, 3) != 250) && (substr($reply, 0, 3) != 251)) {
						$this->trigger_error('RCPT TO not accepted from server!');
						return false;
					}
				}
			}

			fputs($handle, 'DATA' . $this->crlf);

			$reply = '';

			while ($line = fgets($handle, 515)) {
				$reply .= $line;

				if (substr($line, 3, 1) == ' ') {
					break;
				}
			}

			if (substr($reply, 0, 3) != 354) {
				$this->trigger_error('DATA not accepted from server!');
				return false;
			}

			// According to rfc 821 we should not send more than 1000 including the CRLF
			$message = str_replace("\r\n", "\n", $header . $message);
			$message = str_replace("\r", "\n", $message);

			$lines = explode("\n", $message);

			foreach ($lines as $line) {
				$results = str_split($line, 998);

				foreach ($results as $result) {
					if (substr(PHP_OS, 0, 3) != 'WIN') {
						fputs($handle, $result . $this->crlf);
					} else {
						fputs($handle, str_replace("\n", "\r\n", $result) . $this->crlf);
					}
				}
			}

			fputs($handle, '.' . $this->crlf);

			$reply = '';

			while ($line = fgets($handle, 515)) {
				$reply .= $line;

				if (substr($line, 3, 1) == ' ') {
					break;
				}
			}

			if (substr($reply, 0, 3) != 250) {
				$this->trigger_error('DATA not accepted from server!');
				return false;
			}

			fputs($handle, 'QUIT' . $this->crlf);

			$reply = '';

			while ($line = fgets($handle, 515)) {
				$reply .= $line;

				if (substr($line, 3, 1) == ' ') {
					break;
				}
			}

			if (substr($reply, 0, 3) != 221) {
				$this->trigger_error('QUIT not accepted from server!');
				return false;
			}

			fclose($handle);
		}

		return true;
	}

	private function trigger_error($msg)
	{
		//Hide Mail errors when ajax pages are requested
		if (!empty($_POST['async']) && $this->config->get('config_error_display')) {
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
			'time'       => time(),
		);

		$temp = $this->registry;
		unset($this->registry);
		$mail_fail      = serialize($mail_fail);
		$this->registry = $temp;

		$mail_fail = $this->db->escape($mail_fail);

		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `group` = 'mail_fail', `key` = 'mail_fail', value = '$mail_fail', serialized = '1', auto_load = '0'");
	}
}
