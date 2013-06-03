<?php
class Catalog_Controller_Block_Account_Login extends Controller 
{
	
	public function index($settings)
	{
		$this->language->load('block/account/login');
		
		$type = !empty($settings['type']) ? $settings['type'] : 'header';
		
		switch($type){
			case 'header':
				$this->template->load('block/account/login_header');
				break;
			case 'standard':
				$this->template->load('block/account/login_standard');
				break;
			default:
				trigger_error("Error in ControllerAccountBlockLogin::index(): Invalid template type requested $type");
				return '';
		}
		
		$this->data['action'] = $this->url->link('account/login');
		$this->data['register'] = $this->url->link('account/register');
		$this->data['forgotten'] = $this->url->link('account/forgotten');
		
		$this->data['email'] = !empty($_POST['email']) ? $_POST['email'] : '';
		
		$this->data['redirect'] = $this->url->here();
		
		$this->render();
  	}
}
