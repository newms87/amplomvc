<?php
class App_Controller_Admin_Common_Home extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Dashboard"));

		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));

		$data = array();

		$data['total_sale']      = $this->currency->format($this->System_Model_Order->getGrossSales(), option('config_currency'));
		$data['total_sale_year'] = $this->currency->format($this->System_Model_Order->getGrossSales(array('years' => array(date('Y')))), option('config_currency'));
		$data['total_order']     = $this->System_Model_Order->getTotalOrders();

		$data['total_customer']          = $this->Model_Sale_Customer->getTotalCustomers();
		$data['total_customer_approval'] = $this->Model_Sale_Customer->getTotalCustomersAwaitingApproval();

		$data['total_review']          = $this->Model_Catalog_Review->getTotalReviews();
		$data['total_review_approval'] = $this->Model_Catalog_Review->getTotalReviewsAwaitingApproval();

		//Last 10 orders
		$data += array(
			'sort'              => 'o.date_added',
			'order'             => 'DESC',
			'start'             => 0,
			'limit'             => 10,
			'!order_status_ids' => array(0),
		);

		$orders = $this->System_Model_Order->getOrders($data);

		foreach ($orders as &$order) {
			$order['action'] = array(
				'view' => array(
					'text' => _l("View"),
					'href' => site_url('admin/sale/order/info', 'order_id=' . $order['order_id'])
				),
			);

			$order['order_status'] = $this->order->getOrderStatus($order['order_status_id']);

			$customer = $this->customer->getCustomer($order['customer_id']);

			if (!$customer) {
				$customer['firstname'] = 'Guest';
				$customer['lastname']  = '';
			}

			$order['customer'] = $customer;

			$order['date_added'] = $this->date->format($order['date_added'], 'short');
			$order['total']      = $this->currency->format($order['total'], $order['currency_code'], $order['currency_value']);
		}
		unset($order);

		$data['orders'] = $orders;

		if (option('config_currency_auto')) {
			$this->Model_Localisation_Currency->updateCurrencies();
		}

		$this->response->setOutput($this->render('common/home', $data));
	}

	public function chart()
	{
		$data = array();

		$data['order']    = array();
		$data['customer'] = array();
		$data['xaxis']    = array();

		$data['order']['label']    = _l("Total Orders");
		$data['customer']['label'] = _l("Total Customers");

		if (isset($_GET['range'])) {
			$range = $_GET['range'];
		} else {
			$range = 'month';
		}

		switch ($range) {
			case 'day':
				$now = $this->date->now();

				for ($i = 0; $i < 24; $i++) {
					$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND (DATE(date_added) = DATE('$now') AND HOUR(date_added) = '" . (int)$i . "') GROUP BY HOUR(date_added) ORDER BY date_added ASC");

					if ($query->num_rows) {
						$data['order']['data'][] = array(
							$i,
							(int)$query->row['total']
						);
					} else {
						$data['order']['data'][] = array(
							$i,
							0
						);
					}

					$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE DATE(date_added) = DATE('$now') AND HOUR(date_added) = '" . (int)$i . "' GROUP BY HOUR(date_added) ORDER BY date_added ASC");

					if ($query->num_rows) {
						$data['customer']['data'][] = array(
							$i,
							(int)$query->row['total']
						);
					} else {
						$data['customer']['data'][] = array(
							$i,
							0
						);
					}

					$data['xaxis'][] = array(
						$i,
						date('H', mktime($i, 0, 0, date('n'), date('j'), date('Y')))
					);
				}
				break;
			case 'week':
				$date_start = strtotime('-' . date('w') . ' days');

				for ($i = 0; $i < 7; $i++) {
					$date = date('Y-m-d', $date_start + ($i * 86400));

					$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND DATE(date_added) = '" . $this->db->escape($date) . "' GROUP BY DATE(date_added)");

					if ($query->num_rows) {
						$data['order']['data'][] = array(
							$i,
							(int)$query->row['total']
						);
					} else {
						$data['order']['data'][] = array(
							$i,
							0
						);
					}

					$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer` WHERE DATE(date_added) = '" . $this->db->escape($date) . "' GROUP BY DATE(date_added)");

					if ($query->num_rows) {
						$data['customer']['data'][] = array(
							$i,
							(int)$query->row['total']
						);
					} else {
						$data['customer']['data'][] = array(
							$i,
							0
						);
					}

					$data['xaxis'][] = array(
						$i,
						date('D', strtotime($date))
					);
				}

				break;
			default:
			case 'month':
				for ($i = 1; $i <= date('t'); $i++) {
					$date = date('Y') . '-' . date('m') . '-' . $i;

					$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND (DATE(date_added) = '" . $this->db->escape($date) . "') GROUP BY DAY(date_added)");

					if ($query->num_rows) {
						$data['order']['data'][] = array(
							$i,
							(int)$query->row['total']
						);
					} else {
						$data['order']['data'][] = array(
							$i,
							0
						);
					}

					$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE DATE(date_added) = '" . $this->db->escape($date) . "' GROUP BY DAY(date_added)");

					if ($query->num_rows) {
						$data['customer']['data'][] = array(
							$i,
							(int)$query->row['total']
						);
					} else {
						$data['customer']['data'][] = array(
							$i,
							0
						);
					}

					$data['xaxis'][] = array(
						$i,
						date('j', strtotime($date))
					);
				}
				break;
			case 'year':
				for ($i = 1; $i <= 12; $i++) {
					$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND YEAR(date_added) = '" . date('Y') . "' AND MONTH(date_added) = '" . $i . "' GROUP BY MONTH(date_added)");

					if ($query->num_rows) {
						$data['order']['data'][] = array(
							$i,
							(int)$query->row['total']
						);
					} else {
						$data['order']['data'][] = array(
							$i,
							0
						);
					}

					$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE YEAR(date_added) = '" . date('Y') . "' AND MONTH(date_added) = '" . $i . "' GROUP BY MONTH(date_added)");

					if ($query->num_rows) {
						$data['customer']['data'][] = array(
							$i,
							(int)$query->row['total']
						);
					} else {
						$data['customer']['data'][] = array(
							$i,
							0
						);
					}

					$data['xaxis'][] = array(
						$i,
						date('M', mktime(0, 0, 0, $i, 1, date('Y')))
					);
				}
				break;
		}

		$this->response->setOutput(json_encode($data));
	}
}
