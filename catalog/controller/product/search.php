<?php 
class ControllerProductSearch extends Controller { 	
	public function index() { 
		$this->template->load('product/search');

    	$this->language->load('product/search');
		
		if (isset($_GET['filter_name'])) {
			$filter_name = $_GET['filter_name'];
		} else {
			$filter_name = '';
		} 
		
		if (isset($_GET['filter_tag'])) {
			$filter_tag = $_GET['filter_tag'];
		} elseif (isset($_GET['filter_name'])) {
			$filter_tag = $_GET['filter_name'];
		} else {
			$filter_tag = '';
		} 
				
		if (isset($_GET['filter_description'])) {
			$filter_description = $_GET['filter_description'];
		} else {
			$filter_description = '';
		} 
				
		if (isset($_GET['filter_category_id'])) {
			$filter_category_id = $_GET['filter_category_id'];
		} else {
			$filter_category_id = 0;
		} 
		
		if (isset($_GET['filter_sub_category'])) {
			$filter_sub_category = $_GET['filter_sub_category'];
		} else {
			$filter_sub_category = '';
		} 
								
		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'p.sort_order';
		} 

		if (isset($_GET['order'])) {
			$order = $_GET['order'];
		} else {
			$order = 'ASC';
		}
  		
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
				
		if (isset($_GET['limit'])) {
			$limit = $_GET['limit'];
		} else {
			$limit = $this->config->get('config_catalog_limit');
		}
		
		if (isset($_GET['keyword'])) {
			$this->document->setTitle($this->_('heading_title') .  ' - ' . $_GET['keyword']);
		} else {
			$this->document->setTitle($this->_('heading_title'));
		}
      
