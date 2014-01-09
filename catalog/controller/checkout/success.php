<?php
class Catalog_Controller_Checkout_Success extends Controller
{
	public function index()
	{
		//Template and Language
		$this->template->load('common/success');
		$this->language->load('checkout/success');

		//Page Head
		$this->document->setTitle(_l("Your Order Has Been Processed!"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Basket"), $this->url->link('cart/cart'));
		$this->breadcrumb->add(_l("Checkout"), $this->url->link('checkout/checkout'));
		$this->breadcrumb->add(_l("Success"), $this->url->link('checkout/success'));

		//Clear Cart
		$this->cart->clear();

		if ($this->customer->isLogged()) {
			$this->data['text_message'] = _l("<p>Your order has been successfully processed!</p><p>You can view your order history by going to the <a href=\"%s\">my account</a> page and by clicking on <a href=\"%s\">history</a>.</p><p>If you have any questions or concerns please feel free to <a href=\"%s\">contact us</a>.</p><p>Thanks for shopping with %s!</p>", $this->url->link('account/account'), $this->url->link('account/order'), $this->url->link('information/contact'), $this->config->get('config_name'));
		} else {
			$this->data['text_message'] = _l("<p>Your order has been successfully processed!</p><p>If you have any questions or concerns please feel free to <a href=\"%s\">contact us</a>.</p><p>Thanks for shopping with %s!</p>", $this->url->link('information/contact'), $this->config->get('config_name'));
		}

		$this->data['continue'] = $this->url->link('common/home');

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		$this->response->setOutput($this->render());
	}
}
