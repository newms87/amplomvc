<?php 
class ControllerToolTool extends Controller { 
	
	
	public function index() {		
$this->template->load('tool/tool');

		$this->load->language('tool/tool');

		$this->document->setTitle($this->_('heading_title'));
      
      $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      $this->breadcrumb->add($this->_('heading_title'), $this->url->link('tool/tool'));
      
		$this->data['clear_cache'] = $this->url->link('tool/tool/clear_cache');
      
      $this->data['cancel'] = $this->url->link('common/home');
      
      $defaults = array(
         'cache_tables' => ''
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
	
	public function clear_cache() {
	   $this->language->load('tool/tool');
      
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['cache_tables']) && $this->validate()) {
		   
		   $this->cache->delete($_POST['cache_tables']);
			
			$this->message->add('success', $this->_('success_clear_cache'));
		} else {
		   $this->message->add('warning', $this->_('error_clear_cache'));
		}
      
      $this->index();
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'tool/tool')) {
			$this->error['warning'] = $this->_('error_permission');
		}
      
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}		
	}
}
