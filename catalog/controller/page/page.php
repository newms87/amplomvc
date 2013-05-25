<?php
class ControllerPagePage extends Controller {
	public function index() {
		$this->template->load('page/page');
		
		$page_id = !empty($_GET['page_id']) ? $_GET['page_id'] : 0;
		
		$page = $this->model_page_page->getPage($page_id);
		
		if(!$page){
			$this->url->redirect("error/not_found");
		}
		
		$this->document->setTitle($page['name']);
		
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
