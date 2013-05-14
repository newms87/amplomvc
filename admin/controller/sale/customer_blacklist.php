<?php    
class ControllerSaleCustomerBlacklist extends Controller { 
	
  
  	public function index() {
		$this->load->language('sale/customer_blacklist');
		 
		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
  	}
  
  	public function insert() {
		$this->load->language('sale/customer_blacklist');

    	$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
      	  	$this->model_sale_customer_blacklist->addCustomerBlacklist($_POST);
			
			$this->message->add('success', $this->_('text_success'));
		  
			$url = '';
							
			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}
			
			$this->url->redirect($this->url->link('sale/customer_blacklist', $url));
		}
    	
    	$this->getForm();
  	} 
   
  	public function update() {
		$this->load->language('sale/customer_blacklist');

    	$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_sale_customer_blacklist->editCustomerBlacklist($_GET['customer_ip_blacklist_id'], $_POST);
	  		
			$this->message->add('success', $this->_('text_success'));
	  
			$url = '';
						
			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}
			
			$this->url->redirect($this->url->link('sale/customer_blacklist', $url));
		}
    
    	$this->getForm();
  	}   

  	public function delete() {
		$this->load->language('sale/customer_blacklist');

    	$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $customer_ip_blacklist_id) {
				$this->model_sale_customer_blacklist->deleteCustomerBlacklist($customer_ip_blacklist_id);
			}
			
			$this->message->add('success', $this->_('text_success'));

			$url = '';
						
			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}
			
			$this->url->redirect($this->url->link('sale/customer_blacklist', $url));
    	}
    
    	$this->getList();
  	}  
    
  	private function getList() {
		$this->template->load('sale/customer_blacklist_list');

		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'ip'; 
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
						
		$url = '';
						
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('sale/customer_blacklist', $url));

		$this->data['insert'] = $this->url->link('sale/customer_blacklist/insert', $url);
		$this->data['delete'] = $this->url->link('sale/customer_blacklist/delete', $url);

		$this->data['customer_blacklists'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$customer_blacklist_total = $this->model_sale_customer_blacklist->getTotalCustomerBlacklists($data);
	
		$results = $this->model_sale_customer_blacklist->getCustomerBlacklists($data);
 
    	foreach ($results as $result) {
			$action = array();
		
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('sale/customer_blacklist/update', 'customer_ip_blacklist_id=' . $result['customer_ip_blacklist_id'] . $url)
			);
			
			$this->data['customer_blacklists'][] = array(
				'customer_ip_blacklist_id' => $result['customer_ip_blacklist_id'],
				'ip'                       => $result['ip'],
				'total'                    => $result['total'],
				'customer'                 => $this->url->link('sale/customer', 'filter_ip=' . $result['ip']),
				'selected'                 => isset($_POST['selected']) && in_array($result['customer_ip_blacklist_id'], $_POST['selected']),
				'action'                   => $action
			);
		}	
					
		if (isset($this->error['warning'])) {
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
			
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}
		
		$this->data['sort_ip'] = $this->url->link('sale/customer_blacklist', 'sort=ip' . $url);
		
		$url = '';
			
		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}
												
		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total = $customer_blacklist_total;
		$this->pagination->page = $page;
		$this->pagination->limit = $this->config->get('config_admin_limit');
		$this->pagination->text = $this->_('text_pagination');
		$this->pagination->url = $this->url->link('sale/customer_blacklist', $url . '&page={page}');
			
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
		$this->template->load('sale/customer_blacklist_form');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
 		if (isset($this->error['ip'])) {
			$this->data['error_ip'] = $this->error['ip'];
		} else {
			$this->data['error_ip'] = '';
		}
		
		$url = '';
		
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('sale/customer_blacklist', $url));

		if (!isset($_GET['customer_ip_blacklist_id'])) {
			$this->data['action'] = $this->url->link('sale/customer_blacklist/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('sale/customer_blacklist/update', 'customer_ip_blacklist_id=' . $_GET['customer_ip_blacklist_id'] . $url);
		}
		  
    	$this->data['cancel'] = $this->url->link('sale/customer_blacklist', $url);

    	if (isset($_GET['customer_ip_blacklist_id']) && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
      		$customer_blacklist_info = $this->model_sale_customer_blacklist->getCustomerBlacklist($_GET['customer_ip_blacklist_id']);
    	}
			
    	if (isset($_POST['ip'])) {
      		$this->data['ip'] = $_POST['ip'];
		} elseif (!empty($customer_blacklist_info)) { 
			$this->data['ip'] = $customer_blacklist_info['ip'];
		} else {
      		$this->data['ip'] = '';
    	}
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
			 
  	private function validateForm() {
    	if (!$this->user->hasPermission('modify', 'sale/customer_blacklist')) {
      		$this->error['warning'] = $this->_('error_permission');
    	}

    	if ((strlen($_POST['ip']) < 1) || (strlen($_POST['ip']) > 15)) {
      		$this->error['ip'] = $this->_('error_ip');
    	}
		
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}    

  	private function validateDelete() {
    	if (!$this->user->hasPermission('modify', 'sale/customer_blacklist')) {
      		$this->error['warning'] = $this->_('error_permission');
    	}	
	  	 
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}  
  	} 
}