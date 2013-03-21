<?php 
class ControllerAccountBlockLogin extends Controller {
	
	public function index($settings, $type = 'header') {
		$this->language->load('account/block/login');
		
		switch($type){
			case 'header':
				$this->template->load('account/block/login_header');
				break;
			case 'standard':
				$this->template->load('account/block/login_standard');
				break;
			default:
				trigger_error("Error in ControllerAccountBlockLogin::index(): Invalid template type requested $type");
				return '';
		}
		
		$this->data['action'] = $this->url->link('account/login');
		$this->data['register'] = $this->url->link('account/register');
		$this->data['forgotten'] = $this->url->link('account/forgotten');
      
		$this->data['email'] = !empty($_POST['email']) ? $_POST['email'] : '';
		
		$this->data['redirect'] = $this->url->current_page();
		
    	$this->render();
  	}
}
