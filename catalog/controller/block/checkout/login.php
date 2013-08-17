<?php
class Catalog_Controller_Block_Checkout_Login extends Controller
{
	public function index()
	{
		$this->template->load('block/checkout/login');
		$this->language->load('block/checkout/login');

		if (isset($_POST['username'])) {
			$this->validate();
		}

		$this->data['guest_checkout'] = ($this->config->get('config_guest_checkout') && !$this->config->get('config_customer_hide_price') && !$this->cart->hasDownload());

		//TODO: do we want to have an isBlock check? Or move this to janrain plugin
		$janrain_args = array(
			'login_redirect' => $this->url->link('checkout/checkout'),
			'display_type'   => 'popup',
			'icon_size'      => 'large'
		);

		$this->data['rpx_login'] = $this->getBlock('widget/janrain', $janrain_args);

		$defaults = array(
			'username' => '',
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		$this->data['validate_login'] = $this->url->link('block/checkout/login/validate');

		$this->data['url_forgotten'] = $this->url->link('account/forgotten');

		$this->response->setOutput($this->render());
	}

	public function validate()
	{
		$this->language->load('block/checkout/login');

		if ($this->customer->login($_POST['username'], $_POST['password'])) {
			$this->message->add('success', $this->_('text_login_success'));
		} else {
			$this->message->add('warning', $this->_('error_login'));
		}

		if (!$this->cart->validate()) {
			$this->url->redirect($this->url->link('cart/cart'));
		}

		$this->url->redirect($this->url->link('checkout/checkout'));
	}
}
