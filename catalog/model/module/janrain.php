<?php
class ModelModulejanrain extends Model
{
	public function janrainGetUserInfo( $provider, $identifier, $newuser_email )
	{
		$check = $this->janrainCheckUser( $provider, $identifier, $newuser_email );
		if(!$check)
			return false;
			
		$user_id = $this->janrainGetUser( $provider, $identifier, $newuser_email );
		if(!$user_id>0)
			return false;
			
		$user_id = $this->janrainGetUserId( $user_id );
		if(!$user_id>0)
			return false;
			
		$user_info = $this->getUser($user_id);
		$user_info = !empty($user_info) ? $user_info : false;
		
		return $user_info;
	}
	
	public function janrainCheckUser( $provider, $identifier, $newuser_email )
	{
		if($newuser_email != '')
			$newuser_email = " AND email='".$newuser_email."'";
		
		$query 	= "SELECT count(*) FROM ".DB_PREFIX."janrain WHERE provider='".$provider."' AND identifier='".$identifier."' ".$newuser_email." LIMIT 1";
		$res 	= $this->query( $query );
		$check 	= (int)$res->num_rows>0 ? true : false;
		
		return $check;
	}
	
	public function janrainGetUser( $provider, $identifier, $newuser_email )
	{
		if($newuser_email != '')
			$newuser_email = " AND email='".$newuser_email."'";
			
		$query 		= "SELECT user_id FROM " . DB_PREFIX . "janrain WHERE `provider`='" . $provider . "' AND `identifier`='" . $identifier . "'".$newuser_email." LIMIT 1";
		$res 		= $this->query( $query );
		$user_id 	= !empty($res->row['user_id']) ? (int)$res->row['user_id'] : 0;
	}
	
	public function janrainGetUserId( $user_id )
	{
		if($user_id>0)
		{
			$query 		= "SELECT customer_id FROM `" . DB_PREFIX . "customer` WHERE customer_id = '" . $user_id . "' LIMIT 1";
			$res 		= $this->query( $query );
			$user_id 	= !empty($res->row['customer_id']) ? (int)$res->row['customer_id'] : 0;
			return $user_id;
		}
		return false;
	}
		
	public function janrainUpdateUser( $newuser_id, $newuser_email, $provider, $identifier )
	{
		if( !$newuser_id || !$newuser_email || !$provider || !$identifier )
			return;
			
		$check = $this->janrainCheckUser( $provider, $identifier, $newuser_email );
		if(!$check)
		{
			$this->janrainCreateUser( $newuser_id, $newuser_email, $provider, $identifier );
		}
		
		$lastvisit_date = date( 'Y-m-d H:i:s' );
			
		if($newuser_email != '')
			$newuser_email = ", `email`='" . $newuser_email . "'";
			
		$query = "UPDATE `" . DB_PREFIX . "janrain` SET user_id='" . $newuser_id . "', lastvisit_date='" . $lastvisit_date . "' ".$newuser_email." WHERE `provider`='" . $provider . "' AND `identifier`='" . $identifier . "' LIMIT 1";
		$this->query( $query );
	}
	
	public function janrainCreateUser( $newuser_id, $newuser_email, $provider, $identifier )
	{
		$check = $this->janrainCheckUser( $provider, $identifier, $newuser_email );
		if($check)
		{
			$this->janrainDeleteUser( $provider, $identifier, $newuser_email );
		}
		
		$register_date 	= date( 'Y-m-d H:i:s' );
		$lastvisit_date = date( 'Y-m-d H:i:s' );
		
		if( !$newuser_id || !$newuser_email || !$provider || !$identifier )
			return;
			
		$query = "INSERT INTO `" . DB_PREFIX . "janrain` SET
					user_id='" . $newuser_id . "',
					email='" . $newuser_email . "',
					provider='" . $provider . "',
					identifier='" . $identifier . "',
					register_date='" . $register_date . "',
					lastvisit_date='" . $lastvisit_date . "'
				";
		$this->query( $query );
	}
	
	public function addCustomer( $user_data )
	{
		$query = "INSERT INTO `" . DB_PREFIX . "customer` SET
					firstname  = '" . $this->db->escape($user_data['firstname']) . "',
					lastname = '" . $this->db->escape($user_data['lastname']) . "',
					email = '" . $this->db->escape($user_data['email']) . "',
					password = '" . $this->user->encrypt($user_data['password']) . "',
					customer_group_id = '" . (int)$user_data['customer_group_id'] . "',
					status = '" . (int)$user_data['status'] . "',
					approved  = '" . (int)$user_data['approved'] . "',
					date_added = NOW()
				";
		
		$this->query($query);
		return (int)$this->db->getLastId();
	}
	
	public function janrainGetCustomerGroupId()
	{
		$customer_group_id = $this->config->get('config_customer_group_id');
		
		if($customer_group_id)
			return $customer_group_id;
		
		$query 				= "SELECT customer_group_id  FROM `" . DB_PREFIX . "customer_group` WHERE name = 'Default' LIMIT 1";
		$result				= $this->query( $query );
		$customer_group_id 	= isset($result->row['customer_group_id']) ? (int)$result->row['customer_group_id'] : 0;
		
		return $customer_group_id;
	}
	
	public function getUser($user_id)
	{
		$query = $this->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE customer_id = '" . (int)$user_id . "' LIMIT 1");
	
		return $query->row;
	}
	public function getCustomer($customer_id)
	{
		$query = $this->query("SELECT DISTINCT * FROM ".DB_PREFIX."customer WHERE customer_id='".(int)$customer_id."' LIMIT 1");
	
		return $query->row;
	}
	
	public function getCustomerByEmail($customer_email)
	{
		$query = $this->query("SELECT DISTINCT * FROM ".DB_PREFIX."customer WHERE email='".$customer_email."' LIMIT 1");
	
		return $query->row;
	}
	
	public function janrainDeleteUser( $provider, $identifier, $newuser_email )
	{
		if($newuser_email != '')
			$newuser_email = " AND `email`='" . $newuser_email . "'";
		$query = "DELETE FROM `" . DB_PREFIX . "janrain` WHERE `provider`='" . $provider . "' AND `identifier`='" . $identifier . "' ".$newuser_email." LIMIT 1";
		$this->query( $query );
	}
	
	public function janrainCheckUsernameExist( $username )
	{
		$query 		= "SELECT customer_id  FROM `" . DB_PREFIX . "customer` WHERE username  = '" . $username . "' LIMIT 1";
		$result		= $this->query( $query );
		$return 	= !empty($result->row['customer_id']) && (int)$result->row['customer_id']>0 ? true : false;
		return $return;
	}

}