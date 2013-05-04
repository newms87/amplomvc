<?php   
class ControllerErrorNotFound extends Controller {
	public function index() {
		$this->template->load('error/not_found');

		$this->language->load('error/not_found');
		
		$this->document->setTitle($this->_('heading_title'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
				
		if (isset($_GET['route'])) {
		   $this->breadcrumb->add($this->_('text_error'), $this->url->link($_GET['route']));
		}
		
		$this->response->addHeader($_SERVER['SERVER_PROTOCOL'] . '/1.1 404 Not Found');
		
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