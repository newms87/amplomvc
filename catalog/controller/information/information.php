<?php 
class ControllerInformationInformation extends Controller {
	public function index() {  
    	$this->language->load('information/information');
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      
		$information_id = isset($_GET['information_id']) ? $_GET['information_id'] : 0;
		
		$information_info = $this->model_catalog_information->getInformation($information_id);
   	
		if ($information_info) {
         $this->template->load('information/information');

	  		$this->document->setTitle($information_info['title']); 
         
         $this->breadcrumb->add($information_info['title'], $this->url->link('information/information', 'information_id=' .  $information_id));
         			
   		$this->language->set('heading_title', $information_info['title']);
      		
			$this->data['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');
      	
			$this->data['continue'] = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : $this->url->link('common/home');

      	$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);
      	
	  		$this->response->setOutput($this->render());
    	} else {
         $this->template->load('error/not_found');

    	   $this->breadcrumb->add($this->_('text_error'), $this->url->link('information/information', 'information_id=' . $information_id));
         
	  		$this->document->setTitle($this->_('text_error'));
			
   		$this->language->set('heading_title', $this->_('text_error'));

   		$this->data['continue'] = $this->url->link('common/home');

			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);
					
	  		$this->response->setOutput($this->render());
    	}
  	}
	
	public function info() {
		if (isset($_GET['information_id'])) {
			$information_id = $_GET['information_id'];
		} else {
			$information_id = 0;
		}      
		
		$information_info = $this->model_catalog_information->getInformation($information_id);

		if ($information_info) {
			$output  = '<html dir="ltr" lang="en">' . "\n";
			$output .= '<head>' . "\n";
			$output .= '  <title>' . $information_info['title'] . '</title>' . "\n";
			$output .= '  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
			$output .= '</head>' . "\n";
			$output .= '<body>' . "\n";
			$output .= '  <h1>' . $information_info['title'] . '</h1>' . "\n";
			$output .= html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8') . "\n";
			$output .= '  </body>' . "\n";
			$output .= '</html>' . "\n";			

			$this->response->setOutput($output);
		}
	}
}