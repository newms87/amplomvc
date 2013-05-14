<?php       
class ControllerCommonLogout extends Controller {   
	public function index() { 
    	$this->user->logout();
      
      $this->url->redirect($this->url->link('common/login'));
  	}
}
