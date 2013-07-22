<?php
class Admin_Controller_Report_CustomerOrder extends Controller
{
	public function index()
	{
		$this->template->load('report/customer_order');

		$this->language->load('report/customer_order');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_GET['filter_date_start'])) {
			$filter_date_start = $_GET['filter_date_start'];
		} else {
			$filter_date_start = '';
		}

		if (isset($_GET['filter_date_end'])) {
			$filter_date_end = $_GET['filter_date_end'];
		} else {
			$filter_date_end = '';
		}
		
		if (isset($_GET['filter_order_status_id'])) {
			$filter_order_status_id = $_GET['filter_order_status_id'];
		} else {
			$filter_order_status_id = 0;
		}
				
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$url = '';
		
		if (isset($_GET['filter_date_start'])) {
			$url .= '&filter_date_start=' . $_GET['filter_date_start'];
		}
		
		if (isset($_GET['filter_date_end'])) {
			$url .= '&filter_date_end=' . $_GET['filter_date_end'];
		}

		if (isset($_GET['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $_GET['filter_order_status_id'];
		}
		
		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}
						
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('report/customer_order', $url));

		$this->data['customers'] = array();
		
		$data = array(
			'filter_date_start'		=> $filter_date_start,
			'filter_date_end'		=> $filter_date_end,
			'filter_order_status_id' => $filter_order_status_id,
			'start'						=> ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'						=> $this->config->get('config_admin_limit')
		);
				
		$customer_total = $this->Model_Report_Customer->getTotalOrders($data);
		
		$results = $this->Model_Report_Customer->getOrders($data);
		
		foreach ($results as $result) {
			$action = array();
		
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('sale/customer/update', 'customer_id=' . $result['customer_id'] . $url)
			);
						
			$this->data['customers'][] = array(
				'customer'		=> $result['customer'],
				'email'			=> $result['email'],
				'customer_group' => $result['customer_group'],
				'status'			=> ($result['status'] ? $this->_('text_enabled') : $this->_('text_disabled')),
				'orders'			=> $result['orders'],
				'products'		=> $result['products'],
				'total'			=> $this->currency->format($result['total'], $this->config->get('config_currency')),
				'action'			=> $action
			);
		}
		
		$this->data['order_statuses'] = $this->order->getOrderStatuses();
			
		$url = '';
						
		if (isset($_GET['filter_date_start'])) {
			$url .= '&filter_date_start=' . $_GET['filter_date_start'];
		}
		
		if (isset($_GET['filter_date_end'])) {
			$url .= '&filter_date_end=' . $_GET['filter_date_end'];
		}

		if (isset($_GET['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $_GET['filter_order_status_id'];
		}
				
		$this->pagination->init();
		$this->pagination->total = $customer_total;
		$this->data['pagination'] = $this->pagination->render();
		
		$this->data['filter_date_start'] = $filter_date_start;
		$this->data['filter_date_end'] = $filter_date_end;
		$this->data['filter_order_status_id'] = $filter_order_status_id;
				
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
}