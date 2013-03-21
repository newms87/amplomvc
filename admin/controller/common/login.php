<?php  
class ControllerCommonLogin extends Controller { 
	
	public function index() { 
      $this->template->load('common/login');

    	$this->load->language('common/login');

		$this->document->setTitle($this->_('heading_title'));
     
      if ($this->user->isLogged() && $this->user->validate_token()){
			$this->redirect($this->url->link('common/home'));
		}
      
      if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      	if (isset($_POST['redirect'])) {
				$this->redirect($_POST['redirect']);
			} else {
				$this->redirect($this->url->link('common/home'));
			}
		}
      
      $this->language->format('text_lost', $this->url->store(1, 'home',''));
      $this->language->format('text_are_you_a_designer', $this->url->store(1, 'home','route=information/are_you_a_designer'));
		
		if ((isset($this->session->data['token']) && !isset($_COOKIE['token'])) || !$this->user->validate_token()) {
			$this->error['warning'] = $this->_('error_token');
		}
         
      $this->data['messages'] = $this->message->fetch();
				
    	$this->data['action'] = $this->url->link('common/login');
      
      $defaults = array(
         'username'=>'',
         'password'=>'',
        );
        
      foreach($defaults as $key=>$default){
         $this->data[$key] = isset($_POST[$key]) ? $_POST[$key]:$default;
      }
		
		if (isset($_GET['route'])) {
			$route = $_GET['route'];
			
         $not_allowed = array(
            'common/login', 'common/logout'
         );
         
         if(in_array($route, $not_allowed)){
            $route = 'common/home';
         } 
         
			unset($_GET['route']);
         
         if(isset($this->session->data['token'])){
            $this->message->add('notify', 'Please log in again.');
            $this->session->end_token_session();
         }
			
			$url = $_GET ? http_build_query($_GET) : '';
			
			$this->data['redirect'] = $this->url->link($route, $url);
		} else {
			$this->data['redirect'] = '';	
		}
	
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