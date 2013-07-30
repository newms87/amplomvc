<?php
class Catalog_Model_Affiliate_Affiliate extends Model
{
	public function addAffiliate($data)
	{
			$this->query("INSERT INTO " . DB_PREFIX . "affiliate SET firstname = '" . $this->escape($data['firstname']) . "', lastname = '" . $this->escape($data['lastname']) . "', email = '" . $this->escape($data['email']) . "', telephone = '" . $this->escape($data['telephone']) . "', fax = '" . $this->escape($data['fax']) . "', password = '" . $this->user->encrypt($data['password']) . "', company = '" . $this->escape($data['company']) . "', address_1 = '" . $this->escape($data['address_1']) . "', address_2 = '" . $this->escape($data['address_2']) . "', city = '" . $this->escape($data['city']) . "', postcode = '" . $this->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', code = '" . $this->escape(uniqid()) . "', commission = '" . (float)$this->config->get('config_commission') . "', tax = '" . $this->escape($data['tax']) . "', payment = '" . $this->escape($data['payment']) . "', cheque = '" . $this->escape($data['cheque']) . "', paypal = '" . $this->escape($data['paypal']) . "', bank_name = '" . $this->escape($data['bank_name']) . "', bank_branch_number = '" . $this->escape($data['bank_branch_number']) . "', bank_swift_code = '" . $this->escape($data['bank_swift_code']) . "', bank_account_name = '" . $this->escape($data['bank_account_name']) . "', bank_account_number = '" . $this->escape($data['bank_account_number']) . "', status = '1', date_added = NOW()");
	
		$this->language->load('mail/affiliate');
		
		$subject = sprintf($this->_('text_subject'), $this->config->get('config_name'));
		
		$message  = sprintf($this->_('text_welcome'), $this->config->get('config_name')) . "\n\n";
		$message .= $this->_('text_approval') . "\n";
		$message .= $this->url->link('affiliate/login') . "\n\n";
		$message .= $this->_('text_services') . "\n\n";
		$message .= $this->_('text_thanks') . "\n";
		$message .= $this->config->get('config_name');
		
		$this->mail->init();
						
		$this->mail->setTo($_POST['email']);
		$this->mail->setFrom($this->config->get('config_email'));
		$this->mail->setSender($this->config->get('config_name'));
		$this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
		$this->mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
		$this->mail->send();
	}
	
	public function editAffiliate($data)
	{
		$this->query("UPDATE " . DB_PREFIX . "affiliate SET firstname = '" . $this->escape($data['firstname']) . "', lastname = '" . $this->escape($data['lastname']) . "', email = '" . $this->escape($data['email']) . "', telephone = '" . $this->escape($data['telephone']) . "', fax = '" . $this->escape($data['fax']) . "', company = '" . $this->escape($data['company']) . "', address_1 = '" . $this->escape($data['address_1']) . "', address_2 = '" . $this->escape($data['address_2']) . "', city = '" . $this->escape($data['city']) . "', postcode = '" . $this->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "' WHERE affiliate_id = '" . (int)$this->affiliate->getId() . "'");
	}

	public function editPayment($data)
	{
			$this->query("UPDATE " . DB_PREFIX . "affiliate SET tax = '" . $this->escape($data['tax']) . "', payment = '" . $this->escape($data['payment']) . "', cheque = '" . $this->escape($data['cheque']) . "', paypal = '" . $this->escape($data['paypal']) . "', bank_name = '" . $this->escape($data['bank_name']) . "', bank_branch_number = '" . $this->escape($data['bank_branch_number']) . "', bank_swift_code = '" . $this->escape($data['bank_swift_code']) . "', bank_account_name = '" . $this->escape($data['bank_account_name']) . "', bank_account_number = '" . $this->escape($data['bank_account_number']) . "' WHERE affiliate_id = '" . (int)$this->affiliate->getId() . "'");
	}
	
	public function editPassword($email, $password)
	{
			$this->query("UPDATE " . DB_PREFIX . "affiliate SET password = '" . $this->user->encrypt($password) . "' WHERE email = '" . $this->escape($email) . "'");
	}
				
	public function getAffiliate($affiliate_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "affiliate WHERE affiliate_id = '" . (int)$affiliate_id . "'");
		
		return $query->row;
	}
	
	public function getAffiliateByCode($code)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "affiliate WHERE code = '" . $this->escape($code) . "'");
		
		return $query->row;
	}
			
	public function getTotalAffiliatesByEmail($email)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "affiliate WHERE email = '" . $this->escape($email) . "'");
		
		return $query->row['total'];
	}
}