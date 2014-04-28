<?php
class Admin_Controller_Report_SaleCoupon extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Coupon Report"));

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
		$this->breadcrumb->add(_l("Coupon Report"), site_url('report/sale_coupon', $url));

		$data['coupons'] = array();

		$data = array(
			'filter_date_start' => $filter_date_start,
			'filter_date_end'   => $filter_date_end,
			'start'             => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'             => $this->config->get('config_admin_limit')
		);

		$coupon_total = $this->Model_Report_Coupon->getTotalCoupons($data);

		$results = $this->Model_Report_Coupon->getCoupons($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("Edit"),
				'href' => site_url('sale/coupon/update', 'coupon_id=' . $result['coupon_id'] . $url)
			);

			$data['coupons'][] = array(
				'name'   => $result['name'],
				'code'   => $result['code'],
				'orders' => $result['orders'],
				'total'  => $this->currency->format($result['total'], $this->config->get('config_currency')),
				'action' => $action
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
		$this->pagination->total  = $coupon_total;
		$data['pagination'] = $this->pagination->render();

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end']   = $filter_date_end;

		$this->response->setOutput($this->render('report/sale_coupon', $data));
	}
}
