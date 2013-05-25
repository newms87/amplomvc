<?php
class Message {
	private $session;
	
	function __construct($session){
		$this->session = $session;
		if(!isset($this->session->data['messages'])){
			$this->session->data['messages'] = array();
		}
	}
	
	
	public function add($type, $message){
		if(is_string($message)){
			$this->session->data['messages'][$type][] = $message;
		}
		elseif(is_array($message)){
			foreach($message as $m){
				$this->add($type, $m);
			}
		}
	}
	
	public function error_set(){
		return isset($this->session->data['messages']['error']) || isset($this->session->data['messages']['warning']);
	}
	
	public function peek($type=''){
		if($type){
			if(isset($this->session->data['messages'][$type])){
				return $this->session->data['messages'][$type];
			}
			else{
				return array();
			}
		}
		
		return $this->session->data['messages'];
	}
	
	public function fetch($type=''){
		if(!isset($this->session->data['messages'])){
			return array();
		}
		
		if($type){
			if(isset($this->session->data['messages'][$type])){
				$msgs = $this->session->data['messages'][$type];
				
				unset($this->session->data['messages'][$type]);
				
				return $msgs;
			}
			else{
				return array();
			}
		}
		
		$msgs = $this->session->data['messages'];
		
		unset($this->session->data['messages']);
		
		return $msgs;
	}
}