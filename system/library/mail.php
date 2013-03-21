<?php
class Mail {
   private $config;
   
	protected $to;
   protected $copy_to;
   protected $blind_copy_to;
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

   public function __construct($registry){
      $this->config = $registry->get('config');
      
      $this->protocol  = $this->config->get('config_mail_protocol');
      $this->parameter = $this->config->get('config_mail_parameter');
      $this->hostname  = $this->config->get('config_smtp_host');
      $this->username  = $this->config->get('config_smtp_username');
      $this->password  = $this->config->get('config_smtp_password');
      $this->port      = $this->config->get('config_smtp_port');
      $this->timeout   = $this->config->get('config_smtp_timeout');
      
      $this->init();
   }
   
   public function init(){
      $this->to = null;
      $this->copy_to = null;
      $this->blind_copy_to = null;
      $this->from = null;
      $this->sender = null;
      $this->subject = null;
      $this->text = null;
      $this->html = null;
      $this->attachments = array();
      $this->newline = "\n";
      $this->crlf = "\r\n";
      $this->verp = false;
   }
   
   /**
    * @param $to - Can be a single email, an array of emails or comma separated string of emails
    */
    
	public function setTo($to) {
	   if (is_array($to)) {
	      array_walk($to, 'trim');
         $this->to = implode(',', $to);
      } else {
         $this->to = trim($to);
      }
	}
   
   public function setCopyTo($to) {
      if (is_array($to)) {
         array_walk($to, 'trim');
         $this->copy_to = implode(',', $to);
      } else {
         $this->copy_to = trim($to);
      }
   }
   
   public function setBlindCopyTo($to) {
      if (is_array($to)) {
         array_walk($to, 'trim');
         $this->blind_copy_to = implode(',', $to);
      } else {
         $this->blind_copy_to = trim($to);
      }
   }
   
   /**
    * @param $from - The email address to be sent from
    */
	public function setFrom($from) {
		$this->from = trim($from);
	}
   
   /**
    * @param $sender - The name displayed for who the email was sent from
    */
	public function setSender($sender) {
		$this->sender = trim($sender);
	}

	public function setSubject($subject) {
		$this->subject = trim($subject);
	}

	public function setText($text) {
		$this->text = $text;
	}

	public function setHtml($html) {
		$this->html = $html;
	}

	public function addAttachment($filename) {
	   if(is_array($filename)){
         $this->attachments = array_merge($this->attachments,$filename);
      }
      else{
		   $this->attachments[] = $filename;
      }
	}

