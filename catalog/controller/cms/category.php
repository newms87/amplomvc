<?php 
class ControllerCmsCategory extends Controller {  
	public function index() {
		$this->template->load('cms/category');

		$this->language->load('cms/category');
      
		$cms_category_id = isset($_GET['cms_category_id'])?$_GET['cms_category_id']:0;
      
      $path = $this->model_cms_category->getCategoryPath($cms_category_id);
      
      $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      
      foreach($path as $cat_id=>$name){
         $this->breadcrumb->add($name, $this->url->link('cms/category', 'cms_category_id=' . $cat_id));
      }
      
		$category_info = $this->model_cms_category->getCategory($cms_category_id);
	
		if (!$category_info) {
		   $this->url->redirect($this->url->link('cms/category'),302);
      }
      
  		$this->document->setTitle($category_info['title']);
		$this->document->setDescription($category_info['meta_description']);
		$this->document->setKeywords($category_info['meta_keyword']);
		
		$this->language->set('heading_title', $category_info['name']);
      
		$this->data['image'] = $this->image->get($category_info['image']?$category_info['image']:'data/no_image.png');
		
		//$this->data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
		
		$this->data['categories'] = array();
		
		$articles = $this->model_cms_category->getCategoryArticles($cms_category_id, $limit);
		
		foreach ($articles as &$article) {
		   $article['thumb'] = $this->image->resize($article['image'], $this->config->get('config_category_article_image_width'), $this->config->get('config_category_article_image_height'));
		}
      
      $limit = $this->config->get('config_catalog_limit');
		$this->pagination->init();
		$this->pagination->total = count($articles);
		$this->pagination->page = $page;
		$this->pagination->limit = $limit;
		$this->pagination->text = $this->_('text_pagination');
		$this->pagination->url = $this->url->link('cms/category');
	
		$this->data['pagination'] = $this->pagination->render();
	
		$this->data['limit'] = $limit;
	
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
   
   private function get_url($filters=false){
      $url = '';
      $filters = $filters?$filters:array('page');
      foreach($filters as $f)
         if (isset($_GET[$f]))
            $url .= "&$f=" . $_GET[$f];
      return $url;
   }
}
