<?php
class Dev{
	private $registry;
	
	function __construct($registry){
		$this->registry = $registry;
	}
	
	public function __get($key){
		return $this->registry->get($key);
	}
	
	public function login_external_server($domain, $username, $password){
		$request = 'username=' . $username;
		$request .= '&password=' . $password;
		
		$curl = curl_init($domain . '/admin/index.php?route=common/login&response=1');
		
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_COOKIEJAR, 'token');
		curl_setopt($curl, CURLOPT_COOKIEFILE, 'token');
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		
		$response = curl_exec($curl);
		
		echo $response . ' was response <br>';
		
		echo $domain . '/admin/index.php?route=common/login&response=1';
		if($response == 'SUCCESS'){
			return true;
		}
		
		return false;
	}
	
	public function request_table_sync($conn_info, $tables){
		if(!$this->login_external_server($conn_info['domain'], $conn_info['username'], $conn_info['password'])){
			$this->message->add("warning", "Login Failed for $conn_info[domain]!");
			
			return false;
		}
		
		$curl = curl_init($conn_info['domain'] . '/admin/index.php?route=dev/dev/request_table_data');
		
		$request = http_build_query(array('tables' => $tables));
		
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_COOKIEJAR, 'token');
		curl_setopt($curl, CURLOPT_COOKIEFILE, 'token');
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		
		$response = curl_exec($curl);
			
		if (!$response) {
			trigger_error('Dev::request_table_sync(): Curl Failed -  ' . curl_error($curl) . '(' . curl_errno($curl) . ')');
		}
		else{
			$file = DIR_DOWNLOAD . 'tempsql.sql';
			
			file_put_contents($file, $response);
			
			chmod($file, 0600);
			
			$this->db->execute_file($file);
			
			$this->message->add('success', "Successfully synchronized the requested tables from $conn_info[domain]!");
		}
		
		return $response;
	}
}