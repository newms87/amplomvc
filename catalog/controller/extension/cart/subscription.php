<?php
class Catalog_Controller_Extension_Cart_Subscription extends Controller
{
	public function renderCart()
	{
		//If no subscriptions do nothing
		if ($this->subscription->cartEmpty()) {
			return;
		}

		if ($this->cart->isCheckout()) {
			$this->data['is_checkout'] = true;
			$this->data['url_cart']    = $this->url->link('cart/cart');
		} else {
			if ($this->request->isPost()) {
				if (!empty($_POST['subscription_remove'])) {
					$subscription = $this->subscription->getCartSubscription($_POST['subscription_remove']);

					$this->subscription->removeFromCart($_POST['subscription_remove']);

					$url_subscription = $this->url->link('product/product', 'product_id=' . $subscription['id']);
					$this->message->add('success', _l("<a href=\"%s\">%s</a> has been removed from your cart.", $url_subscription, $subscription['product']['name']));
				}
			}

			$subscriptions = $this->subscription->getCart();

			$image_width  = $this->config->get('config_image_cart_width');
			$image_height = $this->config->get('config_image_cart_height');

			foreach ($subscriptions as $key => &$subscription) {
				$this->subscription->fillSubscriptionDetails($subscription, $subscription['options']);

				if (!$subscription) {
					$url_subscription = $this->url->link('product/product', 'product_id=' . $subscription['id']);
					$this->message->add('warning', _l("There was a problem with your subscription for <a href=\"%s\">%s</a>. Please try again.", $url_subscription, $subscription['product']['name']));

					unset($subscriptions[$key]);
					$this->subscription->removeFromCart($key);

					continue;
				}

				$subscription['href'] = $this->url->link('product/product', 'product_id=' . $subscription['product']['product_id']);

				$subscription['thumb'] = $this->image->resize($subscription['product']['image'], $image_width, $image_height);

				$subscription['url_subscribe'] = $this->url->link('checkout/subscribe', 'key=' . $subscription['key']);
				$subscription['remove']        = $this->url->link('cart/cart/remove', 'key=' . $subscription['key']);
			}
			unset($subscription);

			//If we are now empty, reload the page, in case cart empty, or other cart updates necessary
			if (empty($subscriptions)) {
				if ($this->request->isAjax()) {
					$this->request->redirectBrowser($this->url->link('cart/cart'));
				}

				$this->url->redirect('cart/cart');
			}

			$this->data['subscriptions'] = $subscriptions;
		}

		//The Template
		$this->template->load('extension/cart/subscription');

		//Render
		$this->render();
	}
}
