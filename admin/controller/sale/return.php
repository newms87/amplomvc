<?php    
class ControllerSaleReturn extends Controller { 
	
   
  	public function index() {
		$this->load->language('sale/return');
		 
		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
  	}
  
  	public function insert() {
		$this->load->language('sale/return');

    	$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
      	  	$this->model_sale_return->addReturn($_POST);
			
			$this->message->add('success', $this->_('text_success'));
		  
			$url = '';
			
			if (isset($_GET['filter_return_id'])) {
				$url .= '&filter_return_id=' . $_GET['filter_return_id'];
			}
			
			if (isset($_GET['filter_order_id'])) {
				$url .= '&filter_order_id=' . $_GET['filter_order_id'];
			}
			
			if (isset($_GET['filter_customer'])) {
				$url .= '&filter_customer=' . $_GET['filter_customer'];
			}
			
			if (isset($_GET['filter_product'])) {
				$url .= '&filter_product=' . $_GET['filter_product'];
			}
			
			if (isset($_GET['filter_model'])) {
				$url .= '&filter_model=' . $_GET['filter_model'];
			}
												
			if (isset($_GET['filter_return_status_id'])) {
				$url .= '&filter_return_status_id=' . $_GET['filter_return_status_id'];
			}
			
			if (isset($_GET['filter_date_added'])) {
				$url .= '&filter_date_added=' . $_GET['filter_date_added'];
			}
			
			if (isset($_GET['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $_GET['filter_date_modified'];
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
			
			$this->url->redirect($this->url->link('sale/return', $url));
		}
    	
    	$this->getForm();
  	} 
   
  	public function update() {
		$this->load->language('sale/return');

    	$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_sale_return->editReturn($_GET['return_id'], $_POST);
	  		
			$this->message->add('success', $this->_('text_success'));
	  
			$url = '';

			if (isset($_GET['filter_return_id'])) {
				$url .= '&filter_return_id=' . $_GET['filter_return_id'];
			}
			
			if (isset($_GET['filter_order_id'])) {
				$url .= '&filter_order_id=' . $_GET['filter_order_id'];
			}
			
			if (isset($_GET['filter_customer'])) {
				$url .= '&filter_customer=' . $_GET['filter_customer'];
			}
			
			if (isset($_GET['filter_product'])) {
				$url .= '&filter_product=' . $_GET['filter_product'];
			}
			
			if (isset($_GET['filter_model'])) {
				$url .= '&filter_model=' . $_GET['filter_model'];
			}
												
			if (isset($_GET['filter_return_status_id'])) {
				$url .= '&filter_return_status_id=' . $_GET['filter_return_status_id'];
			}
			
			if (isset($_GET['filter_date_added'])) {
				$url .= '&filter_date_added=' . $_GET['filter_date_added'];
			}
			
			if (isset($_GET['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $_GET['filter_date_modified'];
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
			
			$this->url->redirect($this->url->link('sale/return', $url));
		}
    
    	$this->getForm();
  	}   

  	public function delete() {
		$this->load->language('sale/return');

    	$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $return_id) {
				$this->model_sale_return->deleteReturn($return_id);
			}
			
			$this->message->add('success', $this->_('text_success'));

			$url = '';

			if (isset($_GET['filter_return_id'])) {
				$url .= '&filter_return_id=' . $_GET['filter_return_id'];
			}
			
			if (isset($_GET['filter_order_id'])) {
				$url .= '&filter_order_id=' . $_GET['filter_order_id'];
			}
			
			if (isset($_GET['filter_customer'])) {
				$url .= '&filter_customer=' . $_GET['filter_customer'];
			}
			
			if (isset($_GET['filter_product'])) {
				$url .= '&filter_product=' . $_GET['filter_product'];
			}
			
			if (isset($_GET['filter_model'])) {
				$url .= '&filter_model=' . $_GET['filter_model'];
			}
												
			if (isset($_GET['filter_return_status_id'])) {
				$url .= '&filter_return_status_id=' . $_GET['filter_return_status_id'];
			}
			
			if (isset($_GET['filter_date_added'])) {
				$url .= '&filter_date_added=' . $_GET['filter_date_added'];
			}
			
			if (isset($_GET['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $_GET['filter_date_modified'];
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
			
			$this->url->redirect($this->url->link('sale/return', $url));
    	}
    
    	$this->getList();
  	}  
    
  	private function getList() {
		$this->template->load('sale/return_list');

		if (isset($_GET['filter_return_id'])) {
			$filter_return_id = $_GET['filter_return_id'];
		} else {
			$filter_return_id = null;
		}
		
		if (isset($_GET['filter_order_id'])) {
			$filter_order_id = $_GET['filter_order_id'];
		} else {
			$filter_order_id = null;
		}
		
		if (isset($_GET['filter_customer'])) {
			$filter_customer = $_GET['filter_customer'];
		} else {
			$filter_customer = null;
		}

		if (isset($_GET['filter_product'])) {
			$filter_product = $_GET['filter_product'];
		} else {
			$filter_product = null;
		}

		if (isset($_GET['filter_model'])) {
			$filter_model = $_GET['filter_model'];
		} else {
			$filter_model = null;
		}
		
		if (isset($_GET['filter_return_status_id'])) {
			$filter_return_status_id = $_GET['filter_return_status_id'];
		} else {
			$filter_return_status_id = null;
		}
		
		if (isset($_GET['filter_date_added'])) {
			$filter_date_added = $_GET['filter_date_added'];
		} else {
			$filter_date_added = null;
		}
		
		if (isset($_GET['filter_date_modified'])) {
			$filter_date_modified = $_GET['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}	
		
		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'r.return_id'; 
		}
		
		if (isset($_GET['order'])) {
			$order = $_GET['order'];
		} else {
			$order = 'DESC';
		}
					
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
				
		$url = '';

		if (isset($_GET['filter_return_id'])) {
			$url .= '&filter_return_id=' . $_GET['filter_return_id'];
		}
		
		if (isset($_GET['filter_order_id'])) {
			$url .= '&filter_order_id=' . $_GET['filter_order_id'];
		}
		
		if (isset($_GET['filter_customer'])) {
			$url .= '&filter_customer=' . $_GET['filter_customer'];
		}
		
		if (isset($_GET['filter_product'])) {
			$url .= '&filter_product=' . $_GET['filter_product'];
		}
		
		if (isset($_GET['filter_model'])) {
			$url .= '&filter_model=' . $_GET['filter_model'];
		}
													
		if (isset($_GET['filter_return_status_id'])) {
			$url .= '&filter_return_status_id=' . $_GET['filter_return_status_id'];
		}
		
		if (isset($_GET['filter_date_added'])) {
			$url .= '&filter_date_added=' . $_GET['filter_date_added'];
		}
		
		if (isset($_GET['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $_GET['filter_date_modified'];
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

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('sale/return', $url));

		$this->data['insert'] = $this->url->link('sale/return/insert', $url);
		$this->data['delete'] = $this->url->link('sale/return/delete', $url);

		$this->data['returns'] = array();

		$data = array(
			'filter_return_id'        => $filter_return_id, 
			'filter_order_id'         => $filter_order_id, 
			'filter_customer'         => $filter_customer, 
			'filter_product'          => $filter_product, 
			'filter_model'            => $filter_model, 
			'filter_return_status_id' => $filter_return_status_id, 
			'filter_date_added'       => $filter_date_added,
			'filter_date_modified'    => $filter_date_modified,
			'sort'                    => $sort,
			'order'                   => $order,
			'start'                   => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'                   => $this->config->get('config_admin_limit')
		);
		
		$return_total = $this->model_sale_return->getTotalReturns($data);
	
		$results = $this->model_sale_return->getReturns($data);
 
    	foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->_('text_view'),
				'href' => $this->url->link('sale/return/info', 'return_id=' . $result['return_id'] . $url)
			);
					
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('sale/return/update', 'return_id=' . $result['return_id'] . $url)
			);
						
			$this->data['returns'][] = array(
				'return_id'     => $result['return_id'],
				'order_id'      => $result['order_id'],
				'customer'      => $result['customer'],
				'product'       => $result['product'],
				'model'         => $result['model'],
				'status'        => $result['status'],
				'date_added'    => $this->tool->format_datetime($result['date_added'], $this->language->getInfo('date_format_short')),
				'date_modified' => date($this->language->getInfo('date_format_short'), strtotime($result['date_modified'])),	
				'selected'      => isset($_POST['selected']) && in_array($result['return_id'], $_POST['selected']),
				'action'        => $action
			);
		}	
		
		if (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];
			
			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
		$url = '';

		if (isset($_GET['filter_return_id'])) {
			$url .= '&filter_return_id=' . $_GET['filter_return_id'];
		}
		
		if (isset($_GET['filter_order_id'])) {
			$url .= '&filter_order_id=' . $_GET['filter_order_id'];
		}
		
		if (isset($_GET['filter_customer'])) {
			$url .= '&filter_customer=' . $_GET['filter_customer'];
		}
		
		if (isset($_GET['filter_product'])) {
			$url .= '&filter_product=' . $_GET['filter_product'];
		}
		
		if (isset($_GET['filter_model'])) {
			$url .= '&filter_model=' . $_GET['filter_model'];
		}
											
		if (isset($_GET['filter_return_status_id'])) {
			$url .= '&filter_return_status_id=' . $_GET['filter_return_status_id'];
		}
		
		if (isset($_GET['filter_date_added'])) {
			$url .= '&filter_date_added=' . $_GET['filter_date_added'];
		}
						
		if (isset($_GET['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $_GET['filter_date_modified'];
		}	
			
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}
		
		$this->data['sort_return_id'] = $this->url->link('sale/return', 'sort=r.return_id' . $url);
		$this->data['sort_order_id'] = $this->url->link('sale/return', 'sort=r.order_id' . $url);
		$this->data['sort_customer'] = $this->url->link('sale/return', 'sort=customer' . $url);
		$this->data['sort_product'] = $this->url->link('sale/return', 'sort=product' . $url);
		$this->data['sort_model'] = $this->url->link('sale/return', 'sort=model' . $url);
		$this->data['sort_status'] = $this->url->link('sale/return', 'sort=status' . $url);
		$this->data['sort_date_added'] = $this->url->link('sale/return', 'sort=r.date_added' . $url);
		$this->data['sort_date_modified'] = $this->url->link('sale/return', 'sort=r.date_modified' . $url);
		
		$url = '';

		if (isset($_GET['filter_return_id'])) {
			$url .= '&filter_return_id=' . $_GET['filter_return_id'];
		}
		
		if (isset($_GET['filter_order_id'])) {
			$url .= '&filter_order_id=' . $_GET['filter_order_id'];
		}
		
		if (isset($_GET['filter_customer'])) {
			$url .= '&filter_customer=' . $_GET['filter_customer'];
		}
		
		if (isset($_GET['filter_product'])) {
			$url .= '&filter_product=' . $_GET['filter_product'];
		}
		
		if (isset($_GET['filter_model'])) {
			$url .= '&filter_model=' . $_GET['filter_model'];
		}
											
		if (isset($_GET['filter_return_status_id'])) {
			$url .= '&filter_return_status_id=' . $_GET['filter_return_status_id'];
		}
		
		if (isset($_GET['filter_date_added'])) {
			$url .= '&filter_date_added=' . $_GET['filter_date_added'];
		}
		
		if (isset($_GET['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $_GET['filter_date_modified'];
		}
					
		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}
												
		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total = $return_total;
		$this->pagination->page = $page;
		$this->pagination->limit = $this->config->get('config_admin_limit');
		$this->pagination->text = $this->_('text_pagination');
		$this->pagination->url = $this->url->link('sale/return', $url);
			
		$this->data['pagination'] = $this->pagination->render();

		$this->data['filter_return_id'] = $filter_return_id;
		$this->data['filter_order_id'] = $filter_order_id;
		$this->data['filter_customer'] = $filter_customer;
		$this->data['filter_product'] = $filter_product;
		$this->data['filter_model'] = $filter_model;
		$this->data['filter_return_status_id'] = $filter_return_status_id;
		$this->data['filter_date_added'] = $filter_date_added;
		$this->data['filter_date_modified'] = $filter_date_modified;

		$this->data['return_statuses'] = $this->model_localisation_return_status->getReturnStatuses();
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
  	}
  
  	private function getForm() {
		$this->template->load('sale/return_form');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
			
 		if (isset($this->error['order_id'])) {
			$this->data['error_order_id'] = $this->error['order_id'];
		} else {
			$this->data['error_order_id'] = '';
		}		

 		if (isset($this->error['firstname'])) {
			$this->data['error_firstname'] = $this->error['firstname'];
		} else {
			$this->data['error_firstname'] = '';
		}

 		if (isset($this->error['lastname'])) {
			$this->data['error_lastname'] = $this->error['lastname'];
		} else {
			$this->data['error_lastname'] = '';
		}
		
 		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}
		
 		if (isset($this->error['telephone'])) {
			$this->data['error_telephone'] = $this->error['telephone'];
		} else {
			$this->data['error_telephone'] = '';
		}
		
		if (isset($this->error['product'])) {
			$this->data['error_product'] = $this->error['product'];
		} else {
			$this->data['error_product'] = '';
		}
		
		if (isset($this->error['model'])) {
			$this->data['error_model'] = $this->error['model'];
		} else {
			$this->data['error_model'] = '';
		}
								
		$url = '';
		
		if (isset($_GET['filter_return_id'])) {
			$url .= '&filter_return_id=' . $_GET['filter_return_id'];
		}
		
		if (isset($_GET['filter_order_id'])) {
			$url .= '&filter_order_id=' . $_GET['filter_order_id'];
		}
		
		if (isset($_GET['filter_customer'])) {
			$url .= '&filter_customer=' . $_GET['filter_customer'];
		}
		
		if (isset($_GET['filter_product'])) {
			$url .= '&filter_product=' . $_GET['filter_product'];
		}
		
		if (isset($_GET['filter_model'])) {
			$url .= '&filter_model=' . $_GET['filter_model'];
		}
											
		if (isset($_GET['filter_return_status_id'])) {
			$url .= '&filter_return_status_id=' . $_GET['filter_return_status_id'];
		}
		
		if (isset($_GET['filter_date_added'])) {
			$url .= '&filter_date_added=' . $_GET['filter_date_added'];
		}
		
		if (isset($_GET['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $_GET['filter_date_modified'];
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

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('sale/return', $url));

		if (!isset($_GET['return_id'])) {
			$this->data['action'] = $this->url->link('sale/return/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('sale/return/update', 'return_id=' . $_GET['return_id'] . $url);
		}
		  
    	$this->data['cancel'] = $this->url->link('sale/return', $url);

    	if (isset($_GET['return_id']) && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
      		$return_info = $this->model_sale_return->getReturn($_GET['return_id']);
    	}
				
    	if (isset($_POST['order_id'])) {
      		$this->data['order_id'] = $_POST['order_id'];
		} elseif (!empty($return_info)) { 
			$this->data['order_id'] = $return_info['order_id'];
		} else {
      		$this->data['order_id'] = '';
    	}	
		
    	if (isset($_POST['date_ordered'])) {
      		$this->data['date_ordered'] = $_POST['date_ordered'];
		} elseif (!empty($return_info)) { 
			$this->data['date_ordered'] = $return_info['date_ordered'];
		} else {
      		$this->data['date_ordered'] = '';
    	}	

		if (isset($_POST['customer'])) {
			$this->data['customer'] = $_POST['customer'];
		} elseif (!empty($return_info)) {
			$this->data['customer'] = $return_info['customer'];
		} else {
			$this->data['customer'] = '';
		}
				
		if (isset($_POST['customer_id'])) {
			$this->data['customer_id'] = $_POST['customer_id'];
		} elseif (!empty($return_info)) {
			$this->data['customer_id'] = $return_info['customer_id'];
		} else {
			$this->data['customer_id'] = '';
		}
			
    	if (isset($_POST['firstname'])) {
      		$this->data['firstname'] = $_POST['firstname'];
		} elseif (!empty($return_info)) { 
			$this->data['firstname'] = $return_info['firstname'];
		} else {
      		$this->data['firstname'] = '';
    	}	
		
    	if (isset($_POST['lastname'])) {
      		$this->data['lastname'] = $_POST['lastname'];
		} elseif (!empty($return_info)) { 
			$this->data['lastname'] = $return_info['lastname'];
		} else {
      		$this->data['lastname'] = '';
    	}
		
    	if (isset($_POST['email'])) {
      		$this->data['email'] = $_POST['email'];
		} elseif (!empty($return_info)) { 
			$this->data['email'] = $return_info['email'];
		} else {
      		$this->data['email'] = '';
    	}
		
    	if (isset($_POST['telephone'])) {
      		$this->data['telephone'] = $_POST['telephone'];
		} elseif (!empty($return_info)) { 
			$this->data['telephone'] = $return_info['telephone'];
		} else {
      		$this->data['telephone'] = '';
    	}
		
		if (isset($_POST['product'])) {
			$this->data['product'] = $_POST['product'];
		} elseif (!empty($return_info)) {
			$this->data['product'] = $return_info['product'];
		} else {
			$this->data['product'] = '';
		}	
			
		if (isset($_POST['product_id'])) {
			$this->data['product_id'] = $_POST['product_id'];
		} elseif (!empty($return_info)) {
			$this->data['product_id'] = $return_info['product_id'];
		} else {
			$this->data['product_id'] = '';
		}	
		
		if (isset($_POST['model'])) {
			$this->data['model'] = $_POST['model'];
		} elseif (!empty($return_info)) {
			$this->data['model'] = $return_info['model'];
		} else {
			$this->data['model'] = '';
		}

		if (isset($_POST['quantity'])) {
			$this->data['quantity'] = $_POST['quantity'];
		} elseif (!empty($return_info)) {
			$this->data['quantity'] = $return_info['quantity'];
		} else {
			$this->data['quantity'] = '';
		}
		
		if (isset($_POST['opened'])) {
			$this->data['opened'] = $_POST['opened'];
		} elseif (!empty($return_info)) {
			$this->data['opened'] = $return_info['opened'];
		} else {
			$this->data['opened'] = '';
		}
		
		if (isset($_POST['return_reason_id'])) {
			$this->data['return_reason_id'] = $_POST['return_reason_id'];
		} elseif (!empty($return_info)) {
			$this->data['return_reason_id'] = $return_info['return_reason_id'];
		} else {
			$this->data['return_reason_id'] = '';
		}
							
		$this->data['return_reasons'] = $this->model_localisation_return_reason->getReturnReasons();
	
		if (isset($_POST['return_action_id'])) {
			$this->data['return_action_id'] = $_POST['return_action_id'];
		} elseif (!empty($return_info)) {
			$this->data['return_action_id'] = $return_info['return_action_id'];
		} else {
			$this->data['return_action_id'] = '';
		}				
				
		$this->data['return_actions'] = $this->model_localisation_return_action->getReturnActions();

		if (isset($_POST['comment'])) {
			$this->data['comment'] = $_POST['comment'];
		} elseif (!empty($return_info)) {
			$this->data['comment'] = $return_info['comment'];
		} else {
			$this->data['comment'] = '';
		}
						
		if (isset($_POST['return_status_id'])) {
			$this->data['return_status_id'] = $_POST['return_status_id'];
		} elseif (!empty($return_info)) {
			$this->data['return_status_id'] = $return_info['return_status_id'];
		} else {
			$this->data['return_status_id'] = '';
		}
		
		$this->data['return_statuses'] = $this->model_localisation_return_status->getReturnStatuses();
						
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	public function info() {
		if (isset($_GET['return_id'])) {
			$return_id = $_GET['return_id'];
		} else {
			$return_id = 0;
		}
				
		$return_info = $this->model_sale_return->getReturn($return_id);
		
		if ($return_info) {
		$this->template->load('sale/return_info');
			$this->load->language('sale/return');
		
			$this->document->setTitle($this->_('heading_title'));
			
			$url = '';
			
			if (isset($_GET['filter_return_id'])) {
				$url .= '&filter_return_id=' . $_GET['filter_return_id'];
			}
			
			if (isset($_GET['filter_order_id'])) {
				$url .= '&filter_order_id=' . $_GET['filter_order_id'];
			}
			
			if (isset($_GET['filter_customer'])) {
				$url .= '&filter_customer=' . $_GET['filter_customer'];
			}
			
			if (isset($_GET['filter_product'])) {
				$url .= '&filter_product=' . $_GET['filter_product'];
			}
			
			if (isset($_GET['filter_model'])) {
				$url .= '&filter_model=' . $_GET['filter_model'];
			}
												
			if (isset($_GET['filter_return_status_id'])) {
				$url .= '&filter_return_status_id=' . $_GET['filter_return_status_id'];
			}
			
			if (isset($_GET['filter_date_added'])) {
				$url .= '&filter_date_added=' . $_GET['filter_date_added'];
			}
			
			if (isset($_GET['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $_GET['filter_date_modified'];
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
				
				$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
				$this->breadcrumb->add($this->_('heading_title'), $this->url->link('sale/return', $url));

			$this->data['cancel'] = $this->url->link('sale/return', $url);			
			
			$this->data['return_id'] = $return_info['return_id'];
			$this->data['order_id'] = $return_info['order_id'];
			
			$order_info = $this->model_sale_order->getOrder($return_info['order_id']);
			
			if ($return_info['order_id'] && $order_info) {
				$this->data['order'] = $this->url->link('sale/order/info', 'order_id=' . $return_info['order_id']);
			} else {
				$this->data['order'] = '';
			}
			
			$this->data['date_ordered'] = date($this->language->getInfo('date_format_short'), strtotime($return_info['date_ordered']));
         
			$this->data['firstname'] = $return_info['firstname'];
			$this->data['lastname'] = $return_info['lastname'];
						
			if ($return_info['customer_id']) {
				$this->data['customer'] = $this->url->link('sale/customer/update', 'customer_id=' . $return_info['customer_id']);
			} else {
				$this->data['customer'] = '';
			}
			
			$this->data['email'] = $return_info['email'];
			$this->data['telephone'] = $return_info['telephone'];
			
			$return_status_info = $this->model_localisation_return_status->getReturnStatus($return_info['return_status_id']);

			if ($return_status_info) {
				$this->data['return_status'] = $return_status_info['name'];
			} else {
				$this->data['return_status'] = '';
			}		
						
			$this->data['date_added'] = date($this->language->getInfo('date_format_short'), strtotime($return_info['date_added']));
			$this->data['date_modified'] = date($this->language->getInfo('date_format_short'), strtotime($return_info['date_modified']));
			$this->data['product'] = $return_info['product'];
			$this->data['model'] = $return_info['model'];
			$this->data['quantity'] = $return_info['quantity'];
			
			$return_reason_info = $this->model_localisation_return_reason->getReturnReason($return_info['return_reason_id']);

			if ($return_reason_info) {
				$this->data['return_reason'] = $return_reason_info['name'];
			} else {
				$this->data['return_reason'] = '';
			}			
			
			$this->data['opened'] = $return_info['opened'] ? $this->_('text_yes') : $this->_('text_no');
	
   		$this->data['comment'] = nl2br($return_info['comment']);
			
			$this->data['return_actions'] = $this->model_localisation_return_action->getReturnActions(); 
			
			$this->data['return_action_id'] = $return_info['return_action_id'];

			$this->data['return_statuses'] = $this->model_localisation_return_status->getReturnStatuses();				
			
			$this->data['return_status_id'] = $return_info['return_status_id'];
		
			$this->children = array(
				'common/header',
				'common/footer'
			);
					
			$this->response->setOutput($this->render());		
		} else {
		$this->template->load('error/not_found');
			$this->load->language('error/not_found');

			$this->document->setTitle($this->_('heading_title'));

				$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
				$this->breadcrumb->add($this->_('heading_title'), $this->url->link('error/not_found'));

			$this->children = array(
				'common/header',
				'common/footer'
			);
		
			$this->response->setOutput($this->render());			
		}
	}
		
  	private function validateForm() {
    	if (!$this->user->hasPermission('modify', 'sale/return')) {
      		$this->error['warning'] = $this->_('error_permission');
    	}

    	if ((strlen($_POST['firstname']) < 1) || (strlen($_POST['firstname']) > 32)) {
      		$this->error['firstname'] = $this->_('error_firstname');
    	}

    	if ((strlen($_POST['lastname']) < 1) || (strlen($_POST['lastname']) > 32)) {
      		$this->error['lastname'] = $this->_('error_lastname');
    	}

    	if ((strlen($_POST['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['email'])) {
      		$this->error['email'] = $this->_('error_email');
    	}
		
    	if ((strlen($_POST['telephone']) < 3) || (strlen($_POST['telephone']) > 32)) {
      		$this->error['telephone'] = $this->_('error_telephone');
    	}
		
		if ((strlen($_POST['product']) < 1) || (strlen($_POST['product']) > 255)) {
			$this->error['product'] = $this->_('error_product');
		}	
		
		if ((strlen($_POST['model']) < 1) || (strlen($_POST['model']) > 64)) {
			$this->error['model'] = $this->_('error_model');
		}							

		if (empty($_POST['return_reason_id'])) {
			$this->error['reason'] = $this->_('error_reason');
		}	
				
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->_('error_warning');
		}
		
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}    

  	private function validateDelete() {
    	if (!$this->user->hasPermission('modify', 'sale/return')) {
      		$this->error['warning'] = $this->_('error_permission');
    	}	
	  	 
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}  
  	} 
	
	public function action() {
		$this->language->load('sale/return');
		
		$json = array();
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			
			if (!$this->user->hasPermission('modify', 'sale/return')) {
				$json['error'] = $this->_('error_permission');
			}
		
			if (!$json) { 
			
				$json['success'] = $this->_('text_success');
				
				$this->model_sale_return->editReturnAction($_GET['return_id'], $_POST['return_action_id']);
			}
		}
		
		$this->response->setOutput(json_encode($json));	
	}
		
	public function history() {
		$this->template->load('sale/return_history');

    	$this->language->load('sale/return');
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->user->hasPermission('modify', 'sale/return')) { 
			$this->model_sale_return->addReturnHistory($_GET['return_id'], $_POST);
				
			$this->language->set('success', $this->_('text_success'));
		} else {
			$this->data['success'] = '';
		}
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && !$this->user->hasPermission('modify', 'sale/return')) {
			$this->language->set('error_warning', $this->_('error_permission'));
		} else {
			$this->data['error_warning'] = '';
		}
				
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}  
		
		$this->data['histories'] = array();
			
		$results = $this->model_sale_return->getReturnHistories($_GET['return_id'], ($page - 1) * 10, 10);
      		
		foreach ($results as $result) {
        	$this->data['histories'][] = array(
				'notify'     => $result['notify'] ? $this->_('text_yes') : $this->_('text_no'),
				'status'     => $result['status'],
				'comment'    => nl2br($result['comment']),
        		'date_added' => $this->tool->format_datetime($result['date_added'], $this->language->getInfo('date_format_short')),
        	);
      	}			
		
		$history_total = $this->model_sale_return->getTotalReturnHistories($_GET['return_id']);
			
		$this->pagination->init();
		$this->pagination->total = $history_total;
		$this->pagination->page = $page;
		$this->pagination->limit = 10; 
		$this->pagination->text = $this->_('text_pagination');
		$this->pagination->url = $this->url->link('sale/return/history', 'return_id=' . $_GET['return_id']);
			
		$this->data['pagination'] = $this->pagination->render();
		
		
		$this->response->setOutput($this->render());
  	}		
}