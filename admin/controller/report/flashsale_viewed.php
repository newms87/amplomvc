<?php
class Admin_Controller_Report_FlashsaleViewed extends Controller
{
	public function index()
	{
		$this->template->load('report/flashsale_viewed');

		$this->language->load('report/flashsale_viewed');

		$this->document->setTitle(_l("Flashsales Viewed Report"));

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$url = $this->get_url();

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Flashsales Viewed Report"), $this->url->link('report/flashsale_viewed'));

		$data = array(
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		$flashsale_view_list = $this->Model_Report_Flashsale->getFlashsaleViews();

		$flashsale_views = array();
		foreach ($flashsale_view_list as $fv) {
			if (isset($flashsale_views[$fv['flashsale_id']])) {
				$id     = & $flashsale_views[$fv['flashsale_id']];
				$unique = false;
				if (($fv['user_id'] == 0 || !in_array($fv['user_id'], $id['users'])) && !in_array($fv['session_id'], $id['sessions'])) {
					if ($fv['user_id'] != 0) {
						$id['users'][] = $fv['user_id'];
					}
					$id['sessions'][] = $fv['session_id'];
					$id['user_total'] += 1;
					$unique = true;
				}
				if (!in_array($fv['ip_address'], $id['ip_addr'])) {
					$id['ip_addr'][] = $fv['ip_address'];
					$id['ip_total'] += 1;
					if ($unique) {
						$id['ip_user_total'] += 1;
					}
				}
			} else {
				$flashsale_views[$fv['flashsale_id']] = array(
					'user_total'    => 1,
					'users'         => array($fv['user_id']),
					'sessions'      => array($fv['session_id']),
					'ip_total'      => 1,
					'ip_addr'       => array($fv['ip_address']),
					'ip_user_total' => 1
				);
			}
		}

		$flashsale_viewed_total = $this->Model_Report_Flashsale->getTotalFlashsalesViewed($data);

		$flashsale_views_total = $this->Model_Report_Flashsale->getTotalFlashsaleViews();

		$this->data['flashsales'] = array();

		$results = $this->Model_Report_Flashsale->getFlashsalesViewed($data);

		foreach ($results as $result) {
			if ($result['views']) {
				$percent = round($result['views'] / $flashsale_views_total * 100, 2);
			} else {
				$percent = 0;
			}

			$this->data['flashsales'][] = array(
				'name'          => $result['name'],
				'date_start'    => $result['date_start'],
				'date_end'      => $result['date_end'],
				'viewed'        => $result['views'],
				'ip_total'      => $flashsale_views[$result['flashsale_id']]['ip_total'],
				'user_total'    => $flashsale_views[$result['flashsale_id']]['user_total'],
				'ip_user_total' => $flashsale_views[$result['flashsale_id']]['ip_user_total'],
				'percent'       => $percent . '%'
			);
		}

		$url = $this->get_url();

		$this->data['reset'] = $this->url->link('report/flashsale_viewed/reset', $url);

		$this->pagination->init();
		$this->pagination->total  = $flashsale_viewed_total;
		$this->data['pagination'] = $this->pagination->render();

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	public function reset()
	{
		$this->language->load('report/flashsale_viewed');

		$this->Model_Report_Flashsale->reset();

		$this->message->add('success', _l("Success: You have reset the flashsales viewed report!"));

		$this->url->redirect('report/flashsale_viewed');
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
