<?php
class Catalog_Controller_Learneat_Box extends Controller
{
	public function add_to_cart()
	{
		$product_id = !empty($_GET['product_id']) ? $_GET['product_id'] : null;

		if (!$product_id) {
			redirect('common/home');
		}

		$options = !empty($_POST['options']) ? $_POST['options'] : array();

		$key = $this->subscription->addToCart($product_id, $options);

		if ($key) {
			$this->message->add('success', _l("Your subscription has been added to the cart!"));
		} else {
			$this->message->add('error', $this->cart->getError());
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} elseif ($key) {
			redirect('checkout/subscribe', 'key=' . $key);
		} else {
			redirect('product/box', 'product_id=' . $product_id);
		}
	}

	public function calculate_total()
	{
		if ($this->cart->validateProduct($_POST['product_id'], 1, $_POST['options'])) {

			$total = $this->subscription->getSubscriptionTotal($_POST['product_id'], $_POST['options']);

			$subscription = $this->subscription->getProductSubscription($_POST['product_id']);

			$this->response->setOutput($this->subscription->formatPrice($subscription, $total));
		} else {
			$this->response->setOutput(_l('Please choose your subscription options.'));
		}
	}
}
