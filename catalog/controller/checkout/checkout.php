<?php
class Catalog_Controller_Checkout_Checkout extends Controller
{
	public function index()
	{
		//TODO: Need to implement a more dynamic cart system to incorporate other cart types (eg: subscriptions, user_custom_types, etc..)
		if (!$this->cart->canCheckout()) {
			$this->message->add("warning", _l("You do not have any products in your cart. Please continue with your purchase via a different method provided from the cart."));
			redirect('cart/cart');
		}

		if (!$this->cart->validate()) {
			redirect('cart/cart');
		}

		$this->request->setRedirect(site_url('checkout/checkout'));

		//Page Head
		$this->document->setTitle(_l("Checkout"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Shopping Cart"), site_url('cart/cart'));
		$this->breadcrumb->add(_l("Checkout"), site_url('checkout/checkout'));

		//Statuses
		$data['is_logged']    = $this->customer->isLogged();
		$data['is_guest']     = $this->session->get('guest_checkout');
		$data['has_shipping'] = $this->cart->hasShipping();

		//Shipping Address
		if ($this->cart->validateShippingAddress()) {
			$data['shipping_address_id'] = $this->cart->getShippingAddressId();
		} else {
			$data['shipping_address_id'] = $this->customer->getDefaultShippingAddressId();
		}

		//Payment Address
		if ($this->cart->validatePaymentAddress()) {
			$data['payment_address_id'] = $this->cart->getPaymentAddressId();
		} else {
			$data['payment_address_id'] = $this->customer->getDefaultPaymentAddressId();
		}

		//Render
		$this->response->setOutput($this->render('checkout/checkout', $data));
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
