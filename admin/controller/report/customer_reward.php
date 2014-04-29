<?php
class Admin_Controller_Report_CustomerReward extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Customer Reward Points Report"));

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

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Customer Reward Points Report"), site_url('report/customer_reward', $url));

		$data['customers'] = array();

		$data = array(
			'filter_date_start' => $filter_date_start,
			'filter_date_end'   => $filter_date_end,
			'start'             => ($page - 1) * option('config_admin_limit'),
			'limit'             => option('config_admin_limit')
		);

		$customer_total = $this->Model_Report_Customer->getTotalRewardPoints($data);

		$results = $this->Model_Report_Customer->getRewardPoints($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("Edit"),
				'href' => site_url('sale/customer/update', 'customer_id=' . $result['customer_id'] . $url)
			);

			$data['customers'][] = array(
				'customer'       => $result['customer'],
				'email'          => $result['email'],
				'customer_group' => $result['customer_group'],
				'status'         => ($result['status'] ? _l("Enabled") : _l("Disabled")),
				'points'         => $result['points'],
				'orders'         => $result['orders'],
				'total'          => $this->currency->format($result['total'], option('config_currency')),
				'action'         => $action
			);
		}

		$url = '';

		if (isset($_GET['filter_date_start'])) {
			$url .= '&filter_date_start=' . $_GET['filter_date_start'];
		}

		if (isset($_GET['filter_date_end'])) {
			$url .= '&filter_date_end=' . $_GET['filter_date_end'];
		}

		$this->pagination->init();
		$this->pagination->total  = $customer_total;
		$data['pagination'] = $this->pagination->render();

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end']   = $filter_date_end;

		$this->response->setOutput($this->render('report/customer_reward', $data));
	}
}