      $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));

		$url = '';
		
		if (isset($_GET['filter_name'])) {
			$url .= '&filter_name=' . $_GET['filter_name'];
		}
		
		if (isset($_GET['filter_tag'])) {
			$url .= '&filter_tag=' . $_GET['filter_tag'];
		}
				
		if (isset($_GET['filter_description'])) {
			$url .= '&filter_description=' . $_GET['filter_description'];
		}
				
		if (isset($_GET['filter_category_id'])) {
			$url .= '&filter_category_id=' . $_GET['filter_category_id'];
		}
		
		if (isset($_GET['filter_sub_category'])) {
			$url .= '&filter_sub_category=' . $_GET['filter_sub_category'];
		}
		
		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}	

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}
				
		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}	
		
		if (isset($_GET['limit'])) {
			$url .= '&limit=' . $_GET['limit'];
		}
		
      $this->breadcrumb->add($this->_('heading_title'), $this->url->link('product/search', $url));
      
		$this->language->format('text_compare', (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
		$this->data['compare'] = $this->url->link('product/compare');
		
		// 3 Level Category Search
		$this->data['categories'] = array();
					
		$categories_1 = $this->model_catalog_category->getCategories(0);
		
		foreach ($categories_1 as $category_1) {
			$level_2_data = array();
			
			$categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);
			
			foreach ($categories_2 as $category_2) {
				$level_3_data = array();
				
				$categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);
				
				foreach ($categories_3 as $category_3) {
					$level_3_data[] = array(
						'category_id' => $category_3['category_id'],
						'name'        => $category_3['name'],
					);
				}
				
				$level_2_data[] = array(
					'category_id' => $category_2['category_id'],	
					'name'        => $category_2['name'],
					'children'    => $level_3_data
				);					
			}
			
			$this->data['categories'][] = array(
				'category_id' => $category_1['category_id'],
				'name'        => $category_1['name'],
				'children'    => $level_2_data
			);
		}
		
		$this->data['products'] = array();
		
		if (isset($_GET['filter_name']) || isset($_GET['product_tag'])) {
			$data = array(
				'filter_name'         => $filter_name, 
				'product_tag'          => $filter_tag, 
				'filter_description'  => $filter_description,
				'filter_category_id'  => $filter_category_id, 
				'filter_sub_category' => $filter_sub_category, 
				'sort'                => $sort,
				'order'               => $order,
				'start'               => ($page - 1) * $limit,
				'limit'               => $limit
			);
					
			$product_total = $this->model_catalog_product->getTotalProducts($data);
								
			$results = $this->model_catalog_product->getProducts($data);
					
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				} else {
					$image = false;
				}
				
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_show_price_with_tax')));
				} else {
					$price = false;
				}
				
				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_show_price_with_tax')));
				} else {
					$special = false;
				}	
				
				if ($this->config->get('config_show_price_with_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price']);
				} else {
					$tax = false;
				}				
				
				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}
			
				$this->data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'rating'      => $result['rating'],
					'reviews'     => sprintf($this->_('text_reviews'), (int)$result['reviews']),
					'href'        => $this->url->link('product/product', $url . '&product_id=' . $result['product_id'])
				);
			}
					
			$url = '';
			
			if (isset($_GET['filter_name'])) {
				$url .= '&filter_name=' . $_GET['filter_name'];
			}
			
			if (isset($_GET['filter_tag'])) {
				$url .= '&filter_tag=' . $_GET['filter_tag'];
			}
					
			if (isset($_GET['filter_description'])) {
				$url .= '&filter_description=' . $_GET['filter_description'];
			}
			
			if (isset($_GET['filter_category_id'])) {
				$url .= '&filter_category_id=' . $_GET['filter_category_id'];
			}
			
			if (isset($_GET['filter_sub_category'])) {
				$url .= '&filter_sub_category=' . $_GET['filter_sub_category'];
			}
					
			if (isset($_GET['limit'])) {
				$url .= '&limit=' . $_GET['limit'];
			}
						
			$this->data['sorts'] = array();
			
			$this->data['sorts'][] = array(
				'text'  => $this->_('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('product/search', 'sort=p.sort_order&order=ASC' . $url)
			);
			
			$this->data['sorts'][] = array(
				'text'  => $this->_('text_name_asc'),
				'value' => 'pd.name-ASC',
				'href'  => $this->url->link('product/search', 'sort=pd.name&order=ASC' . $url)
			); 
	
			$this->data['sorts'][] = array(
				'text'  => $this->_('text_name_desc'),
				'value' => 'pd.name-DESC',
				'href'  => $this->url->link('product/search', 'sort=pd.name&order=DESC' . $url)
			);
	
			$this->data['sorts'][] = array(
				'text'  => $this->_('text_price_asc'),
				'value' => 'p.price-ASC',
				'href'  => $this->url->link('product/search', 'sort=p.price&order=ASC' . $url)
			); 
	
			$this->data['sorts'][] = array(
				'text'  => $this->_('text_price_desc'),
				'value' => 'p.price-DESC',
				'href'  => $this->url->link('product/search', 'sort=p.price&order=DESC' . $url)
			); 
			
			if ($this->config->get('config_review_status')) {
				$this->data['sorts'][] = array(
					'text'  => $this->_('text_rating_desc'),
					'value' => 'rating-DESC',
					'href'  => $this->url->link('product/search', 'sort=rating&order=DESC' . $url)
				); 
				
				$this->data['sorts'][] = array(
					'text'  => $this->_('text_rating_asc'),
					'value' => 'rating-ASC',
					'href'  => $this->url->link('product/search', 'sort=rating&order=ASC' . $url)
				);
			}
			
			$this->data['sorts'][] = array(
				'text'  => $this->_('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('product/search', 'sort=p.model&order=ASC' . $url)
			); 
	
			$this->data['sorts'][] = array(
				'text'  => $this->_('text_model_desc'),
				'value' => 'p.model-DESC',
				'href'  => $this->url->link('product/search', 'sort=p.model&order=DESC' . $url)
			);
	
			$url = '';
			
			if (isset($_GET['filter_name'])) {
				$url .= '&filter_name=' . $_GET['filter_name'];
			}
			
			if (isset($_GET['filter_tag'])) {
				$url .= '&filter_tag=' . $_GET['filter_tag'];
			}
					
			if (isset($_GET['filter_description'])) {
				$url .= '&filter_description=' . $_GET['filter_description'];
			}
			
			if (isset($_GET['filter_category_id'])) {
				$url .= '&filter_category_id=' . $_GET['filter_category_id'];
			}
			
			if (isset($_GET['filter_sub_category'])) {
				$url .= '&filter_sub_category=' . $_GET['filter_sub_category'];
			}
						
			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}	
	
			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}
			
			$this->data['limits'] = array();
			
			$this->data['limits'][] = array(
				'text'  => $this->config->get('config_catalog_limit'),
				'value' => $this->config->get('config_catalog_limit'),
				'href'  => $this->url->link('product/search', $url . '&limit=' . $this->config->get('config_catalog_limit'))
			);
						
			$this->data['limits'][] = array(
				'text'  => 25,
				'value' => 25,
				'href'  => $this->url->link('product/search', $url . '&limit=25')
			);
			
			$this->data['limits'][] = array(
				'text'  => 50,
				'value' => 50,
				'href'  => $this->url->link('product/search', $url . '&limit=50')
			);
	
			$this->data['limits'][] = array(
				'text'  => 75,
				'value' => 75,
				'href'  => $this->url->link('product/search', $url . '&limit=75')
			);
			
			$this->data['limits'][] = array(
				'text'  => 100,
				'value' => 100,
				'href'  => $this->url->link('product/search', $url . '&limit=100')
			);
					
			$url = '';
	
			if (isset($_GET['filter_name'])) {
				$url .= '&filter_name=' . $_GET['filter_name'];
			}
			
			if (isset($_GET['filter_tag'])) {
				$url .= '&filter_tag=' . $_GET['filter_tag'];
			}
					
			if (isset($_GET['filter_description'])) {
				$url .= '&filter_description=' . $_GET['filter_description'];
			}
			
			if (isset($_GET['filter_category_id'])) {
				$url .= '&filter_category_id=' . $_GET['filter_category_id'];
			}
			
			if (isset($_GET['filter_sub_category'])) {
				$url .= '&filter_sub_category=' . $_GET['filter_sub_category'];
			}
										
			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}	
	
			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}
			
			if (isset($_GET['limit'])) {
				$url .= '&limit=' . $_GET['limit'];
			}
					
			$this->pagination->init();
			$this->pagination->total = $product_total;
			$this->pagination->page = $page;
			$this->pagination->limit = $limit;
			$this->pagination->text = $this->_('text_pagination');
			$this->pagination->url = $this->url->link('product/search', $url . '&page={page}');
			
			$this->data['pagination'] = $this->pagination->render();
		}	
		
		$this->data['filter_name'] = $filter_name;
		$this->data['filter_description'] = $filter_description;
		$this->data['filter_category_id'] = $filter_category_id;
		$this->data['filter_sub_category'] = $filter_sub_category;
				
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['limit'] = $limit;
		






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