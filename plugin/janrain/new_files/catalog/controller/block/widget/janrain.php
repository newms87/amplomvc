<?php
class Catalog_Controller_Block_Widget_Janrain extends Controller 
{
	public function index($settings)
	{
		$this->language->load('block/widget/janrain');
		$this->template->load('block/widget/janrain');
		
		$icon_size 				= !empty($settings['icon_size']) ? $settings['icon_size'] : 'small';
		$login_redirect		= !empty($settings['login_redirect']) ? $settings['login_redirect'] : $this->url->here();
		$display_type			= !empty($settings['display_type']) ? $settings['display_type'] : 'popup';
		$application_domain	= !empty($settings['application_domain']) ? $settings['application_domain'] : '';
		$api_key					= !empty($settings['api_key']) ? $settings['api_key'] : '';
		$display_icons			= !empty($settings['display_icons']) ? $settings['display_icons'] : array();
		
		//We do not want to login to the logout page (as the user will be logged out)
		$_SESSION['janrain_login_redirect'] = preg_replace("/\/logout/","/account", $login_redirect);
		
		$this->data['display_type'] = $display_type;
		$this->data['icon_size'] = $icon_size;
		
		//The Janrain Icon image sprite
		$this->data['image_offset'] = array(
			'facebook'=>0,'google'=>1,'linkedin'=>2,'myspace'=>3,'twitter'=>4,'windowslive'=>5,
			'yahoo'=>6,'aol'=>7,'bing'=>8,'flickr'=>9,''=>10,''=>11,''=>12,''=>13,''=>14,
			''=>15,'wordpress'=>16,'paypal'=>17,''=>18,''=>19,''=>20,''=>21
		);
		
		$icon_sizes = array('tiny'=>16,'small'=>16,'large'=>30);
		
		$this->data['image_size'] = $icon_sizes[$icon_size];
		
		$this->data['display_icons'] = $display_icons;
		
		if (!empty($_REQUEST['redirect']) && $_REQUEST['redirect'] === 'logout') {
			$this->logout();
		}
		
		$this->data['janrain_lang']		= 'en';
		$janrain_site	= 'https://'. $application_domain .'.rpxnow.com/';
		$this->data['janrain_token_url']	= $this->url->link('block/widget/janrain/janrain_auth');;
		
		// Janrain Engage Application name
		$this->data['application_domain'] = $application_domain;
		
		if (!$this->customer->isLogged()) {
			if ( $display_type === 'popup' ) {
				$this->data['janrain_post_token_url'] = $janrain_site .'openid/v2/signin?token_url='. $this->data['janrain_token_url'].'&amplanguage_preference='.$this->data['janrain_lang'];
			} else {
				$this->data['janrain_post_token_url'] = $janrain_site .'openid/embed?token_url='. urlencode($this->data['janrain_token_url']).'&amplanguage_preference='.$this->data['janrain_lang'];
			}
		}
		
		$this->render();
	}

	public function janrain_auth()
	{
		$settings = $this->Model_Block_Block->getBlockSettings('widget/janrain');
		
		// Janrain Engage API key
		if (empty($settings['api_key'])) {
			trigger_error("Janrain API key was not set. You must set the API key before users can be authorized!");
			$this->message->add('warning', "Janrain Engage has not been configured. Please log in using your username and password.");
			$this->url->redirect($this->url->link('account/login'));
		}
		
		$application_domain = $settings['application_domain'];
		$api_key = $settings['api_key'];
		
		$janrain_token = !empty($_REQUEST['token']) ? $_REQUEST['token'] : false;
		
		if ($janrain_token) {
			$post_data  = array(
								'token'  => $janrain_token,
								'apiKey'	=> $api_key,
								'format' => 'json'
							);
			$post_url	= "https://{$application_domain}.rpxnow.com/api/v2/auth_info/";
			
			$curl		= curl_init();
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_URL, $post_url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$raw_json	= curl_exec($curl);
			curl_close($curl);
			
			// parse the json response into an associative array
			$auth_info = json_decode($raw_json, true);
		
			$this->language->load('block/widget/janrain');
			
			// process the auth_info response
			if ( $auth_info['stat'] == 'ok' ) {
				$this->parseJanrainInfo($auth_info,$raw_json);
				$this->message->add("success",$this->_('success_janrain_auth'));
			}
			else {
				$this->message->add("warning",$this->language->format('error_janrain_auth',$this->config->get('config_email'),$this->config->get('config_email')));
				$this->url->redirect($this->url->link('account/login'));
			}
		}
	
		// Login Redirection URL
		if (isset($_SESSION['janrain_login_redirect'])) {
			$login_redirect = $_SESSION['janrain_login_redirect'];
		} else {
			$login_redirect = !empty($settings['login_redirect']) ? $settings['login_redirect'] : $this->url->site();
		}
		
		$this->url->redirect(preg_replace("/\/logout/","/account",$login_redirect));
	}

