<?php
class Catalog_Controller_Block_Cart_Reward
{
	
	public function index($settings = null)
	{
		$this->template->load('block/cart/reward');

			
		if (isset($_POST['reward']) && $this->validateReward()) {
			$this->session->data['reward'] = $_POST['reward'];
				
			$this->message->add('success', $this->_('text_reward'));
		}
		
		$defaults = array(
			'reward' => '',
		);
			
		if (isset($_POST[$key])) {
			$this->data[$key] = $_POST[$key];
		} elseif (isset($this->session->data[$key])) {
			$this->data[$key] = $this->session->data[$key];
		} else {
			$this->data[$key] = $default;
		}
		
		$points = $this->customer->getRewardPoints();
			
		$points_total = $this->cart->getTotalPoints();
		
		$this->_('text_use_reward', $points);
		$this->_('entry_reward', $points_total);


		
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
			$this->error['warning'] = $this->_('error_reward');
		}
	
		if ($_POST['reward'] > $points) {
			$this->error['warning'] = sprintf($this->_('error_points'), $_POST['reward']);
		}
		
		if ($_POST['reward'] > $points_total) {
			$this->error['warning'] = sprintf($this->_('error_maximum'), $points_total);
		}
		
		return $this->error ? false : true;
	}
}