<?php
class Catalog_Controller_Checkout_Checkout extends Controller
{
	public function index()
	{
		//TODO: Need to implement a more dynamic cart system to incorporate other cart types (eg: subscriptions, user_custom_types, etc..)
		if (!$this->cart->canCheckout()) {
			$this->message->add("warning", _l("You do not have any products in your cart. Please continue with your purchase via a different method provided from the cart."));
			redirect('cart');
		}

		if (!$this->cart->validate()) {
			redirect('cart');
		}

		$this->request->setRedirect(site_url('checkout/checkout'));

		//Page Head
		$this->document->setTitle(_l("Checkout"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Shopping Cart"), site_url('cart'));
		$this->breadcrumb->add(_l("Checkout"), site_url('checkout/checkout'));

		//Statuses
		$data['is_logged']    = $this->customer->isLogged();
		$data['is_guest']     = $this->session->get('guest_checkout');
		$data['has_shipping'] = $this->cart->hasShipping();

		//Shipping Address
		$data['shipping_addresses'] = $this->customer->getShippingAddresses();

		if ($this->cart->validateShippingAddress()) {
			$data['shipping_address_id'] = $this->cart->getShippingAddressId();
		} else {
			$data['shipping_address_id'] = $this->customer->getDefaultShippingAddressId();
		}

		//Payment Address
		$data['payment_addresses'] = $this->customer->getPaymentAddresses();

		if ($this->cart->validatePaymentAddress()) {
			$data['payment_address_id'] = $this->cart->getPaymentAddressId();
		} else {
			$data['payment_address_id'] = $this->customer->getDefaultPaymentAddressId();
		}

		//Methods
		$data['shipping_key'] = $this->cart->getShippingKey();
		$data['payment_key'] = $this->cart->getPaymentKey();

		//Render
		$this->response->setOutput($this->render('checkout/checkout', $data));
	}

	public function methods($data = array())
	{
		if ($this->request->isPost()) {
			if (!empty($_POST['shipping_address_id'])) {
				$this->cart->setShippingAddress($_POST['shipping_address_id']);
			}

			if (!empty($_POST['payment_address_id'])) {
				$this->cart->setPaymentAddress($_POST['payment_address_id']);
			}
		}

		//Shipping Methods
		$data['shipping_methods'] = $this->cart->getShippingMethods();
		$data['shipping_key'] = $this->cart->getShippingKey();

		//Payment Methods
		$data['payment_methods'] = $this->cart->getPaymentMethods();
		$data['payment_key'] = $this->cart->getPaymentKey();

		$output = $this->render('checkout/methods', $data, true);

		if ($this->request->isAjax()) {
			$this->response->setOutput($output);
		} else {
			return $output;
		}
	}

	public function confirm()
	{
		if (!$this->cart->validate()) {
			$this->message->add('error', $this->cart->getError());

			if (!$this->request->isAjax()) {
				redirect('cart');
			}
		} else {
			$this->cart->validateShippingMethod();
			$this->cart->validatePaymentMethod();

			if ($this->cart->hasError()) {
				$this->message->add('error', $this->cart->getError());

				if (!$this->request->isAjax()) {
					redirect('checkout/checkout');
				}
			}
		}

		if ($this->message->has('error') && $this->request->isAjax()) {
			return $this->response->setOutput($this->message->toJSON());
		}

		if (option('coupon_status')) {
			$data['block_coupon'] = $this->block->render('cart/coupon', null, array('ajax' => true));
		}
		$data['block_totals'] = $this->block->render('cart/total');
		$data['payment'] = $this->cart->getPaymentMethod()->renderTemplate();

		$this->response->setOutput($this->render('block/checkout/confirm', $data));
	}

	public function guest()
	{
		$this->render('checkout/guest');
	}

	public function guest_checkout()
	{
		$this->session->set('guest_checkout', true);

		if (!$this->request->isAjax()) {
			redirect('checkout/checkout');
		}
	}

	public function cancel_guest_checkout()
	{
		$this->session->set('guest_checkout', false);

		if (!$this->request->isAjax()) {
			redirect('checkout/checkout');
		}
	}
}
