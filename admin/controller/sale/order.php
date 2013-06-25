<?php
class Admin_Controller_Sale_Order extends Controller 
{
	

  	public function index()
  	{
		$this->load->language('sale/order');

		$this->document->setTitle($this->_('heading_title'));

		$this->getList();
  	}
	
  	public function insert()
  	{
		$this->load->language('sale/order');

		$this->document->setTitle($this->_('heading_title'));

		if ($this->request->isPost() && $this->validateForm()) {
			//TODO - Need to change this so it will actually work when ordering!
			$this->message->add('warning', "This order method is not in use! sale/order/insert.");
			$this->url->redirect($this->url->link('common/home'));
			
			//TODO - we have updated the order method...
			$this->Model_Sale_Order->addOrder($_POST);
			
			$this->message->add('success', $this->_('text_success'));
		
			$url = '';
			
			if (isset($_GET['filter_order_id'])) {
				$url .= '&filter_order_id=' . $_GET['filter_order_id'];
			}
			
			if (isset($_GET['filter_customer'])) {
				$url .= '&filter_customer=' . $_GET['filter_customer'];
			}
												
			if (isset($_GET['filter_order_status_id'])) {
				$url .= '&filter_order_status_id=' . $_GET['filter_order_status_id'];
			}
			
			if (isset($_GET['filter_total'])) {
				$url .= '&filter_total=' . $_GET['filter_total'];
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
			
			$this->url->redirect($this->url->link('sale/order', $url));
		}
		
		$this->getForm();
  	}
	
  	public function update()
  	{
		$this->load->language('sale/order');

		$this->document->setTitle($this->_('heading_title'));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Sale_Order->editOrder($_GET['order_id'], $_POST);
			
			$this->message->add('success', $this->_('text_success'));
	
			$url = '';

			if (isset($_GET['filter_order_id'])) {
				$url .= '&filter_order_id=' . $_GET['filter_order_id'];
			}
			
			if (isset($_GET['filter_customer'])) {
				$url .= '&filter_customer=' . $_GET['filter_customer'];
			}
												
			if (isset($_GET['filter_order_status_id'])) {
				$url .= '&filter_order_status_id=' . $_GET['filter_order_status_id'];
			}
			
			if (isset($_GET['filter_total'])) {
				$url .= '&filter_total=' . $_GET['filter_total'];
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
			
			$this->url->redirect($this->url->link('sale/order', $url));
		}
		
		$this->getForm();
  	}
	
  	public function delete()
  	{
		$this->load->language('sale/order');

		$this->document->setTitle($this->_('heading_title'));

		if (isset($_POST['selected']) && ($this->validateDelete())) {
			foreach ($_POST['selected'] as $order_id) {
				$this->Model_Sale_Order->deleteOrder($order_id);
			}

			$this->message->add('success', $this->_('text_success'));

			$url = '';

			if (isset($_GET['filter_order_id'])) {
				$url .= '&filter_order_id=' . $_GET['filter_order_id'];
			}
			
			if (isset($_GET['filter_customer'])) {
				$url .= '&filter_customer=' . $_GET['filter_customer'];
			}
												
			if (isset($_GET['filter_order_status_id'])) {
				$url .= '&filter_order_status_id=' . $_GET['filter_order_status_id'];
			}
			
			if (isset($_GET['filter_total'])) {
				$url .= '&filter_total=' . $_GET['filter_total'];
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

			$this->url->redirect($this->url->link('sale/order', $url));
		}

		$this->getList();
  	}

  	private function getList()
  	{
		$this->template->load('sale/order_list');
  		
		$query_defaults = array(
			'filter_order_id'		=> null,
			'filter_customer'		=> null,
			'filter_order_status_id' => null,
			'filter_total'			=> null,
			'filter_date_added'		=> null,
			'filter_date_modified'	=> null,
			'sort'						=> 'o.order_id',
			'order'						=> 'ASC',
			'page'						=> 1
		);
		
		foreach ($query_defaults as $key => $default) {
			if (isset($_GET[$key])) {
				$$key = $_GET[$key];
			} else {
				$$key = $default;
			}
		}
		
		$url = $this->url->get_query(array(
			'filter_order_id', 'filter_customer', 'filter_order_status_id',
			'filter_total', 'filter_date_added', 'filter_date_modified',
			'sort','order','page'));

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('sale/order', $url));

		$this->data['invoice'] = $this->url->link('sale/order/invoice');
		$this->data['insert'] = $this->url->link('sale/order/insert');
		$this->data['delete'] = $this->url->link('sale/order/delete', $url);

		$this->data['orders'] = array();

		$data = array(
			'filter_order_id'		=> $filter_order_id,
			'filter_customer'			=> $filter_customer,
			'filter_order_status_id' => $filter_order_status_id,
			'filter_total'			=> $filter_total,
			'filter_date_added'		=> $filter_date_added,
			'filter_date_modified'	=> $filter_date_modified,
			'sort'						=> $sort,
			'order'						=> $order,
			'start'						=> ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'						=> $this->config->get('config_admin_limit')
		);
		
		$order_total = $this->Model_Sale_Order->getTotalOrders($data);

		$results = $this->Model_Sale_Order->getOrders($data);

		foreach ($results as $result) {
			$action = array();
						
			$action[] = array(
				'text' => $this->_('text_view'),
				'href' => $this->url->link('sale/order/info', 'order_id=' . $result['order_id'] . $url)
			);
			
			if (strtotime($result['date_added']) > strtotime('-' . (int)$this->config->get('config_order_edit') . ' day')) {
				$action[] = array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('sale/order/update', 'order_id=' . $result['order_id'] . $url)
				);
			}
			
			$this->data['orders'][] = array(
				'order_id'		=> $result['order_id'],
				'customer'		=> $result['customer'],
				'status'		=> $result['status'],
				'total'			=> $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'date_added'	=> $this->date->format($result['date_added'], $this->language->getInfo('date_format_short')),
				'date_modified' => date($this->language->getInfo('date_format_short'), strtotime($result['date_modified'])),
				'selected'		=> isset($_POST['selected']) && in_array($result['order_id'], $_POST['selected']),
				'action'		=> $action
			);
		}

		$url = '';

		if (isset($_GET['filter_order_id'])) {
			$url .= '&filter_order_id=' . $_GET['filter_order_id'];
		}
		
		if (isset($_GET['filter_customer'])) {
			$url .= '&filter_customer=' . $_GET['filter_customer'];
		}
											
		if (isset($_GET['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $_GET['filter_order_status_id'];
		}
		
		if (isset($_GET['filter_total'])) {
			$url .= '&filter_total=' . $_GET['filter_total'];
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

		$this->data['sort_order'] = $this->url->link('sale/order', 'sort=o.order_id' . $url);
		$this->data['sort_customer'] = $this->url->link('sale/order', 'sort=customer' . $url);
		$this->data['sort_status'] = $this->url->link('sale/order', 'sort=status' . $url);
		$this->data['sort_total'] = $this->url->link('sale/order', 'sort=o.total' . $url);
		$this->data['sort_date_added'] = $this->url->link('sale/order', 'sort=o.date_added' . $url);
		$this->data['sort_date_modified'] = $this->url->link('sale/order', 'sort=o.date_modified' . $url);

		$url = '';

		if (isset($_GET['filter_order_id'])) {
			$url .= '&filter_order_id=' . $_GET['filter_order_id'];
		}
		
		if (isset($_GET['filter_customer'])) {
			$url .= '&filter_customer=' . $_GET['filter_customer'];
		}
											
		if (isset($_GET['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $_GET['filter_order_status_id'];
		}
		
		if (isset($_GET['filter_total'])) {
			$url .= '&filter_total=' . $_GET['filter_total'];
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
		$this->pagination->total = $order_total;
		$this->data['pagination'] = $this->pagination->render();

		$this->data['filter_order_id'] = $filter_order_id;
		$this->data['filter_customer'] = $filter_customer;
		$this->data['filter_order_status_id'] = $filter_order_status_id;
		$this->data['filter_total'] = $filter_total;
		$this->data['filter_date_added'] = $filter_date_added;
		$this->data['filter_date_modified'] = $filter_date_modified;

		$this->data['order_statuses'] = $this->Model_Localisation_OrderStatus->getOrderStatuses();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->data['breadcrumbs'] = $this->breadcrumb->render();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
  	}

  	public function getForm()
  	{
		$this->template->load('sale/order_form');

		if (isset($_GET['order_id'])) {
			$this->data['order_id'] = $_GET['order_id'];
		} else {
			$this->data['order_id'] = 0;
		}
		
		$url = '';

		if (isset($_GET['filter_order_id'])) {
			$url .= '&filter_order_id=' . $_GET['filter_order_id'];
		}
		
		if (isset($_GET['filter_customer'])) {
			$url .= '&filter_customer=' . $_GET['filter_customer'];
		}
											
		if (isset($_GET['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $_GET['filter_order_status_id'];
		}
		
		if (isset($_GET['filter_total'])) {
			$url .= '&filter_total=' . $_GET['filter_total'];
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('sale/order', $url));

		if (!isset($_GET['order_id'])) {
			$this->data['action'] = $this->url->link('sale/order/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('sale/order/update', 'order_id=' . $_GET['order_id'] . $url);
		}
		
		$this->data['cancel'] = $this->url->link('sale/order', $url);

		if (isset($_GET['order_id']) && !$this->request->isPost()) {
				$order_info = $this->Model_Sale_Order->getOrder($_GET['order_id']);
		}
			
		if (isset($_POST['store_id'])) {
				$this->data['store_id'] = $_POST['store_id'];
		} elseif (!empty($order_info)) {
			$this->data['store_id'] = $order_info['store_id'];
		} else {
				$this->data['store_id'] = '';
		}
		
		$this->data['data_stores'] = $this->Model_Setting_Store->getStores();
		
		$this->data['store_url'] = SITE_URL;
		
		if (isset($_POST['customer'])) {
			$this->data['customer'] = $_POST['customer'];
		} elseif (!empty($order_info)) {
			$this->data['customer'] = $order_info['customer'];
		} else {
			$this->data['customer'] = '';
		}
						
		if (isset($_POST['customer_id'])) {
			$this->data['customer_id'] = $_POST['customer_id'];
		} elseif (!empty($order_info)) {
			$this->data['customer_id'] = $order_info['customer_id'];
		} else {
			$this->data['customer_id'] = '';
		}
		
		if (isset($_POST['customer_group_id'])) {
			$this->data['customer_group_id'] = $_POST['customer_group_id'];
		} elseif (!empty($order_info)) {
			$this->data['customer_group_id'] = $order_info['customer_group_id'];
		} else {
			$this->data['customer_group_id'] = '';
		}
						
		if (isset($_POST['firstname'])) {
				$this->data['firstname'] = $_POST['firstname'];
		} elseif (!empty($order_info)) {
			$this->data['firstname'] = $order_info['firstname'];
		} else {
				$this->data['firstname'] = '';
		}

		if (isset($_POST['lastname'])) {
				$this->data['lastname'] = $_POST['lastname'];
		} elseif (!empty($order_info)) {
			$this->data['lastname'] = $order_info['lastname'];
		} else {
				$this->data['lastname'] = '';
		}

		if (isset($_POST['email'])) {
				$this->data['email'] = $_POST['email'];
		} elseif (!empty($order_info)) {
			$this->data['email'] = $order_info['email'];
		} else {
				$this->data['email'] = '';
		}
				
		if (isset($_POST['telephone'])) {
				$this->data['telephone'] = $_POST['telephone'];
		} elseif (!empty($order_info)) {
			$this->data['telephone'] = $order_info['telephone'];
		} else {
				$this->data['telephone'] = '';
		}
		
		if (isset($_POST['fax'])) {
				$this->data['fax'] = $_POST['fax'];
		} elseif (!empty($order_info)) {
			$this->data['fax'] = $order_info['fax'];
		} else {
				$this->data['fax'] = '';
		}
		
		if (isset($_POST['affiliate_id'])) {
				$this->data['affiliate_id'] = $_POST['affiliate_id'];
		} elseif (!empty($order_info)) {
			$this->data['affiliate_id'] = $order_info['affiliate_id'];
		} else {
				$this->data['affiliate_id'] = '';
		}
		
		if (isset($_POST['affiliate'])) {
				$this->data['affiliate'] = $_POST['affiliate'];
		} elseif (!empty($order_info)) {
			$this->data['affiliate'] = ($order_info['affiliate_id'] ? $order_info['affiliate_firstname'] . ' ' . $order_info['affiliate_lastname'] : '');
		} else {
				$this->data['affiliate'] = '';
		}
				
		if (isset($_POST['order_status_id'])) {
				$this->data['order_status_id'] = $_POST['order_status_id'];
		} elseif (!empty($order_info)) {
			$this->data['order_status_id'] = $order_info['order_status_id'];
		} else {
				$this->data['order_status_id'] = '';
		}
			
		$this->data['order_statuses'] = $this->Model_Localisation_OrderStatus->getOrderStatuses();
			
		if (isset($_POST['comment'])) {
				$this->data['comment'] = $_POST['comment'];
		} elseif (!empty($order_info)) {
			$this->data['comment'] = $order_info['comment'];
		} else {
				$this->data['comment'] = '';
		}
		
		if (isset($_POST['customer_id'])) {
			$this->data['addresses'] = $this->Model_Sale_Customer->getAddresses($_POST['customer_id']);
		} elseif (!empty($order_info)) {
			$this->data['addresses'] = $this->Model_Sale_Customer->getAddresses($order_info['customer_id']);
		} else {
			$this->data['addresses'] = array();
		}
			
		if (isset($_POST['shipping_firstname'])) {
				$this->data['shipping_firstname'] = $_POST['shipping_firstname'];
		} elseif (!empty($order_info)) {
			$this->data['shipping_firstname'] = $order_info['shipping_firstname'];
		} else {
				$this->data['shipping_firstname'] = '';
		}

		if (isset($_POST['shipping_lastname'])) {
				$this->data['shipping_lastname'] = $_POST['shipping_lastname'];
		} elseif (!empty($order_info)) {
			$this->data['shipping_lastname'] = $order_info['shipping_lastname'];
		} else {
				$this->data['shipping_lastname'] = '';
		}

		if (isset($_POST['shipping_company'])) {
				$this->data['shipping_company'] = $_POST['shipping_company'];
		} elseif (!empty($order_info)) {
			$this->data['shipping_company'] = $order_info['shipping_company'];
		} else {
				$this->data['shipping_company'] = '';
		}

		if (isset($_POST['shipping_address_1'])) {
				$this->data['shipping_address_1'] = $_POST['shipping_address_1'];
		} elseif (!empty($order_info)) {
			$this->data['shipping_address_1'] = $order_info['shipping_address_1'];
		} else {
				$this->data['shipping_address_1'] = '';
		}

		if (isset($_POST['shipping_address_2'])) {
				$this->data['shipping_address_2'] = $_POST['shipping_address_2'];
		} elseif (!empty($order_info)) {
			$this->data['shipping_address_2'] = $order_info['shipping_address_2'];
		} else {
				$this->data['shipping_address_2'] = '';
		}
		
		if (isset($_POST['shipping_city'])) {
				$this->data['shipping_city'] = $_POST['shipping_city'];
		} elseif (!empty($order_info)) {
			$this->data['shipping_city'] = $order_info['shipping_city'];
		} else {
				$this->data['shipping_city'] = '';
		}
		
		if (isset($_POST['shipping_postcode'])) {
				$this->data['shipping_postcode'] = $_POST['shipping_postcode'];
		} elseif (!empty($order_info)) {
			$this->data['shipping_postcode'] = $order_info['shipping_postcode'];
		} else {
				$this->data['shipping_postcode'] = '';
		}
				
		if (isset($_POST['shipping_country_id'])) {
				$this->data['shipping_country_id'] = $_POST['shipping_country_id'];
		} elseif (!empty($order_info)) {
			$this->data['shipping_country_id'] = $order_info['shipping_country_id'];
		} else {
				$this->data['shipping_country_id'] = '';
		}
		
		if (isset($_POST['shipping_zone_id'])) {
				$this->data['shipping_zone_id'] = $_POST['shipping_zone_id'];
		} elseif (!empty($order_info)) {
			$this->data['shipping_zone_id'] = $order_info['shipping_zone_id'];
		} else {
				$this->data['shipping_zone_id'] = '';
		}
				
		if (isset($_POST['payment_firstname'])) {
				$this->data['payment_firstname'] = $_POST['payment_firstname'];
		} elseif (!empty($order_info)) {
			$this->data['payment_firstname'] = $order_info['payment_firstname'];
		} else {
				$this->data['payment_firstname'] = '';
		}

		if (isset($_POST['payment_lastname'])) {
				$this->data['payment_lastname'] = $_POST['payment_lastname'];
		} elseif (!empty($order_info)) {
			$this->data['payment_lastname'] = $order_info['payment_lastname'];
		} else {
				$this->data['payment_lastname'] = '';
		}

		if (isset($_POST['payment_company'])) {
				$this->data['payment_company'] = $_POST['payment_company'];
		} elseif (!empty($order_info)) {
			$this->data['payment_company'] = $order_info['payment_company'];
		} else {
				$this->data['payment_company'] = '';
		}

		if (isset($_POST['payment_address_1'])) {
				$this->data['payment_address_1'] = $_POST['payment_address_1'];
		} elseif (!empty($order_info)) {
			$this->data['payment_address_1'] = $order_info['payment_address_1'];
		} else {
				$this->data['payment_address_1'] = '';
		}

		if (isset($_POST['payment_address_2'])) {
				$this->data['payment_address_2'] = $_POST['payment_address_2'];
		} elseif (!empty($order_info)) {
			$this->data['payment_address_2'] = $order_info['payment_address_2'];
		} else {
				$this->data['payment_address_2'] = '';
		}
		
		if (isset($_POST['payment_city'])) {
				$this->data['payment_city'] = $_POST['payment_city'];
		} elseif (!empty($order_info)) {
			$this->data['payment_city'] = $order_info['payment_city'];
		} else {
				$this->data['payment_city'] = '';
		}

		if (isset($_POST['payment_postcode'])) {
				$this->data['payment_postcode'] = $_POST['payment_postcode'];
		} elseif (!empty($order_info)) {
			$this->data['payment_postcode'] = $order_info['payment_postcode'];
		} else {
				$this->data['payment_postcode'] = '';
		}
				
		if (isset($_POST['payment_country_id'])) {
				$this->data['payment_country_id'] = $_POST['payment_country_id'];
		} elseif (!empty($order_info)) {
			$this->data['payment_country_id'] = $order_info['payment_country_id'];
		} else {
				$this->data['payment_country_id'] = '';
		}
		
		if (isset($_POST['payment_zone_id'])) {
				$this->data['payment_zone_id'] = $_POST['payment_zone_id'];
		} elseif (!empty($order_info)) {
			$this->data['payment_zone_id'] = $order_info['payment_zone_id'];
		} else {
				$this->data['payment_zone_id'] = '';
		}
		
		$this->data['countries'] = $this->Model_Localisation_Country->getCountries();
		
		if (isset($_POST['shipping_method'])) {
				$this->data['shipping_method'] = $_POST['shipping_method'];
		} elseif (!empty($order_info)) {
			$this->data['shipping_method'] = $order_info['shipping_method'];
		} else {
				$this->data['shipping_method'] = '';
		}
		
		if (isset($_POST['shipping_code'])) {
				$this->data['shipping_code'] = $_POST['shipping_code'];
		} elseif (!empty($order_info)) {
			$this->data['shipping_code'] = $order_info['shipping_code'];
		} else {
				$this->data['shipping_code'] = '';
		}
						
		if (isset($_POST['payment_method'])) {
				$this->data['payment_method'] = $_POST['payment_method'];
		} elseif (!empty($order_info)) {
			$this->data['payment_method'] = $order_info['payment_method'];
		} else {
				$this->data['payment_method'] = '';
		}
		
		if (isset($_POST['payment_code'])) {
				$this->data['payment_code'] = $_POST['payment_code'];
		} elseif (!empty($order_info)) {
			$this->data['payment_code'] = $order_info['payment_code'];
		} else {
				$this->data['payment_code'] = '';
		}
	
		if (isset($_POST['order_product'])) {
			$order_products = $_POST['order_product'];
		} elseif (isset($_GET['order_id'])) {
			$order_products = $this->Model_Sale_Order->getOrderProducts($_GET['order_id']);
		} else {
			$order_products = array();
		}
		
		$this->document->addScript('view/javascript/jquery/ajaxupload.js');
		
		$this->data['order_products'] = array();
		
		foreach ($order_products as $order_product) {
			if (isset($order_product['order_option'])) {
				$order_option = $order_product['order_option'];
			} elseif (isset($_GET['order_id'])) {
				$order_option = $this->Model_Sale_Order->getOrderOptions($_GET['order_id'], $order_product['order_product_id']);
			} else {
				$order_option = array();
			}

			if (isset($order_product['order_download'])) {
				$order_download = $order_product['order_download'];
			} elseif (isset($_GET['order_id'])) {
				$order_download = $this->Model_Sale_Order->getOrderDownloads($_GET['order_id'], $order_product['order_product_id']);
			} else {
				$order_download = array();
			}
											
			$this->data['order_products'][] = array(
				'order_product_id' => $order_product['order_product_id'],
				'product_id'		=> $order_product['product_id'],
				'name'				=> $order_product['name'],
				'model'				=> $order_product['model'],
				'option'			=> $order_option,
				'download'			=> $order_download,
				'quantity'			=> $order_product['quantity'],
				'price'				=> $order_product['price'],
				'total'				=> $order_product['total'],
				'tax'				=> $order_product['tax'],
				'reward'			=> $order_product['reward']
			);
		}
		
		if (isset($_POST['order_voucher'])) {
			$this->data['order_vouchers'] = $_POST['order_voucher'];
		} elseif (isset($_GET['order_id'])) {
			$this->data['order_vouchers'] = $this->Model_Sale_Order->getOrderVouchers($_GET['order_id']);
		} else {
			$this->data['order_vouchers'] = array();
		}
		
		$this->data['voucher_themes'] = $this->Model_Sale_VoucherTheme->getVoucherThemes();
						
		if (isset($_POST['order_total'])) {
				$this->data['order_totals'] = $_POST['order_total'];
		} elseif (isset($_GET['order_id'])) {
			$this->data['order_totals'] = $this->Model_Sale_Order->getOrderTotals($_GET['order_id']);
		} else {
				$this->data['order_totals'] = array();
		}
		
		$this->data['breadcrumbs'] = $this->breadcrumb->render();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
  	}
	
  	private function validateForm()
  	{
		if (!$this->user->hasPermission('modify', 'sale/order')) {
				$this->error['warning'] = $this->_('error_permission');
		}

		if ((strlen($_POST['firstname']) < 1) || (strlen($_POST['firstname']) > 32)) {
				$this->error['firstname'] = $this->_('error_firstname');
		}

		if ((strlen($_POST['lastname']) < 1) || (strlen($_POST['lastname']) > 32)) {
				$this->error['lastname'] = $this->_('error_lastname');
		}

		if ((strlen($_POST['email']) > 96) || (!preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['email']))) {
				$this->error['email'] = $this->_('error_email');
		}
		
		if ((strlen($_POST['telephone']) < 3) || (strlen($_POST['telephone']) > 32)) {
				$this->error['telephone'] = $this->_('error_telephone');
		}
		
		if ((strlen($_POST['payment_firstname']) < 1) || (strlen($_POST['payment_firstname']) > 32)) {
				$this->error['payment_firstname'] = $this->_('error_firstname');
		}

		if ((strlen($_POST['payment_lastname']) < 1) || (strlen($_POST['payment_lastname']) > 32)) {
				$this->error['payment_lastname'] = $this->_('error_lastname');
		}

		if ((strlen($_POST['payment_address_1']) < 3) || (strlen($_POST['payment_address_1']) > 128)) {
				$this->error['payment_address_1'] = $this->_('error_address_1');
		}

		if ((strlen($_POST['payment_city']) < 3) || (strlen($_POST['payment_city']) > 128)) {
				$this->error['payment_city'] = $this->_('error_city');
		}
		
		$country_info = $this->Model_Localisation_Country->getCountry($_POST['payment_country_id']);
		
		if ($country_info && $country_info['postcode_required'] && (strlen($_POST['payment_postcode']) < 2) || (strlen($_POST['payment_postcode']) > 10)) {
			$this->error['payment_postcode'] = $this->_('error_postcode');
		}

		if ($_POST['payment_country_id'] == '') {
				$this->error['payment_country'] = $this->_('error_country');
		}
		
		if ($_POST['payment_zone_id'] == '') {
				$this->error['payment_zone'] = $this->_('error_zone');
		}
		
		// Check if any products require shipping
		$shipping = false;
		
		if (isset($_POST['order_product'])) {
			foreach ($_POST['order_product'] as $order_product) {
				$product_info = $this->Model_Catalog_Product->getProduct($order_product['product_id']);
			
				if ($product_info && $product_info['shipping']) {
					$shipping = true;
				}
			}
		}
		
		if ($shipping) {
			if ((strlen($_POST['shipping_firstname']) < 1) || (strlen($_POST['shipping_firstname']) > 32)) {
				$this->error['shipping_firstname'] = $this->_('error_firstname');
			}
	
			if ((strlen($_POST['shipping_lastname']) < 1) || (strlen($_POST['shipping_lastname']) > 32)) {
				$this->error['shipping_lastname'] = $this->_('error_lastname');
			}
			
			if ((strlen($_POST['shipping_address_1']) < 3) || (strlen($_POST['shipping_address_1']) > 128)) {
				$this->error['shipping_address_1'] = $this->_('error_address_1');
			}
	
			if ((strlen($_POST['shipping_city']) < 3) || (strlen($_POST['shipping_city']) > 128)) {
				$this->error['shipping_city'] = $this->_('error_city');
			}
	
			$country_info = $this->Model_Localisation_Country->getCountry($_POST['shipping_country_id']);
			
			if ($country_info && $country_info['postcode_required'] && (strlen($_POST['shipping_postcode']) < 2) || (strlen($_POST['shipping_postcode']) > 10)) {
				$this->error['shipping_postcode'] = $this->_('error_postcode');
			}
	
			if ($_POST['shipping_country_id'] == '') {
				$this->error['shipping_country'] = $this->_('error_country');
			}
			
			if ($_POST['shipping_zone_id'] == '') {
				$this->error['shipping_zone'] = $this->_('error_zone');
			}
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->_('error_warning');
		}
		
		return $this->error ? false : true;
  	}
	
		private function validateDelete()
		{
		if (!$this->user->hasPermission('modify', 'sale/order')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		return $this->error ? false : true;
  	}
	
	public function info()
	{
		if (isset($_GET['order_id'])) {
			$order_id = $_GET['order_id'];
		} else {
			$order_id = 0;
		}

		$order_info = $this->Model_Sale_Order->getOrder($order_id);

		if ($order_info) {
		$this->template->load('sale/order_info');
			$this->load->language('sale/order');

			$this->document->setTitle($this->_('heading_title'));

			$url = '';

			if (isset($_GET['filter_order_id'])) {
				$url .= '&filter_order_id=' . $_GET['filter_order_id'];
			}
			
			if (isset($_GET['filter_customer'])) {
				$url .= '&filter_customer=' . $_GET['filter_customer'];
			}
												
			if (isset($_GET['filter_order_status_id'])) {
				$url .= '&filter_order_status_id=' . $_GET['filter_order_status_id'];
			}
			
			if (isset($_GET['filter_total'])) {
				$url .= '&filter_total=' . $_GET['filter_total'];
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
				$this->breadcrumb->add($this->_('heading_title'), $this->url->link('sale/order', $url));

			$this->data['invoice'] = $this->url->link('sale/order/invoice', 'order_id=' . (int)$_GET['order_id']);
			$this->data['cancel'] = $this->url->link('sale/order', $url);

			$this->data['order_id'] = $_GET['order_id'];
			
			if ($order_info['invoice_no']) {
				$this->data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
			} else {
				$this->data['invoice_no'] = '';
			}
			
			$this->data['store_name'] = $order_info['store_name'];
			$this->data['store_url'] = $order_info['store_url'];
			$this->data['firstname'] = $order_info['firstname'];
			$this->data['lastname'] = $order_info['lastname'];
						
			if ($order_info['customer_id']) {
				$this->data['customer'] = $this->url->link('sale/customer/update', 'customer_id=' . $order_info['customer_id']);
			} else {
				$this->data['customer'] = '';
			}

			$customer_group_info = $this->Model_Sale_CustomerGroup->getCustomerGroup($order_info['customer_group_id']);

			if ($customer_group_info) {
				$this->data['customer_group'] = $customer_group_info['name'];
			} else {
				$this->data['customer_group'] = '';
			}

			$this->data['email'] = $order_info['email'];
			$this->data['telephone'] = $order_info['telephone'];
			$this->data['fax'] = $order_info['fax'];
			$this->data['comment'] = nl2br($order_info['comment']);
			$this->data['shipping_method'] = $order_info['shipping_method'];
			$this->data['payment_method'] = $order_info['payment_method'];
			$this->data['total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']);
			
			if ($order_info['total'] < 0) {
				$this->data['credit'] = $order_info['total'];
			} else {
				$this->data['credit'] = 0;
			}
			
			$this->data['credit_total'] = $this->Model_Sale_Customer->getTotalTransactionsByOrderId($_GET['order_id']);
			
			$this->data['reward'] = $order_info['reward'];
						
			$this->data['reward_total'] = $this->Model_Sale_Customer->getTotalCustomerRewardsByOrderId($_GET['order_id']);

			$this->data['affiliate_firstname'] = $order_info['affiliate_firstname'];
			$this->data['affiliate_lastname'] = $order_info['affiliate_lastname'];
			
			if ($order_info['affiliate_id']) {
				$this->data['affiliate'] = $this->url->link('sale/affiliate/update', 'affiliate_id=' . $order_info['affiliate_id']);
			} else {
				$this->data['affiliate'] = '';
			}
			
			$this->data['commission'] = $this->currency->format($order_info['commission'], $order_info['currency_code'], $order_info['currency_value']);
						
			$this->data['commission_total'] = $this->Model_Sale_Affiliate->getTotalTransactionsByOrderId($_GET['order_id']);

			$order_status_info = $this->Model_Localisation_OrderStatus->getOrderStatus($order_info['order_status_id']);

			if ($order_status_info) {
				$this->data['order_status'] = $order_status_info['name'];
			} else {
				$this->data['order_status'] = '';
			}
			
			$this->data += $order_info;
			$this->data['date_added'] = date($this->language->getInfo('date_format_short'), strtotime($order_info['date_added']));
			$this->data['date_modified'] = date($this->language->getInfo('date_format_short'), strtotime($order_info['date_modified']));

			$this->data['products'] = array();

			$products = $this->Model_Sale_Order->getOrderProducts($_GET['order_id']);

			foreach ($products as $product) {
				$option_data = array();

				$options = $this->Model_Sale_Order->getOrderOptions($_GET['order_id'], $product['order_product_id']);
				
				foreach ($options as $option) {
					if ($option['type'] != 'file') {
						$option_data[] = array(
							'name'  => $option['name'],
							'value' => $option['value'],
							'type'  => $option['type']
						);
					} else {
						$option_data[] = array(
							'name'  => $option['name'],
							'value' => substr($option['value'], 0, strrpos($option['value'], '.')),
							'type'  => $option['type'],
							'href'  => $this->url->link('sale/order/download', 'order_id=' . $_GET['order_id'] . '&order_option_id=' . $option['order_option_id'])
						);
					}
				}
				
				$this->data['products'][] = array(
					'order_product_id' => $product['order_product_id'],
					'product_id'		=> $product['product_id'],
					'name'				=> $product['name'],
					'model'				=> $product['model'],
					'option'				=> $option_data,
					'quantity'			=> $product['quantity'],
					'price'				=> $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']),
					'total'				=> $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value']),
					'href'				=> $this->url->link('catalog/product/update', 'product_id=' . $product['product_id'])
				);
			}
		
			$this->data['vouchers'] = array();
			
			$vouchers = $this->Model_Sale_Order->getOrderVouchers($_GET['order_id']);
			
			foreach ($vouchers as $voucher) {
				$this->data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'		=> $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
					'href'		=> $this->url->link('sale/voucher/update', 'voucher_id=' . $voucher['voucher_id'])
				);
			}
		
			$this->data['totals'] = $this->Model_Sale_Order->getOrderTotals($_GET['order_id']);

			$this->data['downloads'] = array();

			foreach ($products as $product) {
				$results = $this->Model_Sale_Order->getOrderDownloads($_GET['order_id'], $product['order_product_id']);
	
				foreach ($results as $result) {
					$this->data['downloads'][] = array(
						'name'		=> $result['name'],
						'filename'  => $result['mask'],
						'remaining' => $result['remaining']
					);
				}
			}
			
			$this->data['order_statuses'] = $this->Model_Localisation_OrderStatus->getOrderStatuses();

			$this->data['order_status_id'] = $order_info['order_status_id'];

			// Fraud
			$fraud_info = $this->Model_Sale_Fraud->getFraud($order_info['order_id']);
			
			if ($fraud_info) {
				$this->data['country_match'] = $fraud_info['country_match'];
				
				if ($fraud_info['country_code']) {
					$this->data['country_code'] = $fraud_info['country_code'];
				} else {
					$this->data['country_code'] = '';
				}
				
				$this->data['high_risk_country'] = $fraud_info['high_risk_country'];
				$this->data['distance'] = $fraud_info['distance'];
				
				if ($fraud_info['ip_region']) {
					$this->data['ip_region'] = $fraud_info['ip_region'];
				} else {
					$this->data['ip_region'] = '';
				}
								
				if ($fraud_info['ip_city']) {
					$this->data['ip_city'] = $fraud_info['ip_city'];
				} else {
					$this->data['ip_city'] = '';
				}
				
				$this->data['ip_latitude'] = $fraud_info['ip_latitude'];
				$this->data['ip_longitude'] = $fraud_info['ip_longitude'];

				if ($fraud_info['ip_isp']) {
					$this->data['ip_isp'] = $fraud_info['ip_isp'];
				} else {
					$this->data['ip_isp'] = '';
				}
				
				if ($fraud_info['ip_org']) {
					$this->data['ip_org'] = $fraud_info['ip_org'];
				} else {
					$this->data['ip_org'] = '';
				}
								
				$this->data['ip_asnum'] = $fraud_info['ip_asnum'];
				
				if ($fraud_info['ip_user_type']) {
					$this->data['ip_user_type'] = $fraud_info['ip_user_type'];
				} else {
					$this->data['ip_user_type'] = '';
				}
				
				if ($fraud_info['ip_country_confidence']) {
					$this->data['ip_country_confidence'] = $fraud_info['ip_country_confidence'];
				} else {
					$this->data['ip_country_confidence'] = '';
				}
												
				if ($fraud_info['ip_region_confidence']) {
					$this->data['ip_region_confidence'] = $fraud_info['ip_region_confidence'];
				} else {
					$this->data['ip_region_confidence'] = '';
				}
				
				if ($fraud_info['ip_city_confidence']) {
					$this->data['ip_city_confidence'] = $fraud_info['ip_city_confidence'];
				} else {
					$this->data['ip_city_confidence'] = '';
				}
				
				if ($fraud_info['ip_postal_confidence']) {
					$this->data['ip_postal_confidence'] = $fraud_info['ip_postal_confidence'];
				} else {
					$this->data['ip_postal_confidence'] = '';
				}
				
				if ($fraud_info['ip_postal_code']) {
					$this->data['ip_postal_code'] = $fraud_info['ip_postal_code'];
				} else {
					$this->data['ip_postal_code'] = '';
				}
								
				$this->data['ip_accuracy_radius'] = $fraud_info['ip_accuracy_radius'];
				
				if ($fraud_info['ip_net_speed_cell']) {
					$this->data['ip_net_speed_cell'] = $fraud_info['ip_net_speed_cell'];
				} else {
					$this->data['ip_net_speed_cell'] = '';
				}
								
				$this->data['ip_metro_code'] = $fraud_info['ip_metro_code'];
				$this->data['ip_area_code'] = $fraud_info['ip_area_code'];
				
				if ($fraud_info['ip_time_zone']) {
					$this->data['ip_time_zone'] = $fraud_info['ip_time_zone'];
				} else {
					$this->data['ip_time_zone'] = '';
				}

				if ($fraud_info['ip_region_name']) {
					$this->data['ip_region_name'] = $fraud_info['ip_region_name'];
				} else {
					$this->data['ip_region_name'] = '';
				}
				
				if ($fraud_info['ip_domain']) {
					$this->data['ip_domain'] = $fraud_info['ip_domain'];
				} else {
					$this->data['ip_domain'] = '';
				}
				
				if ($fraud_info['ip_country_name']) {
					$this->data['ip_country_name'] = $fraud_info['ip_country_name'];
				} else {
					$this->data['ip_country_name'] = '';
				}
								
				if ($fraud_info['ip_continent_code']) {
					$this->data['ip_continent_code'] = $fraud_info['ip_continent_code'];
				} else {
					$this->data['ip_continent_code'] = '';
				}
				
				if ($fraud_info['ip_corporate_proxy']) {
					$this->data['ip_corporate_proxy'] = $fraud_info['ip_corporate_proxy'];
				} else {
					$this->data['ip_corporate_proxy'] = '';
				}
								
				$this->data['anonymous_proxy'] = $fraud_info['anonymous_proxy'];
				$this->data['proxy_score'] = $fraud_info['proxy_score'];
				
				if ($fraud_info['is_trans_proxy']) {
					$this->data['is_trans_proxy'] = $fraud_info['is_trans_proxy'];
				} else {
					$this->data['is_trans_proxy'] = '';
				}
							
				$this->data['free_mail'] = $fraud_info['free_mail'];
				$this->data['carder_email'] = $fraud_info['carder_email'];
				
				if ($fraud_info['high_risk_username']) {
					$this->data['high_risk_username'] = $fraud_info['high_risk_username'];
				} else {
					$this->data['high_risk_username'] = '';
				}
							
				if ($fraud_info['high_risk_password']) {
					$this->data['high_risk_password'] = $fraud_info['high_risk_password'];
				} else {
					$this->data['high_risk_password'] = '';
				}
				
				$this->data['bin_match'] = $fraud_info['bin_match'];

				if ($fraud_info['bin_country']) {
					$this->data['bin_country'] = $fraud_info['bin_country'];
				} else {
					$this->data['bin_country'] = '';
				}
								
				$this->data['bin_name_match'] = $fraud_info['bin_name_match'];
				
				if ($fraud_info['bin_name']) {
					$this->data['bin_name'] = $fraud_info['bin_name'];
				} else {
					$this->data['bin_name'] = '';
				}
								
				$this->data['bin_phone_match'] = $fraud_info['bin_phone_match'];

				if ($fraud_info['bin_phone']) {
					$this->data['bin_phone'] = $fraud_info['bin_phone'];
				} else {
					$this->data['bin_phone'] = '';
				}
				
				if ($fraud_info['customer_phone_in_billing_location']) {
					$this->data['customer_phone_in_billing_location'] = $fraud_info['customer_phone_in_billing_location'];
				} else {
					$this->data['customer_phone_in_billing_location'] = '';
				}
												
				$this->data['ship_forward'] = $fraud_info['ship_forward'];

				if ($fraud_info['city_postal_match']) {
					$this->data['city_postal_match'] = $fraud_info['city_postal_match'];
				} else {
					$this->data['city_postal_match'] = '';
				}
				
				if ($fraud_info['ship_city_postal_match']) {
					$this->data['ship_city_postal_match'] = $fraud_info['ship_city_postal_match'];
				} else {
					$this->data['ship_city_postal_match'] = '';
				}
								
				$this->data['score'] = $fraud_info['score'];
				$this->data['explanation'] = $fraud_info['explanation'];
				$this->data['risk_score'] = $fraud_info['risk_score'];
				$this->data['queries_remaining'] = $fraud_info['queries_remaining'];
				$this->data['maxmind_id'] = $fraud_info['maxmind_id'];
				$this->data['error'] = $fraud_info['error'];
			} else {
				$this->data['maxmind_id'] = '';
			}
			
			$this->data['breadcrumbs'] = $this->breadcrumb->render();
			
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

			$this->data['breadcrumbs'] = $this->breadcrumb->render();
			
			$this->children = array(
				'common/header',
				'common/footer'
			);
		
			$this->response->setOutput($this->render());
		}
	}

	public function createInvoiceNo()
	{
		$this->language->load('sale/order');

		$json = array();
		
		if (!$this->user->hasPermission('modify', 'sale/order')) {
				$json['error'] = $this->_('error_permission');
		} elseif (isset($_GET['order_id'])) {
			$invoice_no = $this->Model_Sale_Order->createInvoiceNo($_GET['order_id']);
			
			if ($invoice_no) {
				$json['invoice_no'] = $invoice_no;
			} else {
				$json['error'] = $this->_('error_action');
			}
		}

		$this->response->setOutput(json_encode($json));
  	}

	public function addCredit()
	{
		$this->language->load('sale/order');
		
		$json = array();
		
		if (!$this->user->hasPermission('modify', 'sale/order')) {
				$json['error'] = $this->_('error_permission');
		} elseif (isset($_GET['order_id'])) {
			$order_info = $this->Model_Sale_Order->getOrder($_GET['order_id']);
			
			if ($order_info && $order_info['customer_id']) {
				$credit_total = $this->Model_Sale_Customer->getTotalTransactionsByOrderId($_GET['order_id']);
				
				if (!$credit_total) {
					$this->Model_Sale_Customer->addTransaction($order_info['customer_id'], $this->_('text_order_id') . ' #' . $_GET['order_id'], $order_info['total'], $_GET['order_id']);
					
					$json['success'] = $this->_('text_credit_added');
				} else {
					$json['error'] = $this->_('error_action');
				}
			}
		}
		
		$this->response->setOutput(json_encode($json));
  	}
	
	public function removeCredit()
	{
		$this->language->load('sale/order');
		
		$json = array();
		
		if (!$this->user->hasPermission('modify', 'sale/order')) {
				$json['error'] = $this->_('error_permission');
		} elseif (isset($_GET['order_id'])) {
			$order_info = $this->Model_Sale_Order->getOrder($_GET['order_id']);
			
			if ($order_info && $order_info['customer_id']) {
				$this->Model_Sale_Customer->deleteTransaction($_GET['order_id']);
					
				$json['success'] = $this->_('text_credit_removed');
			} else {
				$json['error'] = $this->_('error_action');
			}
		}
		
		$this->response->setOutput(json_encode($json));
  	}
				
	public function addReward()
	{
		$this->language->load('sale/order');
		
		$json = array();
		
		if (!$this->user->hasPermission('modify', 'sale/order')) {
				$json['error'] = $this->_('error_permission');
		} elseif (isset($_GET['order_id'])) {
			$order_info = $this->Model_Sale_Order->getOrder($_GET['order_id']);
			
			if ($order_info && $order_info['customer_id']) {
				$reward_total = $this->Model_Sale_Customer->getTotalCustomerRewardsByOrderId($_GET['order_id']);
				
				if (!$reward_total) {
					$this->Model_Sale_Customer->addReward($order_info['customer_id'], $this->_('text_order_id') . ' #' . $_GET['order_id'], $order_info['reward'], $_GET['order_id']);
					
					$json['success'] = $this->_('text_reward_added');
				} else {
					$json['error'] = $this->_('error_action');
				}
			} else {
				$json['error'] = $this->_('error_action');
			}
		}
		
		$this->response->setOutput(json_encode($json));
  	}
	
	public function removeReward()
	{
		$this->language->load('sale/order');
		
		$json = array();
		
		if (!$this->user->hasPermission('modify', 'sale/order')) {
				$json['error'] = $this->_('error_permission');
		} elseif (isset($_GET['order_id'])) {
			$order_info = $this->Model_Sale_Order->getOrder($_GET['order_id']);
			
			if ($order_info && $order_info['customer_id']) {
				$this->Model_Sale_Customer->deleteReward($_GET['order_id']);
				
				$json['success'] = $this->_('text_reward_removed');
			} else {
				$json['error'] = $this->_('error_action');
			}
		}
		
		$this->response->setOutput(json_encode($json));
  	}
		
	public function addCommission()
	{
		$this->language->load('sale/order');
		
		$json = array();
		
		if (!$this->user->hasPermission('modify', 'sale/order')) {
				$json['error'] = $this->_('error_permission');
		} elseif (isset($_GET['order_id'])) {
			$order_info = $this->Model_Sale_Order->getOrder($_GET['order_id']);
			
			if ($order_info && $order_info['affiliate_id']) {
				$affiliate_total = $this->Model_Sale_Affiliate->getTotalTransactionsByOrderId($_GET['order_id']);
				
				if (!$affiliate_total) {
					$this->Model_Sale_Affiliate->addTransaction($order_info['affiliate_id'], $this->_('text_order_id') . ' #' . $_GET['order_id'], $order_info['commission'], $_GET['order_id']);
					
					$json['success'] = $this->_('text_commission_added');
				} else {
					$json['error'] = $this->_('error_action');
				}
			} else {
				$json['error'] = $this->_('error_action');
			}
		}
		
		$this->response->setOutput(json_encode($json));
  	}
	
	public function removeCommission()
	{
		$this->language->load('sale/order');
		
		$json = array();
		
		if (!$this->user->hasPermission('modify', 'sale/order')) {
				$json['error'] = $this->_('error_permission');
		} elseif (isset($_GET['order_id'])) {
			$order_info = $this->Model_Sale_Order->getOrder($_GET['order_id']);
			
			if ($order_info && $order_info['affiliate_id']) {
				$this->Model_Sale_Affiliate->deleteTransaction($_GET['order_id']);
				
				$json['success'] = $this->_('text_commission_removed');
			} else {
				$json['error'] = $this->_('error_action');
			}
		}
		
		$this->response->setOutput(json_encode($json));
  	}

	public function history()
	{
		
		//TODO: implement this
		echo "This page has not yet been ipmlemented!";
		exit;
		
		$this->template->load('sale/order_history');

		$this->language->load('sale/order');
		
		if ($this->request->isPost() && $this->user->hasPermission('modify', 'sale/order')) {
			$this->Model_Sale_Order->addOrderHistory($_GET['order_id'], $_POST);
				
			$this->message->add('success', $this->_('text_success'));
		}
		
		if ($this->request->isPost() && !$this->user->hasPermission('modify', 'sale/order')) {
			$this->message->add('warning', $this->_('error_permission'));
		}
				
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
		
		$this->data['histories'] = array();
			
		$results = $this->Model_Sale_Order->getOrderHistories($_GET['order_id'], ($page - 1) * 10, 10);
				
		foreach ($results as $result) {
			$this->data['histories'][] = array(
				'notify'	=> $result['notify'] ? $this->_('text_yes') : $this->_('text_no'),
				'status'	=> $result['status'],
				'comment'	=> nl2br($result['comment']),
				'date_added' => $this->date->format($result['date_added'], $this->language->getInfo('date_format_short')),
			);
			}
		
		$history_total = $this->Model_Sale_Order->getTotalOrderHistories($_GET['order_id']);
			
		$this->pagination->init();
		$this->pagination->total = $history_total;
		$this->data['pagination'] = $this->pagination->render();
		
		
		$this->response->setOutput($this->render());
  	}
	
	public function download()
	{
		if (isset($_GET['order_option_id'])) {
			$order_option_id = $_GET['order_option_id'];
		} else {
			$order_option_id = 0;
		}
		
		$option_info = $this->Model_Sale_Order->getOrderOption($_GET['order_id'], $order_option_id);
		
		if ($option_info && $option_info['type'] == 'file') {
			$file = DIR_DOWNLOAD . $option_info['value'];
			$mask = basename(substr($option_info['value'], 0, strrpos($option_info['value'], '.')));

			if (!headers_sent()) {
				if (file_exists($file)) {
					header('Content-Type: application/octet-stream');
					header('Content-Description: File Transfer');
					header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));
					
					readfile($file, 'rb');
					exit;
				} else {
					exit('Error: Could not find file ' . $file . '!');
				}
			} else {
				exit('Error: Headers already sent out!');
			}
		} else {
		$this->template->load('error/not_found');

			$this->load->language('error/not_found');

			$this->document->setTitle($this->_('heading_title'));

				$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
				$this->breadcrumb->add($this->_('heading_title'), $this->url->link('error/not_found'));

			$this->data['breadcrumbs'] = $this->breadcrumb->render();
			
			$this->children = array(
				'common/header',
				'common/footer'
			);
		
			$this->response->setOutput($this->render());
		}
	}

	public function upload()
	{
		$this->language->load('sale/order');
		
		$json = array();
		
		if ($this->request->isPost()) {
			if (!empty($_FILES['file']['name'])) {
				$filename = html_entity_decode($_FILES['file']['name'], ENT_QUOTES, 'UTF-8');
				
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
		
			if (!isset($json['error'])) {
				if (is_uploaded_file($_FILES['file']['tmp_name']) && file_exists($_FILES['file']['tmp_name'])) {
					$file = basename($filename) . '.' . md5(rand());
					
					$json['file'] = $file;
					
					move_uploaded_file($_FILES['file']['tmp_name'], DIR_DOWNLOAD . $file);
				}
							
				$json['success'] = $this->_('text_upload');
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
			
  	public function invoice()
  	{
		$this->template->load('sale/order_invoice');

		$this->load->language('sale/order');

		$this->language->set('title', $this->_('heading_title'));

		$this->data['base'] = $this->url->is_ssl() ? SITE_SSL : SITE_URL;

		$this->language->set('language', $this->language->getInfo('code'));

		$this->data['orders'] = array();

		$orders = array();

		if (isset($_POST['selected'])) {
			$orders = $_POST['selected'];
		} elseif (isset($_GET['order_id'])) {
			$orders[] = $_GET['order_id'];
		}

		foreach ($orders as $order_id) {
			$order_info = $this->Model_Sale_Order->getOrder($order_id);

			if ($order_info) {
				$store_info = $this->Model_Setting_Setting->getSetting('config', $order_info['store_id']);
				
				if ($store_info) {
					$store_address = $store_info['config_address'];
					$store_email = $store_info['config_email'];
					$store_telephone = $store_info['config_telephone'];
					$store_fax = $store_info['config_fax'];
				} else {
					$store_address = $this->config->get('config_address');
					$store_email = $this->config->get('config_email');
					$store_telephone = $this->config->get('config_telephone');
					$store_fax = $this->config->get('config_fax');
				}
				
				if ($order_info['invoice_no']) {
					$invoice_no = $order_info['invoice_prefix'] . $order_info['invoice_no'];
				} else {
					$invoice_no = '';
				}
				
				if ($order_info['shipping_address_format']) {
					$format = $order_info['shipping_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}

				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);

				$replace = array(
					'firstname' => $order_info['shipping_firstname'],
					'lastname'  => $order_info['shipping_lastname'],
					'company'	=> $order_info['shipping_company'],
					'address_1' => $order_info['shipping_address_1'],
					'address_2' => $order_info['shipping_address_2'],
					'city'		=> $order_info['shipping_city'],
					'postcode'  => $order_info['shipping_postcode'],
					'zone'		=> $order_info['shipping_zone'],
					'zone_code' => $order_info['shipping_zone_code'],
					'country'	=> $order_info['shipping_country']
				);

				$shipping_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

				if ($order_info['payment_address_format']) {
					$format = $order_info['payment_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}

				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);

				$replace = array(
					'firstname' => $order_info['payment_firstname'],
					'lastname'  => $order_info['payment_lastname'],
					'company'	=> $order_info['payment_company'],
					'address_1' => $order_info['payment_address_1'],
					'address_2' => $order_info['payment_address_2'],
					'city'		=> $order_info['payment_city'],
					'postcode'  => $order_info['payment_postcode'],
					'zone'		=> $order_info['payment_zone'],
					'zone_code' => $order_info['payment_zone_code'],
					'country'	=> $order_info['payment_country']
				);

				$payment_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

				$product_data = array();

				$products = $this->Model_Sale_Order->getOrderProducts($order_id);

				foreach ($products as $product) {
					$option_data = array();

					$options = $this->Model_Sale_Order->getOrderOptions($order_id, $product['order_product_id']);

					foreach ($options as $option) {
						if ($option['type'] != 'file') {
							$value = $option['value'];
						} else {
							$value = substr($option['value'], 0, strrpos($option['value'], '.'));
						}
						
						$option_data[] = array(
							'name'  => $option['name'],
							'value' => $value
						);
					}

					$product_data[] = array(
						'name'	=> $product['name'],
						'model'	=> $product['model'],
						'option'	=> $option_data,
						'quantity' => $product['quantity'],
						'price'	=> $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']),
						'total'	=> $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value'])
					);
				}
				
				$voucher_data = array();
				
				$vouchers = $this->Model_Sale_Order->getOrderVouchers($order_id);

				foreach ($vouchers as $voucher) {
					$voucher_data[] = array(
						'description' => $voucher['description'],
						'amount'		=> $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
					);
				}
					
				$total_data = $this->Model_Sale_Order->getOrderTotals($order_id);

				$this->data['orders'][] = array(
					'order_id'			=> $order_id,
					'invoice_no'		=> $invoice_no,
					'date_added'		=> date($this->language->getInfo('date_format_short'), strtotime($order_info['date_added'])),
					'store_name'		=> $order_info['store_name'],
					'store_url'		=> rtrim($order_info['store_url'], '/'),
					'store_address'	=> nl2br($store_address),
					'store_email'		=> $store_email,
					'store_telephone'  => $store_telephone,
					'store_fax'		=> $store_fax,
					'email'				=> $order_info['email'],
					'telephone'		=> $order_info['telephone'],
					'shipping_address' => $shipping_address,
					'payment_address'  => $payment_address,
					'payment_method'	=> $order_info['payment_method'],
					'shipping_method'  => $order_info['shipping_method'],
					'product'			=> $product_data,
					'voucher'			=> $voucher_data,
					'total'				=> $total_data,
					'comment'			=> nl2br($order_info['comment'])
				);
			}
		}


		$this->response->setOutput($this->render());
	}
}