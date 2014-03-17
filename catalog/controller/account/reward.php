<?php
class Catalog_Controller_Account_Reward extends Controller
{
	public function index()
	{
		$this->view->load('account/reward');

		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', $this->url->link('account/reward'));

			$this->url->redirect('account/login');
		}

		$this->document->setTitle(_l("Your Reward Points"));

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account/account'));
		$this->breadcrumb->add(_l("Reward Points"), $this->url->link('account/reward'));

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$this->data['rewards'] = array();

		$data = array(
			'sort'  => 'date_added',
			'order' => 'DESC',
			'start' => ($page - 1) * 10,
			'limit' => 10
		);

		$reward_total = $this->Model_Account_Reward->getTotalRewards($data);

		$results = $this->Model_Account_Reward->getRewards($data);

		foreach ($results as $result) {
			$this->data['rewards'][] = array(
				'order_id'    => $result['order_id'],
				'points'      => $result['points'],
				'description' => $result['description'],
				'date_added'  => $this->date->format($result['date_added'], 'short'),
				'href'        => $this->url->link('account/order/info', 'order_id=' . $result['order_id'])
			);
		}

		$this->pagination->init();
		$this->pagination->total  = $reward_total;
		$this->data['pagination'] = $this->pagination->render();

		$this->data['total'] = (int)$this->customer->getRewardPoints();

		$this->data['continue'] = $this->url->link('account/account');

		$this->children = array(
			'area/left',
			'area/right',
			'area/top',
			'area/bottom',
			'common/footer',
			'common/header'
		);

		$this->response->setOutput($this->render());
	}
}
