<?php  
class ControllerCommonLogin extends Controller { 
	
	public function index() { 
      $this->template->load('common/login');

    	$this->load->language('common/login');

		$this->document->setTitle($this->_('heading_title'));
		
		//IF user is logged in, redirect to the homepage
      if ($this->user->isLogged()){
			$this->redirect($this->url->link('common/home'));
		}
      
		//if user is not logged in and has provided valid login credentals
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      	if (!empty($_GET['redirect'])) {
      		$this->redirect(urldecode($_GET['redirect']));
			} else {
				$this->redirect($this->url->link('common/home'));
			}
		}
		
      $this->language->format('text_lost', $this->url->store(1, 'home',''));
      $this->language->format('text_are_you_a_designer', $this->url->store(1, 'home','route=information/are_you_a_designer'));
		
		if(isset($this->session->data['token']) && !isset($_COOKIE['token'])){
			$this->error['warning'] = $this->_('error_token');
		}
      
      $this->data['messages'] = $this->message->fetch();
		
    	$defaults = array(
         'username'=>'',
         'password'=>'',
        );
        
      foreach($defaults as $key=>$default){
         $this->data[$key] = isset($_POST[$key]) ? $_POST[$key]:$default;
      }
		
		//If trying to access an admin page, redirect after login
		if (!isset($_GET['redirect'])) {
			if(isset($_GET['route'])){
				$route = $_GET['route'];
				
	         $not_allowed = array(
	            'common/login', 'common/logout'
	         );
	         
	         if(in_array($route, $not_allowed)){
	      		$redirect = urlencode($this->url->link('common/home'));
	         }
				else{
					$redirect = urlencode(preg_replace("/redirect=[^&#]*/",'',$this->url->current_page()));
				}
			}
			else{
				$redirect = urlencode($this->url->link('common/home'));
			}
		}
		else{
			$redirect = $_GET['redirect'];
		}
		
		$this->data['action'] = $this->url->link('common/login', 'redirect=' . $redirect);
      
		$this->data['forgotten'] = $this->url->link('common/forgotten');
	
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
  	}
		
	private function validate() {
		if (isset($_POST['username']) && isset($_POST['password']) && !$this->user->login($_POST['username'], $_POST['password'])) {
			$this->message->add('warning', $this->_('error_login'));
			
         return false;
		}
      
      return true;
	}
}