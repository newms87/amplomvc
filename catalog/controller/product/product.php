<?php  
class ControllerProductProduct extends Controller {
	 
	public function index() {
	   
		$this->language->load('product/product');
      
	   $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : 0;
      
      $product_info = $this->model_catalog_product->getProduct($product_id);
      
      $this->data['product_info'] = $product_info;
      
      if ($product_info) {
         
         $this->data['product_id'] = $product_id;
         
         //Build Breadcrumbs
   		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
         
         $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);
         
			if ($manufacturer_info){
			   $this->breadcrumb->add($manufacturer_info['name'], $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $product_info['manufacturer_id'])); 
         }
   
   		if (isset($product_info['flashsale_id'])) {
   		   $flashsale_info = $this->model_catalog_flashsale->getFlashsale($product_info['flashsale_id']);
            
            if ($flashsale_info){
               $this->breadcrumb->add($flashsale_info['name'], $this->url->link('sales/flashsale', 'flashsale_id=' . $product_info['flashsale_id']));
            }
         }

			$this->breadcrumb->add($product_info['name'], $this->url->link('product/product', 'product_id=' . $product_info['product_id']));
         
         //Setup Document
			$this->document->setTitle($product_info['name']);
			$this->document->setDescription($product_info['meta_description']);
			$this->document->setKeywords($product_info['meta_keyword']);
			
         $this->language->set('heading_title', $product_info['name']);
         
         if($product_info['template']){
            $this->template->load('product/' . $product_info['template']);
         }
         else{
            $this->template->load('product/product');
         }
         
         //Product Flashsale
         if(isset($flashsale_info) && $flashsale_info){
            $this->data['block_product_flashsale_countdown'] = $this->getBlock('product', 'flashsale_countdown', array($flashsale_info));
         }
         
         //Product Images
			$this->data['block_product_images'] = $this->getBlock('product', 'images', array($product_info));
         
         //Product Information
         $this->data['block_product_information'] = $this->getBlock('product', 'information', array($product_info));
         
         //Additional Information
         $this->data['block_product_additional'] = $this->getBlock('product', 'additional', array($product_info));
         
         //Find the related products
			$this->data['block_product_related'] = $this->getBlock('product', 'related', array($product_id));
			
			//The Tags associated with this product		
			$tags = $this->model_catalog_product->getProductTags($product_info['product_id']);
			
			foreach ($tags as &$tag) {
				$tag['href'] = $this->url->link('product/search', 'filter_tag=' . $tag['tag']);
			}
         
			$this->language->format('text_on_store', $this->config->get('config_name'));
			
         $this->data['tags'] = $tags;
         
         if($product_info['template'] == 'product_video'){
            $this->data['description'] = html_entity_decode($product_info['description']);
         }
			
         $this->model_catalog_product->updateViewed($product_info['product_id']);
		} else {
         $this->url->redirect($this->url->link('error/not_found'));
    	}
      
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

	public function review() {
		$this->template->load('product/review');

    	$this->language->load('product/product');

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}  
		
		$this->data['reviews'] = array();
		
		$review_total = $this->model_catalog_review->getTotalReviewsByProductId($_GET['product_id']);
			
		$results = $this->model_catalog_review->getReviewsByProductId($_GET['product_id'], ($page - 1) * 5, 5);
      		
		foreach ($results as $result) {
        	$this->data['reviews'][] = array(
        		'author'     => $result['author'],
				'text'       => $result['text'],
				'rating'     => (int)$result['rating'],
        		'reviews'    => sprintf($this->_('text_reviews'), (int)$review_total),
        		'date_added' => $this->tool->format_datetime($result['date_added'], $this->language->getInfo('date_format_short')),
        	);
      	}			
			
		$this->pagination->init();
		$this->pagination->total = $review_total;
		$this->pagination->page = $page;
		$this->pagination->limit = 5; 
		$this->pagination->text = $this->_('text_pagination');
		$this->pagination->url = $this->url->link('product/product/review', 'product_id=' . $_GET['product_id']);
			
		$this->data['pagination'] = $this->pagination->render();
		






		$this->response->setOutput($this->render());
	}
	
	public function write() {
		$this->language->load('product/product');
		
		$json = array();
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 25)) {
				$json['error'] = $this->_('error_name');
			}
			
			if ((strlen($_POST['text']) < 25) || (strlen($_POST['text']) > 1000)) {
				$json['error'] = $this->_('error_text');
			}
	
			if (!$_POST['rating']) {
				$json['error'] = $this->_('error_rating');
			}
	
			if (empty($this->session->data['captcha']) || ($this->session->data['captcha'] != $_POST['captcha'])) {
				$json['error'] = $this->_('error_captcha');
			}
				
			if (!isset($json['error'])) {
				$this->model_catalog_review->addReview($_GET['product_id'], $_POST);
				
				$json['success'] = $this->_('text_success');
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function captcha() {
		$this->session->data['captcha'] = $this->captcha->getCode();
		
		$this->captcha->showImage();
	}
	
	public function upload() {
		$this->language->load('product/product');
		
		$json = array();
		
		if (!empty($_FILES['file']['name'])) {
			$filename = basename(html_entity_decode($_FILES['file']['name'], ENT_QUOTES, 'UTF-8'));
			
			if ((strlen($filename) < 3) || (strlen($filename) > 128)) {
        		$json['error'] = $this->_('error_filename');
	  		}	  	
			
			$allowed = array();
			
			$filetypes = explode(',', $this->config->get('config_upload_allowed'));
			
			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}
			
			if (!in_array(substr(strrchr($filename, '.'), 1), $allowed)) {
				$json['error'] = $this->_('error_filetype');
       		}	
						
			if ($_FILES['file']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->_('error_upload_' . $_FILES['file']['error']);
			}
		} else {
			$json['error'] = $this->_('error_upload');
		}
		
		if (!$json) {
			if (is_uploaded_file($_FILES['file']['tmp_name']) && file_exists($_FILES['file']['tmp_name'])) {
				$file = basename($filename) . '.' . md5(rand());
				
				// Hide the uploaded file name so people can not link to it directly.
				$json['file'] = $this->encryption->encrypt($file);
				
				move_uploaded_file($_FILES['file']['tmp_name'], DIR_DOWNLOAD . $file);
			}
						
			$json['success'] = $this->_('text_upload');
		}	
		
		$this->response->setOutput(json_encode($json));		
	}
}
