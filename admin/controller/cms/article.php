<?php    
class ControllerCmsArticle extends Controller { 
	
  
  	public function index() {
		$this->load->language('cms/article');
		
		$this->document->setTitle($this->_('heading_title'));
		 
		$this->getList();
  	}
  
  	public function insert() {
		$this->load->language('cms/article');

    	$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_cms_article->addArticle($_POST);
         
		   $this->message->add('success', $this->_('text_success'));
         
			$url = $this->get_url();
			
			$this->url->redirect($this->url->link('cms/article', $url));
		}
    
    	$this->getForm();
  	} 
   
  	public function update() {
		$this->load->language('cms/article');

    	$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
    	   $article_id = $_GET['article_id'];
			$this->model_cms_article->editArticle($_GET['article_id'], $_POST);

			$this->message->add('success', $this->_('text_success'));

			$url = $this->get_url();
			
			$this->url->redirect($this->url->link('cms/article', $url));
		}
    
    	$this->getForm();
  	}   

  	public function delete() {

		$this->load->language('cms/article');

    	$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $article_id) {
				$this->model_cms_article->deleteArticle($article_id);
			}

			$this->message->add('success', $this->_('text_success'));
			
			$url = $this->get_url();
			
			$this->url->redirect($this->url->link('cms/article', $url));
    	}
	
    	$this->getList();
  	}  
   
   public function list_update() {
      $this->load->language('cms/article');

      $this->document->setTitle($this->_('heading_title'));
      
      if (isset($_POST['selected']) && isset($_GET['action']) && $this->validateModify()) {
         foreach ($_POST['selected'] as $article_id) {
            switch($_GET['action']){
               case 'enable':
                  $this->model_cms_article->updateArticleValue($article_id,'status', 1);
                  break;
               case 'disable':
                  $this->model_cms_article->updateArticleValue($article_id,'status', 0);
                  break;
               case 'date_active':
                  $this->model_cms_article->updateArticleValue($article_id,'date_active', $_GET['action_value']);
                  break; 
               case 'gads':
                  $this->model_cms_article->updateArticleValue($article_id,'gads', $_GET['action_value']);
                  break;
               case 'add_cat':
                  $this->model_cms_article->updateArticleCategory($article_id,'add', $_GET['action_value']);
                  break;
               case 'remove_cat':
                  $this->model_cms_article->updateArticleCategory($article_id,'remove', $_GET['action_value']);
                  break;
               default:
                  $this->error['warning'] = "Invalid Action Selected!";
                  break;
            }
            if($this->error)
               break;
         }
         if(!$this->error){
            $this->message->add('success', $this->_('text_success'));
         }
      }

      $this->getList();
   }

  	private function getList() {
		$this->template->load('cms/article_list');

  	   $sort_list = array('sort'=>'ad.title','order'=>'ASC','page'=>1);
  	   foreach($sort_list as $key=>$default)
         $$key = isset($_GET[$key])?$_GET[$key]:$default;
	   
      $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      $this->breadcrumb->add($this->_('heading_title'), $this->url->link('cms/article'));
      
		$cat_list = $this->model_cms_category->getCategories();
      $categories = array();
      foreach($cat_list as $cat){
         $categories[$cat['cms_category_id']] = $cat['name'];
      }
      $this->data['categories'] = $categories;
      
		//Batch actions
      $this->data['batch_actions'] = array('enable'=>'Enable','disable'=>'Disable', 'date_active'=>'Article Activation Date', 
                                            'add_cat'=>'Add Category', 'remove_cat'=>"Remove Category", 'gads'=>"Show Google Ads?",
                                           );
      $this->data['batch_action_values'] = array('date_active'=>array('#type'=>'text','#default'=>DATETIME_ZERO,'#attrs'=>array('class'=>'datetime')),
                                                  'add_cat'=>array('#type'=>'select','#values'=>$categories),
                                                  'remove_cat'=>array('#type'=>'select','#values'=>$categories),
                                                  'gads'=>array('#type'=>'select','#values'=>$this->_('yes_no'), '#default'=>1)
                                                 );
      
      $url = $this->get_url();                                                 
      
      $this->data['batch_action_go'] = html_entity_decode($this->url->link('cms/article/list_update', 'action=%action%&action_value=%action_value%' . $url));
							
		$this->data['insert'] = $this->url->link('cms/article/insert', $url);
		$this->data['delete'] = $this->url->link('cms/article/delete', $url);	

		$this->data['articles'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$article_total = $this->model_cms_article->getTotalArticles($data);
      
		$articles = $this->model_cms_article->getArticles($data);
    	
    	foreach ($articles as &$article) {
			$action = array();
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('cms/article/update', 'article_id=' . $article['article_id'] . $url)
			);
			
			$article['categories'] = $this->model_cms_article->getArticleCategories($article['article_id']);
			$article['date_active'] == DATETIME_ZERO?"Never":$this->tool->format_datetime($article['date_active'],'M d, Y H:i:s');
         $article['date_expires'] == DATETIME_ZERO?"Never":$this->tool->format_datetime($article['date_expires'],'M d, Y H:i:s');
         $article['selected'] = isset($_POST['selected']) && in_array($article['article_id'], $_POST['selected']);
         $article['action'] = $action;
		}
      
      $this->data['articles'] = $articles;
      
		$url = $order == 'ASC'? '&order=DESC':'&order=ASC';

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}
		
      $sort_by = array('title'=>'ad.title','aid'=>'a.aid','author'=>'a.author','date_active'=>'a.date_active','date_expires'=>'a.date_expires','status'=>'a.status');
      foreach($sort_by as $key=>$s)
         $this->data['sort_'.$key] = $this->url->link('cms/article', 'sort=' . $s . $url);
		
		$url = $this->get_url(array('sort','order'));

		$this->pagination->init();
		$this->pagination->total = $article_total;
		$this->pagination->page = $page;
		$this->pagination->limit = $this->config->get('config_admin_limit');
		$this->pagination->text = $this->_('text_pagination');
		$this->pagination->url = $this->url->link('cms/article', $url . '&page={page}');
			
		$this->data['pagination'] = $this->pagination->render();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
  
  	private function getForm() {
		$this->template->load('cms/article_form');

  	   $article_id = isset($_GET['article_id'])?(int)$_GET['article_id']:null;
      
      $url = $this->get_url();
      
      $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      $this->breadcrumb->add($this->_('heading_title'), $this->url->link('cms/article'));
      		
		if (!$article_id) {
			$this->data['action'] = $this->url->link('cms/article/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('cms/article/update', 'article_id=' . $article_id . $url);
		}
		
      $this->data['cancel'] = $this->url->link('cms/article', $url);
      
      $article_info = array();
    	if ($article_id && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
      	$article_info = $this->model_cms_article->getarticle($article_id);
    	}
      
      $defaults = array('author'=>'',
                        'keyword'=>'',
                        'article_category'=>array(),
                        'article_store'=>array(0,1,2),
                        'article_layout'=>array(),
                        'article_description'=>array(),
                        'article_tag'=>array(),
                        'article_related'=>array(),
                        'image'=>'',
                        'date_active'=>date_format(new DateTime(),'Y-m-d H:i:s'),
                        'date_expires'=>'',
                        'members_only'=>0,
                        'gads'=>1,
                        'syndicator'=>'none',
                        'pagination_type'=>'none',
                        'status'=>0
                        );
      
      foreach($defaults as $d=>$value){
         if (isset($_POST[$d]))
            $this->data[$d] = $_POST[$d];
         elseif (isset($article_info[$d]))
            $this->data[$d] = $article_info[$d];
         elseif(!$article_id)
            $this->data[$d] = $value;
      }
      
      //Get the rest of the article information
      if(!isset($this->data['keyword']))
         $this->data['keyword'] = $this->model_cms_article->getArticleKeyword($article_id);
         
		if (!isset($this->data['article_description']))
			$this->data['article_description'] = $this->model_cms_article->getArticleDescriptions($article_id);
      
      if(!isset($this->data['article_category']))
         $this->data['article_category'] = $this->model_cms_article->getArticleCategories($article_id);
      
      if(!isset($this->data['article_store']))
         $this->data['article_store'] = $this->model_cms_article->getArticleStores($article_id);
      
      if(!isset($this->data['article_layout']))
         $this->data['article_layout'] = $this->model_cms_article->getArticleLayouts($article_id);
      
      if(!isset($this->data['article_tag']))
         $this->data['article_tag'] = $this->model_cms_article->getArticleTags($article_id);
      
      if(!isset($this->data['article_related']))
         $this->data['article_related'] = $this->model_cms_article->getArticleRelated($article_id);
        
		if (!empty($article_info) && $article_info['image'] && file_exists(DIR_IMAGE . $article_info['image'])) {
			$this->data['thumb'] = $this->image->resize($article_info['image'], 100, 100);
		} else {
			$this->data['thumb'] = $this->image->resize('no_image.png', 100, 100);
		}
		
		$this->data['no_image'] = $this->image->resize('no_image.png', 100, 100);
      
      
      $this->data['languages'] = $this->model_localisation_language->getLanguages();
      
      $this->data['categories'] = $this->model_cms_category->getCategories();
      
      $this->data['stores'] = array_merge(array(array('store_id'=>0,'name'=>'Default')),$this->model_setting_store->getStores());
      
      $this->data['layouts'] = $this->model_design_layout->getLayouts();
   
   
   	$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	 
  	private function validateForm() {
    	if (!$this->user->hasPermission('modify', 'cms/article')) {
      		$this->error['warning'] = $this->_('error_permission');
    	}

      foreach($_POST['article_description'] as $lang_id=>$ad){
       	if ((strlen($ad['title']) < 3) || (strlen($ad['title']) > 64)) {
            $this->error["article_description[$lang_id][title]"] = $this->_('error_title');
       	}
      }
      
      if(isset($_POST['keyword'])){
         $keyword =$_POST['keyword']; 
         if(empty($keyword) || is_null($keyword) || preg_match("/[^A-Za-z0-9-]/",$keyword) > 0)
            $this->error['keyword'] = $this->_('error_keyword');
      }
      
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}    

   private function validateModify() {
      if (!$this->user->hasPermission('modify', 'cms/article')) {
         $this->error['warning'] = $this->_('error_permission');
      }  
      
      if (!$this->error) {
         return true;
      } else {
         return false;
      }  
   }

  	private function validateDelete() {
    	if (!$this->user->hasPermission('modify', 'cms/article')) {
			$this->error['warning'] = $this->_('error_permission');
    	}	
      
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}  
   }
   
   public function generate_url(){
      $name = isset($_POST['title'])?$_POST['title']:'';
      if(!$name)return;
      
      $this->load->model("cms/article");
      echo json_encode($this->model_cms_article->generate_url($name));
      exit;
   }
   
   private function get_url($filters=false){
      $url = '';
      $filters = $filters?$filters:array('title','author','date_active','date_expires','status','aid','sort', 'order', 'page');
      foreach($filters as $f)
         if (isset($_GET[$f]))
            $url .= "&$f=" . $_GET[$f];
      return $url;
   }
   
   public function autocomplete(){
      $json = array();
      
      if (isset($_GET['filter_title'])) {
         $data = array(
            'ad.title' => $_GET['filter_title'],
            'sort'     => 'ad.title',
            'order'    => 'ASC',
            'start'    => 0,
            'limit'    => 20
         );
         
         $json = array();
         
         $results = $this->model_cms_article->getArticles($data);
         
         foreach ($results as $result) {
            $json[] = array(
               'article_id'    => $result['article_id'], 
               'title'            => $result['title']
            );    
         }     
      }

      $this->response->setOutput(json_encode($json));
   }
   
   public function xml_importer(){
      
      $aids = array('a17509');
      foreach($aids as $aid){
         //Validate that the article ID is not already in the database, then grab from url.
         $query = $this->db->query("SELECT COUNT(*) as count FROM oc_cms_article WHERE aid='$aid'");
         if($query->row['count'] > 0){
            echo "Article $aid already in DB!<BR>";
            continue;
         }
         echo "Grabbing Article $aid...<br>";
         $url = "http://www1.bettyconfidential.com/ar/export/a/".$aid."_export.xml";
         $xml  = simplexml_load_file($url);
         $article = $this->tool->parse_xml_to_array($xml);
         html_dump($article);
         $data['aid'] = $article['id'][0];
         $data['author'] = $article['author'][0];
         
         $synds = array('Yahoo'=>'yahoo','YellowBrix'=>'yellowbrix', 'YouTube'=>'youtube');
         $data['syndicator'] = isset($synds[$article['syndicator'][0]])?$synds[$article['syndicator'][0]]:'none';
         
         $data['members_only'] = $article['member_only'][0];
         $data['gads'] = 1;//$article['id'][0];
         
         $page_types = array(0=>'none',1=>'image-repeat',2=>'no-image-repeat',3=>'manual');
         $data['pagination_type'] = $page_types[(int)$article['paged'][0]];
         
         $data['status'] = $article['draft'][0] == 'true'?1:0;
         
         $data['date_active'] = DATETIME_ZERO;
         $data['date_expires'] = DATETIME_ZERO;
         
         
         $image_url = $article['first_image_url'][0];
         if($image_url){
            $image = basename($image_url);
            $dir = 'data/articles/'.$image;
            $image_dir = DIR_IMAGE . $dir;
            echo "Downloading Image from $image_url<br>";
            if(!file_put_contents($image_dir, file_get_contents($image_url))){
               echo "Failed to download image...continuing to next article<br>";
               continue;
            }
            $data['image'] = $dir;
         }else{
            echo "No Image<br>";
         }
         
         
         $data['article_description'] = array(1=>array(
                     'title'=>$article['title'][0],
                     'description'=>$article['text'][0],
                     'intro'=>$article['teaser'][0],
                     'meta_description'=>$article['tagline'],
                     'meta_keywords'=>$article['keywords'][0]
                     ));
         
         
         $categories = $this->model_cms_category->getCategories();
         $data['article_category'] = array();
         foreach($categories as $cat){
            if(preg_replace('/[^A-Za-z0-9]/','',strtolower($article['category'][0])) == preg_replace('/[^A-Za-z0-9]/','',strtolower($cat['name']))){
               echo'Found Category ' . $cat['name'] ."<br>";
               $data['article_category'][] = $cat['cms_category_id'];
            }
         }
            
         $data['article_store'] = array(0,1,2);
         $data['article_tag'] = $article['keywords'][0];
         $data['keyword'] = $this->model_cms_article->generate_url($article['title'][0]);
         
         $user_created = $article['user_created'][0];
         $user_modified = $article['user_last_edited'][0];
         echo "DONE<br>";
         html_dump($data);
         exit;
         $this->model_cms_article->addArticle($data);
         $query = $this->db->query("SELECT article_id FROM " . DB_PREFIX . "cms_article WHERE aid='$data[aid]'");
         $article_id = $query->row['article_id'];
         $this->db->query("UPDATE " . DB_PREFIX . "cms_article SET user_created='$user_created', user_modified='$user_modified', date_created='$date_created', date_modified='$date_modified' WHERE article_id='$article_id'");
      }
   }
}