	function parseJanrainInfo($auth_info,$raw_json='')
	{
		// load models
		$auth_profile 		= $auth_info['profile'];
		$auth_provider		= strtolower($auth_profile['providerName']);
		$auth_identifier	= strtolower($auth_profile['identifier']);
		$auth_email			= isset($auth_profile['email']) && $auth_profile['email']!='' && $this->janrainIsValidEmail($auth_profile['email']) ? $auth_profile['email'] : NULL;
		$auth_email			= $auth_email ? $auth_email : $this->generateEmailAddress($auth_profile,$auth_provider,$auth_identifier);
		$janrain_user			= $this->Model_Widget_Janrain->getCustomerByEmail( $auth_email );
		
		$janrain_exist_user = false;
		if(!empty($janrain_user) && isset($janrain_user['email']) && $janrain_user['email'] != '' && isset($janrain_user['customer_id']) )
		{
			$janrain_exist_user 	= true;
			$customer_email 	= $janrain_user['email'];
			$customer_password 	= $janrain_user['password'];
		}
		
		if($janrain_exist_user)
		{
			$this->Model_Widget_Janrain->janrainUpdateUser( $janrain_user['customer_id'], $janrain_user['email'], $auth_provider, $auth_identifier );
		}
		else {
			$auth_profile_name = isset($auth_profile['name']) ? $auth_profile['name'] : '';
			
			$auth_username 		= isset($auth_profile_name['preferredUsername']) && $auth_profile_name['preferredUsername'] ? $auth_profile_name['preferredUsername'] : '';
			$auth_display_name 	= isset($auth_profile_name['displayName']) && $auth_profile_name['displayName'] ? $auth_profile_name['displayName'] : '';
			
			$auth_formatted = isset($auth_profile_name['formatted']) && $auth_profile_name['formatted'] ? split(' ',$auth_profile_name['formatted']) : '';
			$firstname 		= is_array($auth_formatted) && isset($auth_formatted[0]) ? trim($auth_formatted[0]) : NULL;
			$firstname		= $firstname ? $firstname : $auth_display_name;
			$firstname		= $firstname ? $firstname : $auth_username;
			$lastname 		= is_array($auth_formatted) && isset($auth_formatted[1]) ? trim($auth_formatted[1]) : '';
							
			$email			= $auth_email;
			$password		= 'thejoomla';//$this->generatePassword();
			$customer_group_id = $this->Model_Widget_Janrain->janrainGetCustomerGroupId();
			$status			= 1;
			$approved		= 1;
			
			$user_data = array();
			$user_data['password'] 		= $password;
			$user_data['firstname'] 	= $firstname;
			$user_data['lastname'] 		= $lastname;
			$user_data['email'] 		= $email;
			$user_data['customer_group_id'] = $customer_group_id;
			$user_data['status'] 		= $status;
			$user_data['approved'] 		= $approved;
			
			$user_id = (int)$this->Model_Widget_Janrain->addCustomer( $user_data );
			
			if($user_id>0)
			{
				$customer_info 		= $this->Model_Widget_Janrain->getCustomer($user_id);
				$customer_email 	= $customer_info['email'];
				$customer_password 	= $customer_info['password'];
				$this->Model_Widget_Janrain->janrainCreateUser( $user_id, $customer_email, $auth_provider, $auth_identifier );
				
				// send email to admin to notify about new customer
				$subject = sprintf($this->_('text_subject'), SITE_SSL);
			
				$message = $this->_('text_hello') . "\n\n";
				
				$customer_name = $customer_info['firstname'];
				if($customer_info['lastname'])
					$customer_name .= ' ' . $customer_info['lastname'];
					
				$message .= sprintf($this->_('text_message'), SITE_SSL). "\n\n";
				$message .= $this->_('text_customer_detail') . "\n";
				$message .= $this->_('text_dash_line') . "\n";
				$message .= sprintf($this->_('text_customer_id'), $customer_info['customer_id']). "\n";
				$message .= sprintf($this->_('text_customer_name'), $customer_name). "\n";
				$message .= sprintf($this->_('text_customer_email'), $customer_info['email']). "\n";
				$message .= sprintf($this->_('text_provider'), ucfirst($auth_provider)). "\n\n";
				
				$this->mail->init();
								
				$this->mail->setTo($this->config->get('config_email'));
				$this->mail->setFrom($this->config->get('config_email'));
				$this->mail->setSender($this->config->get('config_name'));
				$this->mail->setSubject($subject);
				$this->mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
				$this->mail->send();
			}
		}
		
		if ( $customer_email && $customer_password ) {
			$this->login($customer_email, $customer_password);
		}
	}
	
