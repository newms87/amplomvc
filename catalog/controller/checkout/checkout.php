<?php
class Catalog_Controller_Checkout_Checkout extends Controller
{
	public function index()
	{
		//TODO: Need to implement a more dynamic cart system to incorporate other cart types (eg: subscriptions, user_custom_types, etc..)
		if (!$this->cart->canCheckout()) {
			$this->message->add("warning", _l("You do not have any products in your cart. Please continue with your purchase via a different method provided from the cart."));
			$this->url->redirect('cart/cart');
		}

		if (!$this->cart->validate()) {
			$this->url->redirect('cart/cart');
		}

		$this->request->setRedirect($this->url->link('checkout/checkout'));

		//Page Head
		$this->document->setTitle(_l("Checkout"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Shopping Cart"), $this->url->link('cart/cart'));
		$this->breadcrumb->add(_l("Checkout"), $this->url->link('checkout/checkout'));

		$this->data['logged']         = $this->customer->isLogged();
		$this->data['guest_checkout'] = $this->session->get('guest_checkout');

		if (!$this->customer->IsLogged() && !$this->data['guest_checkout']) {
			$this->data['login_form'] = $this->block->render('account/login', array('template' => 'block/account/login'));
		} elseif ($this->data['guest_checkout']) {
			$this->data['cancel_guest_checkout'] = $this->url->link('checkout/checkout/cancel_guest_checkout');
		}

		$this->data['shipping_required'] = $this->cart->hasShipping();

		//The Template
		$this->view->load('checkout/checkout');

		//Dependencies
		$this->children = array(
			'area/left',
			'area/right',
			'area/top',
			'area/bottom',
			'common/footer',
			'common/header'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	public function guest_checkout()
	{
		$this->session->set('guest_checkout', true);

		if (!$this->request->isAjax()) {
			$this->url->redirect('checkout/checkout');
		}
	}

	public function cancel_guest_checkout()
	{
		$this->session->set('guest_checkout', false);

		if (!$this->request->isAjax()) {
			$this->url->redirect('checkout/checkout');
		}
	}
}
