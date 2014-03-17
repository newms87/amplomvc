<?php
class Catalog_Controller_Checkout_Success extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Your Order Has Been Processed!"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Basket"), $this->url->link('cart/cart'));
		$this->breadcrumb->add(_l("Checkout"), $this->url->link('checkout/checkout'));
		$this->breadcrumb->add(_l("Success"), $this->url->link('checkout/success'));

		//Clear Cart
		$this->cart->clear();

		//Template Data
		$this->data['page_title'] = _l("Success");

		if ($this->customer->isLogged()) {
			$this->data['message'] = _l("<p>Your order has been successfully processed!</p><p>You can view your order history by going to the <a href=\"%s\">my account</a> page and by clicking on <a href=\"%s\">history</a>.</p><p>If you have any questions or concerns please feel free to <a href=\"%s\">contact us</a>.</p><p>Thanks for shopping with %s!</p>", $this->url->link('account/account'), $this->url->link('account/order'), $this->url->link('information/contact'), $this->config->get('config_name'));
		} else {
			$this->data['message'] = _l("<p>Your order has been successfully processed!</p><p>If you have any questions or concerns please feel free to <a href=\"%s\">contact us</a>.</p><p>Thanks for shopping with %s!</p>", $this->url->link('information/contact'), $this->config->get('config_name'));
		}

		//Action Buttons
		$this->data['continue'] = $this->url->link('common/home');

		//Template and Language
		$this->view->load('common/success');

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
}