	function generatePassword($length=6)
	{
		$password 	= "";
		$possible 	= "123467890abcdfghjkmnpqrtvwxyzABCDFGHJKLMNPQRTVWXYZ";
		$maxlength 	= strlen($possible);
	
		if ($length > $maxlength) {
			$length = $maxlength;
		}
		$i = 0;
		while ($i < $length) {
			$char = substr($possible, mt_rand(0, $maxlength-1), 1);
			if (!strstr($password, $char)) {
				$password .= $char;
				$i++;
			}
		}
		return $password;
	}
	
	function janrainIsValidEmail($email)
	{
		// Split the email into a local and domain
		list($local,$domain) = explode('@',$email);
	
		// Check Length of domain
		$domainLen	= strlen($domain);
		if ($domainLen < 1 || $domainLen > 255) {
			return false;
		}
	
		// Check the local address
		// We're a bit more conservative about what constitutes a "legal" address, that is, A-Za-z0-9!#$%&\'*+/=?^_`{|}~-
		$allowed	= 'A-Za-z0-9!#&*+=?_-';
		$regex		= "/^[$allowed][\.$allowed]{0,63}$/";
		if ( ! preg_match($regex, $local) ) {
			return false;
		}
	
		// No problem if the domain looks like an IP address, ish
		$regex		= '/^[0-9\.]+$/';
		if ( preg_match($regex, $domain)) {
			return true;
		}
	
		// Check Lengths
		$localLen	= strlen($local);
		if ($localLen < 1 || $localLen > 64) {
			return false;
		}
	
		// Check the domain
		$domain_array	= explode(".", rtrim( $domain, '.' ));
		$regex		= '/^[A-Za-z0-9-]{0,63}$/';
		foreach ($domain_array as $domain ) {
	
			// Must be something
			if ( ! $domain ) {
				return false;
			}
	
			// Check for invalid characters
			if ( ! preg_match($regex, $domain) ) {
				return false;
			}
	
			// Check for a dash at the beginning of the domain
			if ( strpos($domain, '-' ) === 0 ) {
				return false;
			}
	
			// Check for a dash at the end of the domain
			$length = strlen($domain) -1;
			if ( strpos($domain, '-', $length ) === $length ) {
				return false;
			}
	
		}
	
		return true;
	}
	