	public function send() {
	   $errors = '';
		if (!$this->to) {
		   $msg = 'Error: E-Mail to required!';
			trigger_error($msg);
         $errors .= $msg;
		}

		if (!$this->from) {
		   $msg = 'Error: E-Mail from required!';
			trigger_error($msg);
         $errors .= $msg;		
		}

		if (!$this->sender) {
		   $this->sender = $this->from;
		}

		if (!$this->subject) {
		   $msg = 'Error: E-Mail subject required!';
			trigger_error($msg);
			$errors .= $msg;
		}

		if ((!$this->text) && (!$this->html)) {
		   $msg = 'Error: E-Mail message required!';
			trigger_error($msg);
         $errors .= $msg;
		}
      
      if($errors){
      	$copy_to = $this->copy_to ? "(CC: $this->copy_to)":'';
         $blind_copy_to = $this->blind_copy_to ? "(BCC: $this->blind_copy_to)":'';
      	$msg = "There was a problem while sending an email to $this->to $copy_to $blind_copy_to<br />\r\n<br />\r\nThe Errors were as follows below: <br />\r\n<br />\r\n$errors";
         
         list($caller) = debug_backtrace(false);
         $msg .= " Called from $caller[file] on line $caller[line]";
         
      	trigger_error($msg);
			
      	if(isset($this->config) && $this->config->get('config_email_error')){
	         $this->to = $this->config->get('config_email_error');
	         $this->copy_to = '';
	         $this->blind_copy_to = '';
	         $this->subject = "There was a problem sending out the email!";
	         $this->text = $msg;
         }
			else{
				trigger_error("Please set the Error Email Address under settings!");
				return;
			}
      }
		

		$boundary = '----=_NextPart_' . md5(time());

		$header = '';
		
		$header .= 'MIME-Version: 1.0' . $this->newline;
		
		if ($this->protocol != 'mail') {
			$header .= 'To: ' . $this->to . $this->newline;
			$header .= 'Subject: ' . $this->subject . $this->newline;
		}
      
      $header .= 'Cc: ' . $this->copy_to . $this->newline;
      $header .= 'Bcc: ' . $this->blind_copy_to . $this->newline;
		$header .= 'Date: ' . date("D, d M Y H:i:s O") . $this->newline;
		$header .= 'From: ' . '=?UTF-8?B?' . base64_encode($this->sender) . '?=' . '<' . $this->from . '>' . $this->newline;
		$header .= 'Reply-To: ' . $this->sender . '<' . $this->from . '>' . $this->newline;
		$header .= 'Return-Path: ' . $this->from . $this->newline;
		$header .= 'X-Mailer: PHP/' . phpversion() . $this->newline;
		$header .= 'Content-Type: multipart/related; boundary="' . $boundary . '"' . $this->newline . $this->newline;

		if (!$this->html) {
			$message  = '--' . $boundary . $this->newline;
			$message .= 'Content-Type: text/plain; charset="utf-8"' . $this->newline;
			$message .= 'Content-Transfer-Encoding: 8bit' . $this->newline . $this->newline;
			$message .= $this->text . $this->newline;
		} else {
			$message  = '--' . $boundary . $this->newline;
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

			if ($this->parameter) {
				mail($this->to, '=?UTF-8?B?' . base64_encode($this->subject) . '?=', $message, $header, $this->parameter);
			} else {
				mail($this->to, '=?UTF-8?B?' . base64_encode($this->subject) . '?=', $message, $header);
			}
		} elseif ($this->protocol == 'smtp') {
			$handle = fsockopen($this->hostname, $this->port, $errno, $errstr, $this->timeout);

			if (!$handle) {
				trigger_error('Error: ' . $errstr . ' (' . $errno . ')');
			} else {
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
						trigger_error('Error: STARTTLS not accepted from server!');
						exit();								
					}
				}

				if (!empty($this->username)  && !empty($this->password)) {
					fputs($handle, 'EHLO ' . getenv('SERVER_NAME') . $this->crlf);

					$reply = '';

					while ($line = fgets($handle, 515)) {
						$reply .= $line;

						if (substr($line, 3, 1) == ' ') {
							break;
						}
					}

					if (substr($reply, 0, 3) != 250) {
						trigger_error('Error: EHLO not accepted from server!');
						exit();								
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
						trigger_error('Error: AUTH LOGIN not accepted from server!');
						exit();						
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
						trigger_error('Error: Username not accepted from server!');
						exit();								
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
						trigger_error('Error: Password not accepted from server!');
						exit();								
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
						trigger_error('Error: HELO not accepted from server!');
						exit();							
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
					trigger_error('Error: MAIL FROM not accepted from server!');
					exit();							
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
						trigger_error('Error: RCPT TO not accepted from server!');
						exit();							
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
							trigger_error('Error: RCPT TO not accepted from server!');
							exit();								
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
					trigger_error('Error: DATA not accepted from server!');
					exit();						
				}
            	
				// According to rfc 821 we should not send more than 1000 including the CRLF
				$message = str_replace("\r\n", "\n",  $header . $message);
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
					trigger_error('Error: DATA not accepted from server!');
					exit();						
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
					trigger_error('Error: QUIT not accepted from server!');
					exit();						
				}

				fclose($handle);
			}
		}
	}
}