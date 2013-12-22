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

		$defaults = array(
			'username' => '',
		);

		$login_info = array();

		if ($this->request->isPost()) {
			$login_info = $_POST;
		}

		$this->data += $login_info + $defaults;

		$this->data['guest_checkout'] = $this->cart->guestCheckoutAllowed();

		//TODO: Move this to janrain plugin
		$janrain_args = array(
			'login_redirect' => $this->url->link('checkout/checkout'),
			'display_type'   => 'popup',
			'icon_size'      => 'large'
		);

		$this->data['rpx_login'] = $this->getBlock('widget/janrain', $janrain_args);

		//Google Login
		$this->data['gp_login'] = $this->Catalog_Model_Block_Login_Google->getConnectUrl();

		//Facebook Login
		$this->data['fb_login'] = $this->Catalog_Model_Block_Login_Facebook->getConnectUrl();

		$this->data['validate_login'] = $this->url->link('block/checkout/login/validate');

		//Action Buttons
		$this->request->setRedirect($this->url->link('checkout/checkout'));

		$this->data['register'] = $this->url->link("account/register");
		$this->data['forgotten'] = $this->url->link('account/forgotten');

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
			$this->url->redirect('cart/cart');
		}

		$this->url->redirect('checkout/checkout');
	}
}
