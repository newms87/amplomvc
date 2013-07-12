<?php
class Admin_Model_Mail_Error extends Model 
{
	public function getFailedMessage($mail_fail_id)
	{
		$setting = $this->queryRow("SELECT * FROM " . DB_PREFIX . "setting WHERE setting_id = '" . (int)$mail_fail_id . "' AND `key` = 'mail_fail'");
		
		$message = unserialize($setting['value']);
		
		$message['mail_fail_id'] = $setting['setting_id'];
		
		return $message;
	}
	
	public function getFailedMessages()
	{
		$message_list = $this->queryRows("SELECT * FROM " . DB_PREFIX . "setting WHERE `key` = 'mail_fail'");
		
		$messages = array();
		
		//we need to make sure mail class is loaded first!
		$this->mail->init();
		
		foreach($message_list as $message) {
			if(empty($message['value'])) continue;

			$msg = unserialize($message['value']);
			
			$msg['mail_fail_id'] = $message['setting_id'];
			
			$messages[] = $msg;
		}
		
		return $messages;
	}
	
	public function deleteFailedMessage($mail_fail_id)
	{
		$this->query("DELETE FROM " . DB_PREFIX . "setting WHERE setting_id = '" . (int)$mail_fail_id . "' AND `key` = 'mail_fail'");
	}
	
	public function total_failed_messages()
	{
		return $this->db->queryVar("SELECT COUNT(*) as total FROM " . DB_PREFIX . "setting WHERE `key` = 'mail_fail'");
	}
}