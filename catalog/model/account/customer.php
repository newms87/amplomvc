<?php
class ModelAccountCustomer extends Model 
{
	public function addCustomer($data)
	{
		
		//Add New Customer
		$data['store_id']		= $this->config->get('config_store_id');
		$data['customer_group_id'] = $this->config->get('config_customer_group_id');
		$data['date_added']		= date_format(date_create(), 'Y-m-d H:i:s');
		$data['password']		= $this->customer->encrypt($data['password']);
		$data['status']				= 1;
		
		if (!isset($data['newsletter'])) {
			$data['newsletter'] = 0;
		}
		
		if ($this->config->get('config_customer_approval')) {
			$data['approved'] = 1;
		}
		else {
			$data['approved'] = 0;
		}
		
		$data['customer_id'] = $this->insert('customer', $data);
		
		$address_id = $this->insert('address',  $data);
		
		
		//Add Address ID to new customer
		$this->update('customer', array('address_id' => $address_id) , $data['customer_id']);
		
		//Send Mail to new customer
		$this->language->load('mail/customer');
		
		$insertables = array(
			'first_name'	=> $data['firstname'],
			'last_name'=> $data['lastname'],
			'store_name'	=> $this->config->get('config_name'),
			'store_url'=> $this->url->site()
		);
			
		$subject = $this->tool->insertables($insertables, $this->config->get('mail_registration_subject'));
		$message = $this->tool->insertables($insertables, $this->config->get('mail_registration_message'));
		
		$this->mail->init();
		
		$this->mail->setTo($data['email']);
		$this->mail->setFrom($this->config->get('config_email'));
		$this->mail->setSender($this->config->get('config_name'));
		$this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
		$this->mail->setHtml(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
		
		$this->mail->send();
		
		// Send to main admin email if new account email is enabled
		if ($this->config->get('config_account_mail')) {
			$this->mail->setTo($this->config->get('config_email'));
			$this->mail->send();
			
			// Send to additional alert emails if new account email is enabled
			$emails = explode(',', $this->config->get('config_alert_emails'));
			
			foreach ($emails as $email) {
				if ($this->validation->email($email)) {
					$this->mail->setTo($email);
					$this->mail->send();
				}
			}
		}
		
		return $data['customer_id'];
	}
	
	public function editCustomer($data)
	{
		$where = array(
			'customer_id' => $this->customer->getId()
		);
	
		//we do not allow editing the password here ( must be done via $this->editPassword() )
		if (isset($data['password'])) {
			unset($data['password']);
		}
	
		$this->update('customer', $data, $where);
	}

	public function editPassword($email, $password)
	{
		$data = array(
			'password' => $this->customer->encrypt($password)
		);
		
		$this->update('customer', $data, array('email' => $email));
	}

	public function editNewsletter($newsletter)
	{
		$data = array(
			'newsletter' => $newsletter
		);
		
		$where = array(
			'customer_id' => $this->customer->getId()
		);
		
		$this->update('customer', $data, $where);
	}
					
	public function getCustomer($customer_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
		
		return $query->row;
	}
	
	public function getCustomerByToken($token)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "customer WHERE token = '" . $this->db->escape($token) . "' AND token != ''");
		
		$this->query("UPDATE " . DB_PREFIX . "customer SET token = ''");
		
		return $query->row;
	}
		
	public function getCustomers($data = array()) {
		$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cg.name AS customer_group FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "customer_group cg ON (c.customer_group_id = cg.customer_group_id) ";

		$implode = array();
		
		if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
			$implode[] = "LCASE(CONCAT(c.firstname, ' ', c.lastname)) LIKE '" . $this->db->escape(strtolower($data['filter_name'])) . "%'";
		}
		
		if (isset($data['filter_email']) && !is_null($data['filter_email'])) {
			$implode[] = "c.email = '" . $this->db->escape($data['filter_email']) . "'";
		}
		
		if (isset($data['filter_customer_group_id']) && !is_null($data['filter_customer_group_id'])) {
			$implode[] = "cg.customer_group_id = '" . $this->db->escape($data['filter_customer_group_id']) . "'";
		}
		
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
		}
		
		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "c.approved = '" . (int)$data['filter_approved'] . "'";
		}
			
		if (isset($data['filter_ip']) && !is_null($data['filter_ip'])) {
			$implode[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
		}
				
		if (isset($data['filter_date_added']) && !is_null($data['filter_date_added'])) {
			$implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		
		$sort_data = array(
			'name',
			'c.email',
			'customer_group',
			'c.status',
			'c.ip',
			'c.date_added'
		);
			
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}
			
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		
		$query = $this->query($sql);
		
		return $query->rows;
	}
		
	public function getTotalCustomersByEmail($email)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE email = '" . $this->db->escape($email) . "'");
		
		return $query->row['total'];
	}
	
	public function getIps($customer_id)
	{
		$query = $this->query("SELECT * FROM `" . DB_PREFIX . "customer_ip` WHERE customer_id = '" . (int)$customer_id . "'");
		
		return $query->rows;
	}
	
	public function isBlacklisted($ip)
	{
		$query = $this->query("SELECT * FROM `" . DB_PREFIX . "customer_ip_blacklist` WHERE ip = '" . $this->db->escape($ip) . "'");
		
		return $query->num_rows;
	}
}
