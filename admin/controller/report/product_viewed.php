<?php
class Admin_Controller_Report_ProductViewed extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Products Viewed Report"));

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$url = $this->get_url();

		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Products Viewed Report"), site_url('report/product_viewed'));

		$data = array(
			'start' => ($page - 1) * option('config_admin_limit'),
			'limit' => option('config_admin_limit')
		);

		$product_view_list = $this->Model_Report_Product->getProductViews();

		$product_views = array();
		foreach ($product_view_list as $pv) {
			if (isset($product_views[$pv['product_id']])) {
				$id     = & $product_views[$pv['product_id']];
				$unique = false;
				if (($pv['user_id'] == 0 || !in_array($pv['user_id'], $id['users'])) && !in_array($pv['session_id'], $id['sessions'])) {
					if ($pv['user_id'] != 0) {
						$id['users'][] = $pv['user_id'];
					}
					$id['sessions'][] = $pv['session_id'];
					$id['user_total'] += 1;
					$unique = true;
				}
				if (!in_array($pv['ip_address'], $id['ip_addr'])) {
					$id['ip_addr'][] = $pv['ip_address'];
					$id['ip_total'] += 1;
					if ($unique) {
						$id['ip_user_total'] += 1;
					}
				}
			} else {
				$product_views[$pv['product_id']] = array(
					'user_total'    => 1,
					'users'         => array($pv['user_id']),
					'sessions'      => array($pv['session_id']),
					'ip_total'      => 1,
					'ip_addr'       => array($pv['ip_address']),
					'ip_user_total' => 1
				);
			}
		}

		$product_viewed_total = $this->Model_Report_Product->getTotalProductsViewed($data);

		$product_views_total = $this->Model_Report_Product->getTotalProductViews();

		$data['products'] = array();

		$results = $this->Model_Report_Product->getProductsViewed($data);

		foreach ($results as $result) {
			if ($result['views']) {
				$percent = round($result['views'] / $product_views_total * 100, 2);
			} else {
				$percent = 0;
			}

			$data['products'][] = array(
				'name'          => $result['name'],
				'model'         => $result['model'],
				'viewed'        => $result['views'],
				'ip_total'      => $product_views[$result['product_id']]['ip_total'],
				'user_total'    => $product_views[$result['product_id']]['user_total'],
				'ip_user_total' => $product_views[$result['product_id']]['ip_user_total'],
				'percent'       => $percent . '%'
			);
		}

		$url = $this->get_url();

		$data['reset'] = site_url('report/product_viewed/reset', $url);

		$this->pagination->init();
		$this->pagination->total  = $product_viewed_total;
		$data['pagination'] = $this->pagination->render();

		$this->response->setOutput($this->render('report/product_viewed', $data));
	}

	public function reset()
	{
		$this->Model_Report_Product->reset();

		$this->message->add('success', _l("Success: You have reset the product viewed report!"));

		redirect('report/product_viewed');
	}

	private function get_url($filters = null)
	{
		$url     = '';
		$filters = $filters ? $filters : array('page');
		foreach ($filters as $f) {
			if (isset($_GET[$f])) {
				$url .= "&$f=" . $_GET[$f];
			}
		}
		return $url;
	}
}
