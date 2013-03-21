<?php 
class ControllerSalesFlashsale extends Controller {  
	public function index() {
      $flashsale_id = isset($_GET['flashsale_id'])?$_GET['flashsale_id']:false;
      
      $this->language->load("sales/flashsale");
      
      $this->document->setTitle($this->_('heading_title'));
      
      if($flashsale_id){
         $this->template->load('sales/flashsale');
         
         $flashsale = $this->model_catalog_flashsale->getFlashsale($flashsale_id);
         
         if(!$flashsale || $this->model_catalog_flashsale->getStatus($flashsale) != 'active')
            $this->redirect($this->url->link('sales/flashsale'), 302);
         
         if( ! (isset($_GET['preview_flashsale']) && $this->user->canPreview('flashsale')) ){
            $this->model_catalog_flashsale->updateViewed($flashsale_id);
         }
         
         $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
         $this->breadcrumb->add($this->_('heading_title'), $this->url->link('sales/flashsale'));
         $this->breadcrumb->add($flashsale['name'], $this->url->link('sales/flashsale', 'flashsale_id=' . $flashsale_id));
         
         $this->document->setTitle(ucfirst($flashsale['name']));
         
         $this->language->set('heading_title', ucfirst($flashsale['name']));
         
         $this->data['d_sort_by'] = $d_sort_by = isset($_GET['d_sort_by'])?$_GET['d_sort_by']:null;
         
         
         //OPTIONAL VERSION WHERE FLASHSALE PAGE REMAINS SHOWING FLASHSALE OVER
         if($this->model_catalog_flashsale->getStatus($flashsale) != 'active'){
            $designers = $this->model_catalog_flashsale->getFlashsaleDesigners($flashsale_id);
            foreach($designers as &$d)
               $d['href'] = $this->url->site($d['keyword']);
            $this->data['designers'] = $designers;
            
            $this->language->set('sale_over_text', $this->_('flashsale_over_text'));
            $this->data['continue'] = $this->url->link('common/home');
         }
         else{
            $this->data['the_page'] = $_SERVER['REQUEST_URI'];
            
            $this->model_catalog_flashsale->activateFlashsaleDesigners($flashsale_id);
            
   		   $products = $this->model_catalog_flashsale->getFlashsaleProducts($flashsale, $d_sort_by);
            
            $article_insert = array();
            $articles = array();
            $article_list = $this->model_catalog_flashsale->getFlashsaleArticles($flashsale_id);
            
            
            //This specifies the location of the articles by number inserted 
            $art_loc = array(
               0=>array('from'=>1,'to'=>min(5,count($products)-1)),
               1=>array('from'=>3,'to'=>count($products)-1),
            );
            
            $count = 0; 
            foreach($article_list as $a){
               $articles[$a['flashsale_article_id']] = $a;
               $articles[$a['flashsale_article_id']]['description'] = html_entity_decode($a['description']);
               
               $location = isset($art_loc[$count])?rand($art_loc[$count]['from'],$art_loc[$count]['to']):rand(3,($first?$first:count($products)-1));
               
               $location = array_key_exists($location, $article_insert)?$location+1:$location;
               
               $article_insert[$location] = $a['flashsale_article_id'];
               
               $count++;
            }

            $sections = array(0=>array('section_name'=>'All','products'=>array()));
            $sect_id = 0;
            
            $count = 0;
            foreach($products as $p){
               if($sect_id !== (int)$p['section_id']){
                  //if we are sorting by something, do not add products to different sections,
                  //just jumble them together under the 0=>'All' section.
                  if(!$d_sort_by)
                     $sect_id = (int)$p['section_id'];
                  //But we need to keep the section ids/names for the product attribute filter at the top of the page.
                  $sections[(int)$p['section_id']] = array('section_name'=>$p['section_name'], 'products'=>array());
               }
               
               //insert articles between products at the specified random location
               if(array_key_exists($count,$article_insert)){
                  $sections[$sect_id]['products']['article-'.$count] = $articles[$article_insert[$count]];
                  unset($article_insert[$count]);
               }
               $count++;
               
               $sections[$sect_id]['products'][$p['product_id']] = $p;
               $sections[$sect_id]['products'][$p['product_id']]['name'] = $this->tool->limit_characters($p['name'],50);
               $sections[$sect_id]['products'][$p['product_id']]['price'] = $this->currency->format($p['price']);
               $sections[$sect_id]['products'][$p['product_id']]['special'] = (int)$p['special'] > 0 ?$this->currency->format($p['special']):null;
               $sections[$sect_id]['products'][$p['product_id']]['href'] = $this->url->link('product/product','product_id='.$p['product_id'].'&flashsale_id='.$flashsale_id);
               $sections[$sect_id]['products'][$p['product_id']]['image'] = $this->image->get($p['image']);
               $sections[$sect_id]['products'][$p['product_id']]['thumb'] = $this->image->resize($p['image'],$this->config->get('config_image_category_width'),$this->config->get('config_image_category_height'));
            }
            
            while(!empty($article_insert)){
               $sections[$sect_id]['products'][] = $articles[array_pop($article_insert)];
            }
            uasort($sections,function($a,$b){if($a=='All'||$a>$b)return 1;});
            $this->data['section_products'] = $sections;
            
            $this->data['open_quote'] = $this->image->get('data/open_quote.png');
            $this->data['close_quote'] = $this->image->get('data/close_quote.png');
            
            $this->data['description'] = html_entity_decode($flashsale['blurb']);
            $this->data['flashsale_image'] = $this->image->resize($flashsale['image'], $this->config->get('config_image_manufacturer_width'),$this->config->get('config_image_manufacturer_height'));
            
            $this->data['flashsale_clock'] = $this->image->get('data/clock.png');
            $this->data['flashsale_link'] = $this->url->link('sales/flashsale','flashsale_id='.$flashsale_id);
            
            $this->data['num_cols'] = 3;
            
            $this->data['filter'] = isset($_GET['filter'])?$_GET['filter']:0;
            
            $this->data['sort_url'] = preg_replace("/\?.*/","",$this->data['the_page']);
            $this->data['sort_list'] = array('pd.name ASC'=>'Sort A-Z', 'pd.name DESC'=>'Sort Z-A',
                                             'price ASC'=>'Lowest Price', 'price DESC'=>'Highest Price');
                                             
            $this->data['share_status'] = $this->config->get('config_share_status');
         }
      }
      else{
         $this->template->load('sales/flashsales');
         
         $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
         $this->breadcrumb->add($this->_('heading_title'), $this->url->link('sales/flashsale'));
                        
         $flashsales = $this->model_catalog_flashsale->getFlashsales();
         
         if(empty($flashsales)){
            $this->data['continue'] = $this->url->link('common/home');
         }
         else{
            $this->language->set('flashsale_heading', $this->_('heading_title'));
            $this->data['polaroids'] = array(
                     $this->image->resize('data/polaroid-1.png', 260,283),
                     $this->image->resize('data/polaroid-2.png',253,283),
                     $this->image->resize('data/polaroid-3.png', 250,283)
                  );
            $this->data['polaroid_3_back'] = $this->image->get('polaroid-3-back.png');
            $this->data['fs_tac'] = $this->image->resize('data/pink_tac.png', 36,52);

            foreach($flashsales as &$fs){
               $fs['image'] =$this->image->resize( (isset($fs['image'])?$fs['image']:"no_image.jpg") ,196,196);
               $fs['href'] = $this->url->site($fs['keyword']);
            }
         }
         
         $this->data['flashsales'] = $flashsales;
      }
		
		$this->data['flashsale_id'] = $flashsale_id;
      
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

   public function ajax_countdown(){
      if(!isset($_POST['flashsales']))return;
      
      $flashsales = array();
      foreach($_POST['flashsales'] as $fs){
         $flashsales[] = array('id'=>$fs['id'],'countdown'=>$this->countdown((int)$fs['flash_id'],isset($fs['type'])?$fs['type']:null, isset($fs['msg_start'])?$fs['msg_start']:null));
      }
      echo json_encode($flashsales);
      exit;
   }
   
   public function countdown($flashsale=null, $type='long', $msg_start=false){
      $msg_start = trim((string)$msg_start);
      if(is_integer($flashsale)){
         $flashsale = $this->model_catalog_flashsale->getFlashsale($flashsale);
      }
      
      //problem finding flashsale or no flashsale specified
      if(!$flashsale)return false;
      
      $diff_start =  date_diff(date_create(), date_create($flashsale['date_start']));
      $diff_end =  date_diff(date_create(), date_create($flashsale['date_end']));
      
      switch($type){
         case 'letters':
            $text_hour = 'H';
            $text_min = 'M';
            $text_sec = 'S';
            $plural = '';
            break;
         case 'short':
            $text_hour = 'hour';
            $text_min = 'min';
            $text_sec = 'day';
            $plural = 's';
            break;
         default:
            $text_hour = 'hour';
            $text_min = 'minute';
            $text_sec = 'second';
            $plural = 's';
      }
      
      if(!$diff_start->invert){
         $msg = "<span class='msg_start'>" . (($msg_start && !empty($msg_start))?$msg_start:"Sale starts in ") . "</span>";
         $class='start_soon';
         $diff_end->d = -$diff_start->d;
         $diff_end->h = -$diff_start->h;
         $diff_end->i = $diff_start->i;
         $diff_end->s = $diff_start->s;
      }
      else if(!$diff_end->invert){
         $msg = "<span class='msg_start'>" . (($msg_start && !empty($msg_start))?$msg_start:"Ends in ")."</span>";
         $class='normal';
      }
      else{
         $msg = "<span class='msg_start'>Sale has Ended.</span>";
         $class='ended';
      } 
      
      $diff_end->h = $diff_end->h + ((int)$diff_end->d)*24;
      $time = '';
      if($type == 'letters'){
         $h = 
         $time .= "<span class='fs_num'>". sprintf('%02d',$diff_end->h) . "</span>" . $text_hour . ($diff_end->h!=1?$plural:'');
         $time .= "<span class='fs_num'>". sprintf('%02d',$diff_end->i) . "</span>" . $text_min . ($diff_end->i!=1?$plural:'');
         $time .= "<span class='fs_num'>". sprintf('%02d',$diff_end->s) . "</span>" . $text_sec . ($diff_end->s!=1?$plural:'');
      }
      else{
         if($diff_end->h > 0){
            $time .= "<span class='fs_num'>$diff_end->h</span>" . $text_hour . ($diff_end->h!=1?$plural:'');
            $time .= "<span class='fs_num'>$diff_end->i</span>" . $text_min . ($diff_end->i!=1?$plural:'');
         }
         else{
            $time .= "<span class='fs_num'>$diff_end->i</span>" . $text_min . ($diff_end->i!=1?$plural:'');
            $time .= "<span class='fs_num'>$diff_end->s</span>" . $text_sec . ($diff_end->s!=1?$plural:'');
            $class='ending_soon';
         }
      }
      
      return "<div id='countdown_msg' class='$class'>$msg $time</div>";
      
   }
}
