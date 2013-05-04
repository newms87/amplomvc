<?php
class ControllerDevDev extends Controller {
	public function index(){
		$this->template->load('dev/dev');

		$this->load->language('dev/dev');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('dev/dev'));
		
		$this->data['request_sync_table'] = $this->url->link('dev/dev/request_sync_table');
		
		$this->data['cancel'] = $this->url->link('common/home');
		
		$defaults = array(
			'tables' => ''
		);

		foreach($defaults as $key=>$default){
			if(isset($_POST[$key]))
				$this->data[$key] = $_POST[$key];
			else
				$this->data[$key] = $default;
		}
		
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render()); 
	}
	
	public function request_sync_table() {
		$this->language->load('dev/dev');
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['tables']) && $this->validate()) {
			echo $this->_('success_sync_table');
		} else {
			echo $this->_('error_sync_table');
		}
		
		exit;
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'dev/dev')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
	}
}
