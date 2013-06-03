<?php
class Admin_Controller_Common_Home extends Controller 
{
	public function index()
	{
		$this->template->load('common/home');
		$this->load->language('common/home');
		
		if ($this->user->isDesigner()) {
			$this->document->setTitle($this->_('heading_title_restricted'));;
			$this->language->set('heading_title', $this->_('heading_title_restricted'));
		}
		else {
			$this->document->setTitle($this->_('heading_title'));
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));

		$this->data['total_sale'] = $this->currency->format($this->Model_Sale_Order->getTotalSales(), $this->config->get('config_currency'));
		$this->data['total_sale_year'] = $this->currency->format($this->Model_Sale_Order->getTotalSalesByYear(date('Y')), $this->config->get('config_currency'));
		$this->data['total_order'] = $this->Model_Sale_Order->getTotalOrders();
		
		$this->data['total_customer'] = $this->Model_Sale_Customer->getTotalCustomers();
		$this->data['total_customer_approval'] = $this->Model_Sale_Customer->getTotalCustomersAwaitingApproval();
		
		$this->data['total_review'] = $this->Model_Catalog_Review->getTotalReviews();
		$this->data['total_review_approval'] = $this->Model_Catalog_Review->getTotalReviewsAwaitingApproval();
		
		$this->data['total_affiliate'] = $this->Model_Sale_Affiliate->getTotalAffiliates();
		$this->data['total_affiliate_approval'] = $this->Model_Sale_Affiliate->getTotalAffiliatesAwaitingApproval();
				
		$this->data['orders'] = array();
		
		$data = array(
			'sort'  => 'o.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => 10
		);
		
		$results = $this->Model_Sale_Order->getOrders($data);
		
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->_('text_view'),
				'href' => $this->url->link('sale/order/info', 'order_id=' . $result['order_id'])
			);
					
			$this->data['orders'][] = array(
				'order_id'	=> $result['order_id'],
				'customer'	=> $result['customer'],
				'status'	=> $result['status'],
				'date_added' => $this->tool->format_datetime($result['date_added'], $this->language->getInfo('date_format_short')),
				'total'		=> $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'action'	=> $action
			);
		}

		if ($this->config->get('config_currency_auto')) {
			$this->Model_Localisation_Currency->updateCurrencies();
		}
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
  	}
	
	public function chart()
	{
		$this->load->language('common/home');
		
		$data = array();
		
		$data['order'] = array();
		$data['customer'] = array();
		$data['xaxis'] = array();
		
		$data['order']['label'] = $this->_('text_order');
		$data['customer']['label'] = $this->_('text_customer');
		
		if (isset($_GET['range'])) {
			$range = $_GET['range'];
		} else {
			$range = 'month';
		}
		
		switch ($range) {
			case 'day':
				for ($i = 0; $i < 24; $i++) {
					$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND (DATE(date_added) = DATE(NOW()) AND HOUR(date_added) = '" . (int)$i . "') GROUP BY HOUR(date_added) ORDER BY date_added ASC");
					
					if ($query->num_rows) {
						$data['order']['data'][]  = array($i, (int)$query->row['total']);
					} else {
						$data['order']['data'][]  = array($i, 0);
					}
					
					$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE DATE(date_added) = DATE(NOW()) AND HOUR(date_added) = '" . (int)$i . "' GROUP BY HOUR(date_added) ORDER BY date_added ASC");
					
					if ($query->num_rows) {
						$data['customer']['data'][] = array($i, (int)$query->row['total']);
					} else {
						$data['customer']['data'][] = array($i, 0);
					}
			
					$data['xaxis'][] = array($i, date('H', mktime($i, 0, 0, date('n'), date('j'), date('Y'))));
				}
				break;
			case 'week':
				$date_start = strtotime('-' . date('w') . ' days');
				
				for ($i = 0; $i < 7; $i++) {
					$date = date('Y-m-d', $date_start + ($i * 86400));

					$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND DATE(date_added) = '" . $this->db->escape($date) . "' GROUP BY DATE(date_added)");
			
					if ($query->num_rows) {
						$data['order']['data'][] = array($i, (int)$query->row['total']);
					} else {
						$data['order']['data'][] = array($i, 0);
					}
				
					$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer` WHERE DATE(date_added) = '" . $this->db->escape($date) . "' GROUP BY DATE(date_added)");
			
					if ($query->num_rows) {
						$data['customer']['data'][] = array($i, (int)$query->row['total']);
					} else {
						$data['customer']['data'][] = array($i, 0);
					}
		
					$data['xaxis'][] = array($i, date('D', strtotime($date)));
				}
				
				break;
			default:
			case 'month':
				for ($i = 1; $i <= date('t'); $i++) {
					$date = date('Y') . '-' . date('m') . '-' . $i;
					
					$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND (DATE(date_added) = '" . $this->db->escape($date) . "') GROUP BY DAY(date_added)");
					
					if ($query->num_rows) {
						$data['order']['data'][] = array($i, (int)$query->row['total']);
					} else {
						$data['order']['data'][] = array($i, 0);
					}
				
					$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE DATE(date_added) = '" . $this->db->escape($date) . "' GROUP BY DAY(date_added)");
			
					if ($query->num_rows) {
						$data['customer']['data'][] = array($i, (int)$query->row['total']);
					} else {
						$data['customer']['data'][] = array($i, 0);
					}
					
					$data['xaxis'][] = array($i, date('j', strtotime($date)));
				}
				break;
			case 'year':
				for ($i = 1; $i <= 12; $i++) {
					$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND YEAR(date_added) = '" . date('Y') . "' AND MONTH(date_added) = '" . $i . "' GROUP BY MONTH(date_added)");
					
					if ($query->num_rows) {
						$data['order']['data'][] = array($i, (int)$query->row['total']);
					} else {
						$data['order']['data'][] = array($i, 0);
					}
					
					$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE YEAR(date_added) = '" . date('Y') . "' AND MONTH(date_added) = '" . $i . "' GROUP BY MONTH(date_added)");
					
					if ($query->num_rows) {
						$data['customer']['data'][] = array($i, (int)$query->row['total']);
					} else {
						$data['customer']['data'][] = array($i, 0);
					}
					
					$data['xaxis'][] = array($i, date('M', mktime(0, 0, 0, $i, 1, date('Y'))));
				}
				break;
		}
		
		$this->response->setOutput(json_encode($data));
	}
}
