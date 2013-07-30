<?php
class Admin_Controller_Sale_Customer extends Controller
{
	

  	public function index()
  	{
		$this->language->load('sale/customer');
		
		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
  	}

  	public function insert()
  	{
		$this->language->load('sale/customer');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validateForm()) {
				$this->Model_Sale_Customer->addCustomer($_POST);
			
			$this->message->add('success', $this->_('text_success'));
		
			$url = '';

			if (isset($_GET['filter_name'])) {
				$url .= '&filter_name=' . $_GET['filter_name'];
			}
			
			if (isset($_GET['filter_email'])) {
				$url .= '&filter_email=' . $_GET['filter_email'];
			}
			
			if (isset($_GET['filter_customer_group_id'])) {
				$url .= '&filter_customer_group_id=' . $_GET['filter_customer_group_id'];
			}
			
			if (isset($_GET['filter_status'])) {
				$url .= '&filter_status=' . $_GET['filter_status'];
			}
			
			if (isset($_GET['filter_approved'])) {
				$url .= '&filter_approved=' . $_GET['filter_approved'];
			}

			if (isset($_GET['filter_ip'])) {
				$url .= '&filter_ip=' . $_GET['filter_ip'];
			}
					
			if (isset($_GET['filter_date_added'])) {
				$url .= '&filter_date_added=' . $_GET['filter_date_added'];
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
			
			$this->url->redirect($this->url->link('sale/customer', $url));
		}
		
		$this->getForm();
  	}
	
  	public function update()
  	{
		$this->language->load('sale/customer');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Sale_Customer->editCustomer($_GET['customer_id'], $_POST);
			
			$this->message->add('success', $this->_('text_success'));
	
			$url = '';

			if (isset($_GET['filter_name'])) {
				$url .= '&filter_name=' . $_GET['filter_name'];
			}
			
			if (isset($_GET['filter_email'])) {
				$url .= '&filter_email=' . $_GET['filter_email'];
			}
			
			if (isset($_GET['filter_customer_group_id'])) {
				$url .= '&filter_customer_group_id=' . $_GET['filter_customer_group_id'];
			}
			
			if (isset($_GET['filter_status'])) {
				$url .= '&filter_status=' . $_GET['filter_status'];
			}
			
			if (isset($_GET['filter_approved'])) {
				$url .= '&filter_approved=' . $_GET['filter_approved'];
			}
			
			if (isset($_GET['filter_ip'])) {
				$url .= '&filter_ip=' . $_GET['filter_ip'];
			}
					
			if (isset($_GET['filter_date_added'])) {
				$url .= '&filter_date_added=' . $_GET['filter_date_added'];
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
			
			$this->url->redirect($this->url->link('sale/customer', $url));
		}
	
		$this->getForm();
  	}

  	public function delete()
  	{
		$this->language->load('sale/customer');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $customer_id) {
				$this->Model_Sale_Customer->deleteCustomer($customer_id);
			}
			
			$this->message->add('success', $this->_('text_success'));

			$url = '';

			if (isset($_GET['filter_name'])) {
				$url .= '&filter_name=' . $_GET['filter_name'];
			}
			
			if (isset($_GET['filter_email'])) {
				$url .= '&filter_email=' . $_GET['filter_email'];
			}
			
			if (isset($_GET['filter_customer_group_id'])) {
				$url .= '&filter_customer_group_id=' . $_GET['filter_customer_group_id'];
			}
			
			if (isset($_GET['filter_status'])) {
				$url .= '&filter_status=' . $_GET['filter_status'];
			}
			
			if (isset($_GET['filter_approved'])) {
				$url .= '&filter_approved=' . $_GET['filter_approved'];
			}
				
			if (isset($_GET['filter_ip'])) {
				$url .= '&filter_ip=' . $_GET['filter_ip'];
			}
					
			if (isset($_GET['filter_date_added'])) {
				$url .= '&filter_date_added=' . $_GET['filter_date_added'];
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
			
			$this->url->redirect($this->url->link('sale/customer', $url));
		}
	
