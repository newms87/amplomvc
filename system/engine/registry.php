<?php
final class Registry {
	private $data = array();
	
	public function get($key) {
		if(isset($this->data[$key])){
			return $this->data[$key];
		}
		elseif(strpos($key,'model_') === 0){
			return $this->data['load']->model($key);
		}
		else{
			return $this->data['load']->library($key);
		}
	}

	public function set($key, $value) {
		$this->data[$key] = $value;
	}

	public function has($key) {
		return isset($this->data[$key]);
  	}
}