	function getUniqUsername($auth_username,$auth_display_name,$firstname,$lastname)
	{
		if( !$this->Model_Widget_Janrain->janrainCheckUsernameExist( $auth_username ) )
			return $auth_username;
		
		if( !$this->Model_Widget_Janrain->janrainCheckUsernameExist( $auth_display_name ) )
			return $auth_display_name;
		
		if( !$this->Model_Widget_Janrain->janrainCheckUsernameExist( $firstname ) )
			return $firstname;
			
		$username = str_replace(' ','',$firstname.$lastname);
		if( !$this->Model_Widget_Janrain->janrainCheckUsernameExist( $username ) )
			return $username;
	}
	
	public function login($email, $password)
	{
		$approved = $this->config->get('config_customer_approval') ? "AND approved = '1'" : '';
		
		$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($email)) . "' AND password = '" . $this->db->escape($password) . "' AND status = '1' $approved");
		
		if ($customer_query->num_rows)
		{
			$this->customer->login($email, '', true);
			
			$this->customer_id 	= $customer_query->row['customer_id'];
			$this->firstname 	= $customer_query->row['firstname'];
			$this->lastname 	= $customer_query->row['lastname'];
			$this->email 		= $customer_query->row['email'];
			$this->telephone 	= $customer_query->row['telephone'];
			$this->fax 			= $customer_query->row['fax'];
			$this->newsletter 	= $customer_query->row['newsletter'];
			$this->customer_group_id = $customer_query->row['customer_group_id'];
			$this->address_id 	= $customer_query->row['address_id'];
		
			return true;
		}
		return false;
  	}
  
  	public function logout()
  	{
  		$settings = $this->Model_Block_Block->getBlockSettings('widget/janrain');
		
		if(isset($this->session->data['customer_id']))
			unset($this->session->data['customer_id']);

		$this->customer_id 	= '';
		$this->firstname 	= '';
		$this->lastname 	= '';
		$this->email 		= '';
		$this->telephone 	= '';
		$this->fax 			= '';
		$this->newsletter 	= '';
		$this->customer_group_id = '';
		$this->address_id 	= '';
		
		$this->customer->logout();
		
		$logout_redirect = !empty($settings['logout_redirect']) ? $settings['logout_redirect'] : $this->url->site();

		$this->url->redirect($logout_redirect);
  	}
	
	public function generateEmailAddress( $auth_profile, $auth_provider, $auth_identifier )
	{
		$auth_profile_name = isset($auth_profile['name']) ? $auth_profile['name'] : '';
			
		$auth_username 		= isset($auth_profile_name['preferredUsername']) && $auth_profile_name['preferredUsername'] ? $auth_profile_name['preferredUsername'] : '';
		$auth_display_name 	= isset($auth_profile_name['displayName']) && $auth_profile_name['displayName'] ? $auth_profile_name['displayName'] : '';
		
		$auth_formatted = isset($auth_profile_name['formatted']) && $auth_profile_name['formatted'] ? split(' ',$auth_profile_name['formatted']) : '';
		$firstname 		= is_array($auth_formatted) && isset($auth_formatted[0]) ? trim($auth_formatted[0]) : NULL;
		$firstname		= $firstname ? $firstname : $auth_display_name;
		$firstname		= $firstname ? $firstname : $auth_username;
		
		$lastname 		= is_array($auth_formatted) && isset($auth_formatted[1]) ? trim($auth_formatted[1]) : '';
		
		$email			= $firstname;
		$email			.= $lastname ? $lastname : '';
		$email			.= strlen($auth_provider).strlen($auth_identifier);
		$email			= strtolower($email);
		
		// get host name from URL
		preg_match( "/^(http:\/\/)?([^\/]+)/i", 'http://www.mysite.com/index.php', $matches );
		
		if( isset($matches[2]) && $matches[2] )
		{
			// get last two segments of host name
			preg_match("/[^\.\/]+\.[^\.\/]+$/", $matches[2], $matches);
			$email			.= isset($matches[0]) && $matches[0] ? '@'.$matches[0] : '@yoursite.com';
		}
		else {
			$email			.= '@yoursite.com';
		}
		$email = str_replace( ' ', '_', $email );
		return $email;
  	}
}