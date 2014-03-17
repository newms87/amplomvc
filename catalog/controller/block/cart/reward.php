<?php
class Catalog_Controller_Block_Cart_Reward
{
	public function index($settings = null)
	{
		$this->view->load('block/cart/reward');


		if (isset($_POST['reward']) && $this->validateReward()) {
			$this->session->set('reward', $_POST['reward']);

			$this->message->add('success', _l("Success: Your reward points discount has been applied!"));
		}

		$reward_info = array();

		if ($this->request->isPost()) {
			$reward_info = $_POST;
		} else {
			$reward_info['reward'] = $this->session->get('reward');
		}

		$defaults = array(
			'reward' => '',
		);

		$this->data += $reward_info + $defaults;

		$this->data['reward_points'] = $this->customer->getRewardPoints();
		$this->data['total_points']  = $this->cart->getTotalPoints();

		$this->response->setOutput($this->render());
	}

	private function validateReward()
	{
		$points = $this->customer->getRewardPoints();

		$points_total = 0;

		foreach ($this->cart->getProducts() as $product) {
			if ($product['points']) {
				$points_total += $product['points'];
			}
		}

		if (empty($_POST['reward'])) {
			$this->error['warning'] = _l("Warning: Please enter the amount of reward points to use!");
		}

		if ($_POST['reward'] > $points) {
			$this->error['warning'] = sprintf(_l("Warning: You don't have %s reward points!"), $_POST['reward']);
		}

		if ($_POST['reward'] > $points_total) {
			$this->error['warning'] = sprintf(_l("Warning: The maximum number of points that can be applied is %s!"), $points_total);
		}

		return $this->error ? false : true;
	}
}
