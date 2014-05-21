<?php

class App_Controller_Checkout extends Controller
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

		$this->request->setRedirect(site_url('checkout'));

		//Page Head
		$this->document->setTitle(_l("Checkout"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Shopping Cart"), site_url('cart'));
		$this->breadcrumb->add(_l("Checkout"), site_url('checkout'));

		//Statuses
		$data['is_logged']    = $this->customer->isLogged();
		$data['is_guest']     = $this->session->get('guest_checkout');
		$data['has_shipping'] = $this->cart->hasShipping();

		//Shipping Address
		$data['shipping_addresses'] = $this->customer->getShippingAddresses();

		if ($this->cart->validateShippingAddress()) {
			$data['shipping_address_id'] = $this->cart->getShippingAddressId();
		} else {
			//Set the customer's default shipping address
			$address_id = $this->customer->getDefaultShippingAddressId();

			if ($this->cart->setShippingAddress($address_id)) {
				$data['shipping_address_id'] = $address_id;
			}
		}

		//Payment Address
		$data['payment_addresses'] = $this->customer->getPaymentAddresses();

		if ($this->cart->validatePaymentAddress()) {
			$data['payment_address_id'] = $this->cart->getPaymentAddressId();
		} else {
			//Set the customer's default payment address
			$address_id = $this->customer->getDefaultPaymentAddressId();

			if ($this->cart->setPaymentAddress($address_id)) {
				$data['payment_address_id'] = $address_id;
			}
		}

		//Methods
		$data['shipping_key'] = $this->cart->getShippingKey();
		$data['payment_key']  = $this->cart->getPaymentKey();

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
			redirect('checkout');
		}
	}

	public function cancel_guest_checkout()
	{
		$this->session->set('guest_checkout', false);

		if (!$this->request->isAjax()) {
			redirect('checkout');
		}
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
		$data['shipping_code']    = $this->cart->getShippingCode();
		$data['shipping_key']     = $this->cart->getShippingKey();

		//Payment Methods
		$data['payment_methods'] = $this->cart->getPaymentMethods();
		$data['payment_code']    = $this->cart->getPaymentCode();
		$data['payment_key']     = $this->cart->getPaymentKey();

		$output = $this->render('checkout/methods', $data, true);

		if ($this->request->isAjax()) {
			$this->response->setOutput($output);
		} else {
			return $output;
		}
	}

	public function add_order()
	{
		$payment_address_id  = !empty($_POST['payment_address_id']) ? $_POST['payment_address_id'] : false;
		$shipping_address_id = !empty($_POST['shipping_address_id']) ? $_POST['shipping_address_id'] : false;
		$payment_code        = !empty($_POST['payment_code']) ? $_POST['payment_code'] : false;
		$payment_key         = !empty($_POST['payment_key']) ? $_POST['payment_key'] : false;
		$shipping_code       = !empty($_POST['shipping_code']) ? $_POST['shipping_code'] : false;
		$shipping_key        = !empty($_POST['shipping_key']) ? $_POST['shipping_key'] : false;

		if ($payment_code && $payment_key === 'new') {
			$payment_key = $this->System_Extension_Payment->get($payment_code)->addCard($_POST);
		}

		$this->cart->setShippingAddress($shipping_address_id);
		$this->cart->setPaymentAddress($payment_address_id);
		$this->cart->setPaymentMethod($payment_code, $payment_key);
		$this->cart->setShippingMethod($shipping_code, $shipping_key);

		$this->message->add('notify', "key = " . $payment_key . ' / ' . $this->cart->getPaymentKey());

		if ($this->cart->hasError()) {
			$this->message->add('error', $this->cart->getError());
		}

		//Create Order
		if (!$this->order->add()) {
			$this->message->add('error', $this->order->getError());
		}

		if ($this->message->has('error')) {
			if (!$this->request->isAjax()) {
				redirect('checkout/methods');
			}
		} else {
			$this->message->add('success', _l("Please confirm your order."));
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} else {
			redirect('checkout/confirmation');
		}
	}

	public function confirmation()
	{
		//Shipping & Payment
		$data = array(
			'shipping_address_id' => $this->cart->getShippingAddressId(),
			'shipping_code'       => $this->cart->getShippingCode(),
			'shipping_key'        => $this->cart->getShippingKey(),

			'payment_address_id'  => $this->cart->getPaymentAddressId(),
			'payment_code'        => $this->cart->getPaymentCode(),
			'payment_key'         => $this->cart->getPaymentKey(),
		);

		$data['has_confirmation'] = is_callable($this->cart->getPaymentMethod(), 'confirmation');

		//Action
		$data['action'] = site_url('checkout/confirm');

		//Render
		$this->response->setOutput($this->render('checkout/confirmation', $data));
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
					redirect('checkout');
				}
			}
		}

		$payment_ext = $this->cart->getPaymentMethod();

		if (!$payment_ext->confirm($this->order->getId())) {
			$this->message->add('error', $payment_ext->getError());

			if (!$this->request->isAjax()) {
				redirect('checkout');
			}
		}

		if (!$this->message->has('error')) {
			$this->message->add('success', _l("Your order has been processed successfully!"));
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} else {
			redirect('checkout/success');
		}
	}

	public function success()
	{
		//Page Head
		$this->document->setTitle(_l("Your Order Has Been Processed!"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Basket"), site_url('cart'));
		$this->breadcrumb->add(_l("Checkout"), site_url('checkout'));
		$this->breadcrumb->add(_l("Success"), site_url('checkout/success'));

		//Clear Cart
		$this->cart->clear();

		//Template Data
		$data['page_title'] = _l("Success");

		if ($this->customer->isLogged()) {
			$data['message'] = _l("<p>Your order has been successfully processed!</p><p>You can view your order history by going to the <a href=\"%s\">my account</a> page and by clicking on <a href=\"%s\">history</a>.</p><p>If you have any questions or concerns please feel free to <a href=\"%s\">contact us</a>.</p><p>Thanks for shopping with %s!</p>", site_url('account'), site_url('account/order'), site_url('page/contact'), option('config_name'));
		} else {
			$data['message'] = _l("<p>Your order has been successfully processed!</p><p>If you have any questions or concerns please feel free to <a href=\"%s\">contact us</a>.</p><p>Thanks for shopping with %s!</p>", site_url('page/contact'), option('config_name'));
		}

		//Action Buttons
		$data['continue'] = site_url('common/home');

		//Render
		$this->response->setOutput($this->render('common/success', $data));
	}
}
