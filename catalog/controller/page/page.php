<?php
class ControllerPagePage extends Controller {
	public function index() {
		$this->template->load('page/page');
		
		if(empty($_GET['page_id'])){
			$this->url->redirect("error/not_found");
		}
		
		$page = $this->model_page_page->getPage($_GET['page_id']);
		
		if(!$page){
			$this->url->redirect("error/not_found");
		}
		
		$this->config->set('config_layout_id', $page['layout_id']);
		
		$this->data['content'] = html_entity_decode($page['content']);
		
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
