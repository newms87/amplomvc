<?php
class Catalog_Controller_Product_Manufacturer extends Controller 
{
	public function index()
	{
		$this->template->load('product/manufacturer_list');

		$this->language->load('product/manufacturer');
		
		$this->document->setTitle($this->_('heading_title'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_brand'), $this->url->link('product/manufacturer'));
		
		$this->data['categories'] = array();
									
		$results = $this->Model_Catalog_Manufacturer->getManufacturers();
	
		foreach ($results as $result) {
			if (is_numeric(substr($result['name'], 0, 1))) {
				$key = '0 - 9';
			} else {
				$key = substr(strtoupper($result['name']), 0, 1);
			}
			
			if (!isset($this->data['manufacturers'][$key])) {
				$this->data['categories'][$key]['name'] = $key;
			}
			
			$this->data['categories'][$key]['manufacturer'][] = array(
				'name' => $result['name'],
				'href' => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $result['manufacturer_id'])
			);
		}
		
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
	
	public function product()
	{
		$this->language->load('product/manufacturer');
		
		if (isset($_GET['manufacturer_id'])) {
			$manufacturer_id = $_GET['manufacturer_id'];
		} else {
			$manufacturer_id = 0;
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
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));

		$manufacturer_info = $this->Model_Catalog_Manufacturer->getManufacturer($manufacturer_id);
	
		if ($manufacturer_info) {
			$this->template->load('product/product_list');

			$this->document->setTitle($manufacturer_info['name']);
			
			$url = $this->url->get_query('sort','order','page','limit');
						
			$this->breadcrumb->add($manufacturer_info['name'], $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $_GET['manufacturer_id'] . $url));
			
			$this->language->set('heading_title', $manufacturer_info['name']);
			/*
			$this->_('text_compare', (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
			$this->data['compare'] = $this->url->link('product/compare');
			*/
			$this->data['products'] = array();
			
			$data = array(
				'filter_manufacturer_id' => $manufacturer_id,
				'sort'						=> $sort,
				'order'						=> $order,
				'start'						=> ($page - 1) * $limit,
				'limit'						=> $limit
			);
					
			$product_total = $this->Model_Catalog_Product->getTotalProducts($data);
								
			$results = $this->Model_Catalog_Product->getProducts($data);
					
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->image->resize($result['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
				} else {
					$image = false;
				}
				
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id']));
				} else {
					$price = false;
				}
				
				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id']));
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
				
				$result['thumb'] = $image;
				$result['description'] = substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..';
				$result['price'] = $price;
				$result['special'] = $special;
				if (!isset($result['flashsale_id'])) {
					$result['flashsale_id'] = 0;
				}
				$result['tax'] = $tax;
				$result['reviews'] = sprintf($this->_('text_reviews'), (int)$result['reviews']);
				$result['href'] = $this->url->link('product/product', $url . '&manufacturer_id=' . $result['manufacturer_id'] . '&product_id=' . $result['product_id']);
				
				$this->data['products'][] = $result;
			}
					
			$url = '';
			
			if (isset($_GET['limit'])) {
				$url .= '&limit=' . $_GET['limit'];
			}
						
			$this->data['sorts'] = array();
			
			$this->data['sorts'][] = array(
				'text'  => $this->_('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $_GET['manufacturer_id'] . '&sort=p.sort_order&order=ASC' . $url)
			);
			
			$this->data['sorts'][] = array(
				'text'  => $this->_('text_name_asc'),
				'value' => 'p.name-ASC',
				'href'  => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $_GET['manufacturer_id'] . '&sort=p.name&order=ASC' . $url)
			);
	
			$this->data['sorts'][] = array(
				'text'  => $this->_('text_name_desc'),
				'value' => 'p.name-DESC',
				'href'  => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $_GET['manufacturer_id'] . '&sort=p.name&order=DESC' . $url)
			);
	
			$this->data['sorts'][] = array(
				'text'  => $this->_('text_price_asc'),
				'value' => 'p.price-ASC',
				'href'  => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $_GET['manufacturer_id'] . '&sort=p.price&order=ASC' . $url)
			);
	
			$this->data['sorts'][] = array(
				'text'  => $this->_('text_price_desc'),
				'value' => 'p.price-DESC',
				'href'  => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $_GET['manufacturer_id'] . '&sort=p.price&order=DESC' . $url)
			);
			
			if ($this->config->get('config_review_status')) {
				$this->data['sorts'][] = array(
					'text'  => $this->_('text_rating_desc'),
					'value' => 'rating-DESC',
					'href'  => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $_GET['manufacturer_id'] . '&sort=rating&order=DESC' . $url)
				);
				
				$this->data['sorts'][] = array(
					'text'  => $this->_('text_rating_asc'),
					'value' => 'rating-ASC',
					'href'  => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $_GET['manufacturer_id'] . '&sort=rating&order=ASC' . $url)
				);
			}
			
			$this->data['sorts'][] = array(
				'text'  => $this->_('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $_GET['manufacturer_id'] . '&sort=p.model&order=ASC' . $url)
			);
	
			$this->data['sorts'][] = array(
				'text'  => $this->_('text_model_desc'),
				'value' => 'p.model-DESC',
				'href'  => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $_GET['manufacturer_id'] . '&sort=p.model&order=DESC' . $url)
			);
	
			$url = '';
					
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
				'href'  => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $_GET['manufacturer_id'] . $url . '&limit=' . $this->config->get('config_catalog_limit'))
			);
						
			$this->data['limits'][] = array(
				'text'  => 25,
				'value' => 25,
				'href'  => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $_GET['manufacturer_id'] . $url . '&limit=25')
			);
			
			$this->data['limits'][] = array(
				'text'  => 50,
				'value' => 50,
				'href'  => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $_GET['manufacturer_id'] . $url . '&limit=50')
			);
	
			$this->data['limits'][] = array(
				'text'  => 75,
				'value' => 75,
				'href'  => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $_GET['manufacturer_id'] . $url . '&limit=75')
			);
			
			$this->data['limits'][] = array(
				'text'  => 100,
				'value' => 100,
				'href'  => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $_GET['manufacturer_id'] . $url . '&limit=100')
			);
					
			$url = '';
							
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
			$this->data['pagination'] = $this->pagination->render();
			
			$this->data['sort'] = $sort;
			$this->data['order'] = $order;
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
		} else {
			$this->template->load('error/not_found');

			$url = '';
			
			if (isset($_GET['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $_GET['manufacturer_id'];
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
			
			$this->breadcrumb->add($this->_('text_error'), $this->url->link('product/category', $url));
				
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
}