		$this->getList();
  	}
	
	public function approve()
	{
		$this->language->load('sale/customer');
		
		$this->document->setTitle($this->_('heading_title'));
		
		if (!$this->user->hasPermission('modify', 'sale/customer')) {
			$this->error['warning'] = $this->_('error_permission');
		} elseif (isset($_POST['selected'])) {
			$approved = 0;
			
			foreach ($_POST['selected'] as $customer_id) {
				$customer_info = $this->Model_Sale_Customer->getCustomer($customer_id);
				
				if ($customer_info && !$customer_info['approved']) {
					$this->Model_Sale_Customer->approve($customer_id);
					
					$approved++;
				}
			}
			
			$this->message->add('success', sprintf($this->_('text_approved'), $approved));
			
			$url = '';
		
			if (isset($_GET['filter_name'])) {
				$url .= '&filter_name=' . $_GET['filter_name'];
			}
		
			if (isset($_GET['filter_email'])) {
				$url .= '&filter_email=' . $_GET['filter_email'];
			}
			
			if (isset($_GET['filter_customer_group_id'])) {
				$url .= '&filter_customer_group_id=' . $_GET['filter_customer_group_id'];
			}
		
			if (isset($_GET['filter_status'])) {
				$url .= '&filter_status=' . $_GET['filter_status'];
			}
			
			if (isset($_GET['filter_approved'])) {
				$url .= '&filter_approved=' . $_GET['filter_approved'];
			}
				
			if (isset($_GET['filter_ip'])) {
				$url .= '&filter_ip=' . $_GET['filter_ip'];
			}
						
			if (isset($_GET['filter_date_added'])) {
				$url .= '&filter_date_added=' . $_GET['filter_date_added'];
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
	
			$this->url->redirect($this->url->link('sale/customer', $url));
		}
		
		$this->getList();
	}
	
  	private function getList()
  	{
		$this->template->load('sale/customer_list');

		if (isset($_GET['filter_name'])) {
			$filter_name = $_GET['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($_GET['filter_email'])) {
			$filter_email = $_GET['filter_email'];
		} else {
			$filter_email = null;
		}
		
		if (isset($_GET['filter_customer_group_id'])) {
			$filter_customer_group_id = $_GET['filter_customer_group_id'];
		} else {
			$filter_customer_group_id = null;
		}

		if (isset($_GET['filter_status'])) {
			$filter_status = $_GET['filter_status'];
		} else {
			$filter_status = null;
		}
		
		if (isset($_GET['filter_approved'])) {
			$filter_approved = $_GET['filter_approved'];
		} else {
			$filter_approved = null;
		}
		
		if (isset($_GET['filter_ip'])) {
			$filter_ip = $_GET['filter_ip'];
		} else {
			$filter_ip = null;
		}
				
		if (isset($_GET['filter_date_added'])) {
			$filter_date_added = $_GET['filter_date_added'];
		} else {
			$filter_date_added = null;
		}
		
		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'name';
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

		if (isset($_GET['filter_name'])) {
			$url .= '&filter_name=' . $_GET['filter_name'];
		}
		
		if (isset($_GET['filter_email'])) {
			$url .= '&filter_email=' . $_GET['filter_email'];
		}
		
		if (isset($_GET['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $_GET['filter_customer_group_id'];
		}
			
		if (isset($_GET['filter_status'])) {
			$url .= '&filter_status=' . $_GET['filter_status'];
		}
		
		if (isset($_GET['filter_approved'])) {
			$url .= '&filter_approved=' . $_GET['filter_approved'];
		}
		
		if (isset($_GET['filter_ip'])) {
			$url .= '&filter_ip=' . $_GET['filter_ip'];
		}
					
		if (isset($_GET['filter_date_added'])) {
			$url .= '&filter_date_added=' . $_GET['filter_date_added'];
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('sale/customer', $url));

		$this->data['approve'] = $this->url->link('sale/customer/approve', $url);
		$this->data['insert'] = $this->url->link('sale/customer/insert', $url);
		$this->data['delete'] = $this->url->link('sale/customer/delete', $url);

		$this->data['customers'] = array();

		$data = array(
			'filter_name'				=> $filter_name,
			'filter_email'				=> $filter_email,
			'filter_customer_group_id' => $filter_customer_group_id,
			'filter_status'				=> $filter_status,
			'filter_approved'			=> $filter_approved,
			'filter_date_added'		=> $filter_date_added,
			'filter_ip'					=> $filter_ip,
			'sort'							=> $sort,
			'order'						=> $order,
			'start'						=> ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'						=> $this->config->get('config_admin_limit')
		);
		
		$customer_total = $this->Model_Sale_Customer->getTotalCustomers($data);
	
		$results = $this->Model_Sale_Customer->getCustomers($data);

		foreach ($results as $result) {
			$action = array();
		
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('sale/customer/update', 'customer_id=' . $result['customer_id'] . $url)
			);
			
			$this->data['customers'][] = array(
				'customer_id'	=> $result['customer_id'],
				'name'			=> $result['name'],
				'email'			=> $result['email'],
				'customer_group' => $result['customer_group'],
				'status'			=> ($result['status'] ? $this->_('text_enabled') : $this->_('text_disabled')),
				'approved'		=> ($result['approved'] ? $this->_('text_yes') : $this->_('text_no')),
				'ip'				=> $result['ip'],
				'date_added'	=> $this->date->format($result['date_added'], $this->language->getInfo('date_format_short')),
				'selected'		=> isset($_POST['selected']) && in_array($result['customer_id'], $_POST['selected']),
				'action'			=> $action
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

		if (isset($_GET['filter_name'])) {
			$url .= '&filter_name=' . $_GET['filter_name'];
		}
		
		if (isset($_GET['filter_email'])) {
			$url .= '&filter_email=' . $_GET['filter_email'];
		}
		
		if (isset($_GET['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $_GET['filter_customer_group_id'];
		}
			
		if (isset($_GET['filter_status'])) {
			$url .= '&filter_status=' . $_GET['filter_status'];
		}
		
		if (isset($_GET['filter_approved'])) {
			$url .= '&filter_approved=' . $_GET['filter_approved'];
		}
		
		if (isset($_GET['filter_ip'])) {
			$url .= '&filter_ip=' . $_GET['filter_ip'];
		}
				
		if (isset($_GET['filter_date_added'])) {
			$url .= '&filter_date_added=' . $_GET['filter_date_added'];
		}
			
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}
		
		$this->data['sort_name'] = $this->url->link('sale/customer', 'sort=name' . $url);
		$this->data['sort_email'] = $this->url->link('sale/customer', 'sort=c.email' . $url);
		$this->data['sort_customer_group'] = $this->url->link('sale/customer', 'sort=customer_group' . $url);
		$this->data['sort_status'] = $this->url->link('sale/customer', 'sort=c.status' . $url);
		$this->data['sort_approved'] = $this->url->link('sale/customer', 'sort=c.approved' . $url);
		$this->data['sort_ip'] = $this->url->link('sale/customer', 'sort=c.ip' . $url);
		$this->data['sort_date_added'] = $this->url->link('sale/customer', 'sort=c.date_added' . $url);
		
		$url = '';

		if (isset($_GET['filter_name'])) {
			$url .= '&filter_name=' . $_GET['filter_name'];
		}
		
		if (isset($_GET['filter_email'])) {
			$url .= '&filter_email=' . $_GET['filter_email'];
		}
		
		if (isset($_GET['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $_GET['filter_customer_group_id'];
		}

		if (isset($_GET['filter_status'])) {
			$url .= '&filter_status=' . $_GET['filter_status'];
		}
		
		if (isset($_GET['filter_approved'])) {
			$url .= '&filter_approved=' . $_GET['filter_approved'];
		}
		
		if (isset($_GET['filter_ip'])) {
			$url .= '&filter_ip=' . $_GET['filter_ip'];
		}
				
		if (isset($_GET['filter_date_added'])) {
			$url .= '&filter_date_added=' . $_GET['filter_date_added'];
		}
			
		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}
												
		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total = $customer_total;
		$this->data['pagination'] = $this->pagination->render();

		$this->data['filter_name'] = $filter_name;
		$this->data['filter_email'] = $filter_email;
		$this->data['filter_customer_group_id'] = $filter_customer_group_id;
		$this->data['filter_status'] = $filter_status;
		$this->data['filter_approved'] = $filter_approved;
		$this->data['filter_ip'] = $filter_ip;
		$this->data['filter_date_added'] = $filter_date_added;
		
		$this->data['customer_groups'] = $this->Model_Sale_CustomerGroup->getCustomerGroups();

		$this->data['data_stores'] = array('' => $this->_('text_select')) + $this->Model_Setting_Store->getStores();
				
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
  	}

  	private function getForm()
  	{
		$this->template->load('sale/customer_form');

		if (isset($_GET['customer_id'])) {
			$this->data['customer_id'] = $_GET['customer_id'];
		} else {
			$this->data['customer_id'] = 0;
		}

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
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
		
 		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}
		
 		if (isset($this->error['confirm'])) {
			$this->data['error_confirm'] = $this->error['confirm'];
		} else {
			$this->data['error_confirm'] = '';
		}
		
		if (isset($this->error['address_firstname'])) {
			$this->data['error_address_firstname'] = $this->error['address_firstname'];
		} else {
			$this->data['error_address_firstname'] = '';
		}

 		if (isset($this->error['address_lastname'])) {
			$this->data['error_address_lastname'] = $this->error['address_lastname'];
		} else {
			$this->data['error_address_lastname'] = '';
		}
		
		if (isset($this->error['address_address_1'])) {
			$this->data['error_address_address_1'] = $this->error['address_address_1'];
		} else {
			$this->data['error_address_address_1'] = '';
		}
		
		if (isset($this->error['address_city'])) {
			$this->data['error_address_city'] = $this->error['address_city'];
		} else {
			$this->data['error_address_city'] = '';
		}
		
		if (isset($this->error['address_postcode'])) {
			$this->data['error_address_postcode'] = $this->error['address_postcode'];
		} else {
			$this->data['error_address_postcode'] = '';
		}
		
		if (isset($this->error['address_country'])) {
			$this->data['error_address_country'] = $this->error['address_country'];
		} else {
			$this->data['error_address_country'] = '';
		}
		
		if (isset($this->error['address_zone'])) {
			$this->data['error_address_zone'] = $this->error['address_zone'];
		} else {
			$this->data['error_address_zone'] = '';
		}
		
		$url = '';
		
		if (isset($_GET['filter_name'])) {
			$url .= '&filter_name=' . $_GET['filter_name'];
		}
		
		if (isset($_GET['filter_email'])) {
			$url .= '&filter_email=' . $_GET['filter_email'];
		}
		
		if (isset($_GET['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $_GET['filter_customer_group_id'];
		}
		
		if (isset($_GET['filter_status'])) {
			$url .= '&filter_status=' . $_GET['filter_status'];
		}
		
		if (isset($_GET['filter_approved'])) {
			$url .= '&filter_approved=' . $_GET['filter_approved'];
		}
		
		if (isset($_GET['filter_date_added'])) {
			$url .= '&filter_date_added=' . $_GET['filter_date_added'];
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('sale/customer', $url));

		if (!isset($_GET['customer_id'])) {
			$this->data['action'] = $this->url->link('sale/customer/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('sale/customer/update', 'customer_id=' . $_GET['customer_id'] . $url);
		}
		
		$this->data['cancel'] = $this->url->link('sale/customer', $url);

		if (isset($_GET['customer_id']) && !$this->request->isPost()) {
				$customer_info = $this->Model_Sale_Customer->getCustomer($_GET['customer_id']);
		}
			
		if (isset($_POST['firstname'])) {
				$this->data['firstname'] = $_POST['firstname'];
		} elseif (!empty($customer_info)) {
			$this->data['firstname'] = $customer_info['firstname'];
		} else {
				$this->data['firstname'] = '';
		}

		if (isset($_POST['lastname'])) {
				$this->data['lastname'] = $_POST['lastname'];
		} elseif (!empty($customer_info)) {
			$this->data['lastname'] = $customer_info['lastname'];
		} else {
				$this->data['lastname'] = '';
		}

		if (isset($_POST['email'])) {
				$this->data['email'] = $_POST['email'];
		} elseif (!empty($customer_info)) {
			$this->data['email'] = $customer_info['email'];
		} else {
				$this->data['email'] = '';
		}

		if (isset($_POST['telephone'])) {
				$this->data['telephone'] = $_POST['telephone'];
		} elseif (!empty($customer_info)) {
			$this->data['telephone'] = $customer_info['telephone'];
		} else {
				$this->data['telephone'] = '';
		}

		if (isset($_POST['fax'])) {
				$this->data['fax'] = $_POST['fax'];
		} elseif (!empty($customer_info)) {
			$this->data['fax'] = $customer_info['fax'];
		} else {
				$this->data['fax'] = '';
		}

		if (isset($_POST['newsletter'])) {
				$this->data['newsletter'] = $_POST['newsletter'];
		} elseif (!empty($customer_info)) {
			$this->data['newsletter'] = $customer_info['newsletter'];
		} else {
				$this->data['newsletter'] = '';
		}
		
		$this->data['customer_groups'] = $this->Model_Sale_CustomerGroup->getCustomerGroups();

		if (isset($_POST['customer_group_id'])) {
				$this->data['customer_group_id'] = $_POST['customer_group_id'];
		} elseif (!empty($customer_info)) {
			$this->data['customer_group_id'] = $customer_info['customer_group_id'];
		} else {
				$this->data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}
		
		if (isset($_POST['status'])) {
				$this->data['status'] = $_POST['status'];
		} elseif (!empty($customer_info)) {
			$this->data['status'] = $customer_info['status'];
		} else {
				$this->data['status'] = 1;
		}

		if (isset($_POST['password'])) {
			$this->data['password'] = $_POST['password'];
		} else {
			$this->data['password'] = '';
		}
		
		if (isset($_POST['confirm'])) {
			$this->data['confirm'] = $_POST['confirm'];
		} else {
			$this->data['confirm'] = '';
		}
		
		$this->data['countries'] = $this->Model_Localisation_Country->getCountries();
			
		if (isset($_POST['address'])) {
				$this->data['addresses'] = $_POST['address'];
		} elseif (!empty($_GET['customer_id'])) {
			$this->data['addresses'] = $this->Model_Sale_Customer->getAddresses($_GET['customer_id']);
		} else {
			$this->data['addresses'] = array();
		}

		if (isset($_POST['address_id'])) {
				$this->data['address_id'] = $_POST['address_id'];
		} elseif (!empty($customer_info)) {
			$this->data['address_id'] = $customer_info['address_id'];
		} else {
				$this->data['address_id'] = '';
		}
		
		$this->data['ips'] = array();
		
		if (!empty($customer_info)) {
			$results = $this->Model_Sale_Customer->getIpsByCustomerId($_GET['customer_id']);
		
			foreach ($results as $result) {
				$blacklist_total = $this->Model_Sale_Customer->getTotalBlacklistsByIp($result['ip']);
				
				$this->data['ips'][] = array(
					'ip'			=> $result['ip'],
					'total'		=> $this->Model_Sale_Customer->getTotalCustomersByIp($result['ip']),
					'date_added' => date('d/m/y', strtotime($result['date_added'])),
					'filter_ip'  => $this->url->link('sale/customer', 'filter_ip=' . $result['ip']),
					'blacklist'  => $blacklist_total
				);
			}
		}
		
		//Ajax Urls
		$this->data['url_transaction'] = $this->url->ajax('sale/customer/transaction', 'customer_id=' . (int)$customer_id);
		$this->data['url_reward'] = $this->url->ajax('sale/customer/reward', 'customer_id=' . (int)$customer_id);
		$this->data['url_blacklist'] = $this->url->ajax('sale/customer/addblacklist');
		$this->data['url_remove_blacklist'] = $this->url->ajax('sale/customer/removeblacklist');
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
			
  	private function validateForm()
  	{
		if (!$this->user->hasPermission('modify', 'sale/customer')) {
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
		
		$customer_info = $this->Model_Sale_Customer->getCustomerByEmail($_POST['email']);
		
		if (!isset($_GET['customer_id'])) {
			if ($customer_info) {
				$this->error['warning'] = $this->_('error_exists');
			}
		} else {
			if ($customer_info && ($_GET['customer_id'] != $customer_info['customer_id'])) {
				$this->error['warning'] = $this->_('error_exists');
			}
		}
		
		if ((strlen($_POST['telephone']) < 3) || (strlen($_POST['telephone']) > 32)) {
				$this->error['telephone'] = $this->_('error_telephone');
		}

		if ($_POST['password'] || (!isset($_GET['customer_id']))) {
				if ((strlen($_POST['password']) < 4) || (strlen($_POST['password']) > 20)) {
				$this->error['password'] = $this->_('error_password');
				}
	
			if ($_POST['password'] != $_POST['confirm']) {
				$this->error['confirm'] = $this->_('error_confirm');
			}
		}

		if (isset($_POST['address'])) {
			foreach ($_POST['address'] as $key => $value) {
				if ((strlen($value['firstname']) < 1) || (strlen($value['firstname']) > 32)) {
					$this->error['address_firstname'][$key] = $this->_('error_firstname');
				}
				
				if ((strlen($value['lastname']) < 1) || (strlen($value['lastname']) > 32)) {
					$this->error['address_lastname'][$key] = $this->_('error_lastname');
				}
				
				if ((strlen($value['address_1']) < 3) || (strlen($value['address_1']) > 128)) {
					$this->error['address_address_1'][$key] = $this->_('error_address_1');
				}
			
				if ((strlen($value['city']) < 2) || (strlen($value['city']) > 128)) {
					$this->error['address_city'][$key] = $this->_('error_city');
				}
	
				$country_info = $this->Model_Localisation_Country->getCountry($value['country_id']);
						
				if ($country_info && $country_info['postcode_required'] && (strlen($value['postcode']) < 2) || (strlen($value['postcode']) > 10)) {
					$this->error['address_postcode'][$key] = $this->_('error_postcode');
				}
			
				if ($value['country_id'] == '') {
					$this->error['address_country'][$key] = $this->_('error_country');
				}
				
				if ($value['zone_id'] == '') {
					$this->error['address_zone'][$key] = $this->_('error_zone');
				}
			}
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->_('error_warning');
		}
		
		return $this->error ? false : true;
  	}

  	private function validateDelete()
  	{
		if (!$this->user->hasPermission('modify', 'sale/customer')) {
				$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
  	}
	
	public function login()
	{
		$json = array();
		
		if (isset($_GET['customer_id'])) {
			$customer_id = $_GET['customer_id'];
		} else {
			$customer_id = 0;
		}
		
		$customer_info = $this->Model_Sale_Customer->getCustomer($customer_id);
				
		if ($customer_info) {
			$token = md5(mt_rand());
			
			$this->Model_Sale_Customer->editToken($customer_id, $token);
			
			if (isset($_GET['store_id'])) {
				$store_id = $_GET['store_id'];
			} else {
				$store_id = 0;
			}
			
			$store_info = $this->Model_Setting_Store->getStore($store_id);
			
			if ($store_info) {
				$this->url->redirect($this->url->store($store_id, 'account/login'));
			} else {
				$this->url->redirect($this->url->store($this->config->get('config_default_store'), 'account/login'));
			}
		} else {
		$this->template->load('error/not_found');

			$this->language->load('error/not_found');

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

	public function transaction()
	{
		$this->template->load('sale/customer_transaction');

		$this->language->load('sale/customer');
		
		if ($this->request->isPost() && $this->user->hasPermission('modify', 'sale/customer')) {
			$this->Model_Sale_Customer->addTransaction($_GET['customer_id'], $_POST['description'], $_POST['amount']);
				
			$this->language->set('success', $this->_('text_success'));
		} else {
			$this->data['success'] = '';
		}
		
		if ($this->request->isPost() && !$this->user->hasPermission('modify', 'sale/customer')) {
			$this->language->set('error_warning', $this->_('error_permission'));
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
		
		$this->data['transactions'] = array();
			
		$results = $this->Model_Sale_Customer->getTransactions($_GET['customer_id'], ($page - 1) * 10, 10);
				
		foreach ($results as $result) {
			$this->data['transactions'][] = array(
				'amount'		=> $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'description' => $result['description'],
				'date_added'  => $this->date->format($result['date_added'], $this->language->getInfo('date_format_short')),
			);
			}
		
		$this->data['balance'] = $this->currency->format($this->Model_Sale_Customer->getTransactionTotal($_GET['customer_id']), $this->config->get('config_currency'));
		
		$transaction_total = $this->Model_Sale_Customer->getTotalTransactions($_GET['customer_id']);
			
		$this->pagination->init();
		$this->pagination->total = $transaction_total;
		$this->data['pagination'] = $this->pagination->render();

		
		$this->response->setOutput($this->render());
	}
			
	public function reward()
	{
		$this->template->load('sale/customer_reward');

		$this->language->load('sale/customer');
		
		if ($this->request->isPost() && $this->user->hasPermission('modify', 'sale/customer')) {
			$this->Model_Sale_Customer->addReward($_GET['customer_id'], $_POST['description'], $_POST['points']);
				
			$this->language->set('success', $this->_('text_success'));
		} else {
			$this->data['success'] = '';
		}
		
		if ($this->request->isPost() && !$this->user->hasPermission('modify', 'sale/customer')) {
			$this->language->set('error_warning', $this->_('error_permission'));
		} else {
			$this->data['error_warning'] = '';
		}
				
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
		
		$this->data['rewards'] = array();
			
		$results = $this->Model_Sale_Customer->getRewards($_GET['customer_id'], ($page - 1) * 10, 10);
				
		foreach ($results as $result) {
			$this->data['rewards'][] = array(
				'points'		=> $result['points'],
				'description' => $result['description'],
				'date_added'  => $this->date->format($result['date_added'], $this->language->getInfo('date_format_short')),
			);
			}
		
		$this->data['balance'] = $this->Model_Sale_Customer->getRewardTotal($_GET['customer_id']);
		
		$reward_total = $this->Model_Sale_Customer->getTotalRewards($_GET['customer_id']);
			
		$this->pagination->init();
		$this->pagination->total = $reward_total;
		$this->data['pagination'] = $this->pagination->render();

		
		$this->response->setOutput($this->render());
	}
	
	public function addblacklist()
	{
		$this->language->load('sale/customer');
		
		$json = array();

		if (isset($_POST['ip'])) {
			if (!$this->user->hasPermission('modify', 'sale/customer')) {
				$json['error'] = $this->_('error_permission');
			} else {
				$this->Model_Sale_Customer->addBlacklist($_POST['ip']);
				
				$json['success'] = $this->_('text_success');
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function removeblacklist()
	{
		$this->language->load('sale/customer');
		
		$json = array();

		if (isset($_POST['ip'])) {
			if (!$this->user->hasPermission('modify', 'sale/customer')) {
				$json['error'] = $this->_('error_permission');
			} else {
				$this->Model_Sale_Customer->deleteBlacklist($_POST['ip']);
				
				$json['success'] = $this->_('text_success');
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}

	public function autocomplete()
	{
		//Sort
		$sort = $this->sort->getQueryDefaults('name', 'ASC', $this->config->get('config_autocomplete_limit'));
		
		//Filter
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();
		
		//Label and Value
		$label = !empty($_GET['label']) ? $_GET['label'] : 'name';
		$value = !empty($_GET['value']) ? $_GET['value'] : 'customer_id';
		
		//Load Sorted / Filtered Data
		$customers = $this->Model_Sale_Customer->getCustomers($sort + $filter);
		
		foreach ($customers as &$customer) {
			$customer['label'] = $customer[$label];
			$customer['value'] = $customer[$value];
			
			$customer['name'] = html_entity_decode($customer['name'], ENT_QUOTES, 'UTF-8');
			$customer['address'] = $this->Model_Sale_Customer->getAddresses($customer['customer_id']);
		} unset($customer);
		
		//JSON output
		$this->response->setOutput(json_encode($customers));
	}
	
	public function address()
	{
		$json = array();
		
		if (!empty($_GET['address_id'])) {
			$json = $this->Model_Sale_Customer->getAddress($_GET['address_id']);
		}

		$this->response->setOutput(json_encode($json));
	}
}