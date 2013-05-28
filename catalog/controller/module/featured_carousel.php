<?php
class ControllerModuleFeaturedCarousel extends Controller {
	public function index($setting=null) {
		//$this->template->load('module/featured_carousel');
		
		//$this->language->load('module/featured_carousel');
		
		if(!$setting){
			$fc = $this->model_setting_setting->getSetting('featured_carousel');
			$products = $fc['featured_carousel_list'];
		}
		
		empty($setting['limit'])?$setting['limit']=3:'';
		
		$product_list = array();
		foreach($products as $product_id=>$item){
			$product = $this->model_catalog_product->getProduct($product_id);
			//$flashsale = $this->model_catalog_product->getProductFlashsale($product_id);
			
			if($product){
				$product_list[] = array(
					'product_id' => $product_id,
					'title'		=> $item['name'],
					'retail'	=> $this->currency->format($product['price'], '', '', true, 0),
					'price'		=> $this->currency->format($product['special'] ? $product['special'] : $product['price'], '', '', true, 0),
					'image'		=> $this->image->resize($item['image'], 98, 76),
					'href'		=> $this->url->link('product/product', 'product_id='.$product_id),
				);
			}
		}
		
		echo json_encode($product_list);
		exit;
		
		$this->render();
	}

	public function cron(){
		$featured_carousel = $this->model_setting_setting->getSetting('featured_carousel');
		
		$flashsales = $this->model_catalog_flashsale->getFlashsales('', 'fs.date_end DESC');
		
		$default_exclude = array(370,371,373,374);
		
		//The carousel ad on magazine
		$products = array();
		$exclude_list = $default_exclude;
		
		if($flashsales){
			$flashsale = current($flashsales);
			
			$query = $this->db->query("SELECT p.product_id, pd.name, p.image FROM " . DB_PREFIX . "flashsale_product fp LEFT JOIN " . DB_PREFIX . "product p ON(p.product_id = fp.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id) WHERE fp.flashsale_id = '$flashsale[flashsale_id]' LIMIT 1");
			
			if($query->num_rows){
				$products[$query->row['product_id']] = $query->row;
				$exclude_list[] = $query->row['product_id'];
			}
		}
		
		$limit = isset($featured_carousel['featued_carousel_limit']) ? $featured_carousel['featued_carousel_limit'] : 6;
		
		if(count($products) < $limit){
			$limit -= count($products);
			
			$exclude = !empty($exclude_list) ? "WHERE product_id NOT IN (" . implode(',', $exclude_list) . ")" : '';
			
			$query = $this->db->query("SELECT p.product_id, pd.name, p.image FROM (SELECT * FROM " . DB_PREFIX . "product $exclude ORDER BY RAND()) as p LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (m.manufacturer_id = p.manufacturer_id) WHERE m.status = '1' AND p.status = '1' GROUP BY p.manufacturer_id ORDER BY RAND() LIMIT $limit");
			
			foreach($query->rows as $row){
				$products[$row['product_id']] = $row;
			}
		}
		
		$featured_carousel['featured_carousel_list'] = $products;
		
		//The Shop Main ad on the magazine site
		$products = array();
		$exclude_list = $default_exclude;
		
		if($flashsales){
			foreach($flashsales as $flashsale){
				$query = $this->db->query("SELECT p.product_id, pd.name, p.image FROM " . DB_PREFIX . "flashsale_product fp LEFT JOIN " . DB_PREFIX . "product p ON(p.product_id = fp.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id) WHERE fp.flashsale_id = '$flashsale[flashsale_id]' LIMIT 1");
				
				if($query->num_rows){
					$products[$query->row['product_id']] = $query->row;
					$exclude_list[] = $query->row['product_id'];
				}
			}
		}
		
		$limit = isset($featured_carousel['featued_product_limit']) ? $featured_carousel['featued_product_limit'] : 12;
		
		if(count($products) < $limit){
			$limit -= count($products);
			
			$exclude = !empty($exclude_list) ? "WHERE product_id NOT IN (" . implode(',', $exclude_list) . ")" : '';
			
			$query = $this->db->query("SELECT p.product_id, pd.name, p.image FROM (SELECT * FROM " . DB_PREFIX . "product $exclude ORDER BY RAND()) as p LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (m.manufacturer_id = p.manufacturer_id) WHERE m.status = '1' AND p.status = '1' GROUP BY p.manufacturer_id ORDER BY RAND() LIMIT $limit");
			
			foreach($query->rows as $row){
				$products[$row['product_id']] = $row;
			}
		}
		
		$featured_carousel['featured_product_list'] = $products;
		
		$limit = $this->model_setting_setting->editSetting('featured_carousel', $featured_carousel);
	}
}