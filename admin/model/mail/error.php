<?php
class Admin_Model_Mail_Error extends Model 
{
		
	public function getFailedMessages()
	{
		$result = $this->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `key` = 'mail_fail'");
		
		$messages = array();
		
		//we need to make sure mail class is loaded first!
		$this->mail->init();
		
		foreach($result->rows as $row) {
			if(empty($row['value'])) continue;

			$msg = unserialize($row['value']);
			
			$msg['mail_fail_id'] = $row['setting_id'];
			
			$messages[] = $msg;
		}
		
		return $messages;
	}
	
	public function deleteFailedMessage($mail_fail_id)
	{
		$this->query("DELETE FROM " . DB_PREFIX . "setting WHERE setting_id = '" . (int)$mail_fail_id . "'");
	}
	
	public function total_failed_messages()
	{
		return $this->db->query_var("SELECT COUNT(*) as total FROM " . DB_PREFIX . "setting WHERE `key` = 'mail_fail'");
	}
}