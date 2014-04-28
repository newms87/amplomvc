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

		$data['logged']         = $this->customer->isLogged();
		$data['guest_checkout'] = $this->session->get('guest_checkout');

		if (!$this->customer->IsLogged() && !$data['guest_checkout']) {
			$data['login_form'] = $this->block->render('customer/login', null, array('template' => 'block/customer/login'));
		} elseif ($data['guest_checkout']) {
			$data['cancel_guest_checkout'] = site_url('checkout/checkout/cancel_guest_checkout');
		}

		$data['shipping_required'] = $this->cart->hasShipping();

		//Render
		$this->response->setOutput($this->render('checkout/checkout', $data));
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
