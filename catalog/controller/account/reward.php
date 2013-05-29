<?php
class ControllerAccountReward extends Controller 
{
	public function index()
	{
		$this->template->load('account/reward');

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/reward');
			
			$this->url->redirect($this->url->link('account/login'));
		}
		
		$this->language->load('account/reward');

		$this->document->setTitle($this->_('heading_title'));

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
			$this->breadcrumb->add($this->_('text_reward'), $this->url->link('account/reward'));

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
		
		$reward_total = $this->model_account_reward->getTotalRewards($data);
	
		$results = $this->model_account_reward->getRewards($data);
 		
		foreach ($results as $result) {
			$this->data['rewards'][] = array(
				'order_id'	=> $result['order_id'],
				'points'		=> $result['points'],
				'description' => $result['description'],
				'date_added'  => $this->tool->format_datetime($result['date_added'], $this->language->getInfo('date_format_short')),
				'href'		=> $this->url->link('account/order/info', 'order_id=' . $result['order_id'])
			);
		}

		$this->pagination->init();
		$this->pagination->total = $reward_total;
		$this->data['pagination'] = $this->pagination->render();
		
		$this->data['total'] = (int)$this->customer->getRewardPoints();
		
		$this->data['continue'] = $this->url->link('account/account');

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);
						
		$this->response->setOutput($this->render());
	}
}